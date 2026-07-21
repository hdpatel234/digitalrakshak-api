<?php

namespace App\Console\Commands;

use App\Enums\CandidateSource;
use App\Enums\ImportStatus;
use App\Services\CountryService;
use App\Services\StateService;
use App\Services\CityService;
use App\Services\CandidateImportService;
use App\Services\CandiateImportErrorService;
use App\Services\CandidateService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;
use ZipArchive;

class ProcessCandidateImportsCommand extends Command
{
    protected $signature = 'candidates:process-imports {--limit=50}';
    protected $description = 'Process pending candidate imports';

    public function __construct(
        protected CandidateService $candidateService,
        protected CountryService $countryService,
        protected StateService $stateService,
        protected CityService $cityService,
        protected CandidateImportService $candidateImportService,
        protected CandiateImportErrorService $candiateImportErrorService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $imports = $this->candidateImportService->query()
            ->whereIn($this->candidateImportService->status(), [ImportStatus::PENDING->value, ImportStatus::QUEUED->value])
            ->orderBy($this->candidateImportService->id())
            ->limit($limit)
            ->get();

        foreach ($imports as $import) {
            $this->processImport($import);
        }

        $this->info('Candidate import processing completed.');

        return self::SUCCESS;
    }

    protected function processImport($import): void
    {
        $meta = $this->decodeMeta($import->{$this->candidateImportService->jsonData()});
        $storedPath = $meta['stored_path'] ?? null;

        if (!is_string($storedPath) || $storedPath === '') {
            $this->markImportAsFailed($import, $meta, 'Stored file path not found.');
            return;
        }

        if (!Storage::exists($storedPath)) {
            $this->markImportAsFailed($import, $meta, 'Stored file does not exist.');
            return;
        }

        $import->update([
            $this->candidateImportService->status() => ImportStatus::PROCESSING->value,
            $this->candidateImportService->reason() => null,
        ]);

        try {
            $rows = $this->readRows($storedPath);
            if (count($rows) < 2) {
                $this->markImportAsFailed($import, $meta, 'The uploaded file has no data rows.');
                return;
            }

            $headers = $this->normalizeHeaders($rows[0]);
            $successCount = 0;
            $failedCount = 0;
            $totalRecords = count($rows) - 1;
            $firstFailureReason = null;

            for ($i = 1; $i < count($rows); $i++) {
                $rowNumber = $i + 1;
                $row = $rows[$i];
                $mapped = $this->mapRow($headers, $row);
                $payload = $this->buildPayload($mapped);

                $validator = Validator::make($payload, $this->rowRules((int) $import->client_id));
                if ($validator->fails()) {
                    $failedCount++;
                    $errorMessage = implode('; ', $validator->errors()->all());
                    $firstFailureReason ??= $errorMessage;
                    $this->storeRowError($import->{$this->candidateImportService->id()}, $rowNumber, $errorMessage, $mapped);
                    continue;
                }

                try {
                    $location = $this->resolveLocationIds(
                        $payload['country'] ?? null,
                        $payload['state'] ?? null,
                        $payload['city'] ?? null
                    );

                    $this->candidateService->createWithAssociations(
                        array_merge($payload, $location, [$this->candidateService->source() => CandidateSource::IMPORT_FILE->value]),
                        (int) $import->{$this->candidateImportService->clientId()},
                        null
                    );
                    $successCount++;
                } catch (ValidationException $e) {
                    $failedCount++;
                    $errors = method_exists($e, 'errors') ? $e->errors() : [];
                    $errorText = is_array($errors) && $errors !== []
                        ? implode('; ', array_merge(...array_values($errors)))
                        : $e->getMessage();
                    $firstFailureReason ??= $errorText;
                    $this->storeRowError($import->{$this->candidateImportService->id()}, $rowNumber, $errorText, $mapped);
                } catch (Throwable $e) {
                    $failedCount++;
                    $errorMessage = $e->getMessage();
                    $firstFailureReason ??= $errorMessage;
                    $this->storeRowError($import->{$this->candidateImportService->id()}, $rowNumber, $errorMessage, $mapped);
                }
            }

            $status = $failedCount > 0 ? ImportStatus::FAILED->value : ImportStatus::COMPLETED->value;
            $reason = $failedCount > 0
                ? mb_substr((string) ($firstFailureReason ?? 'Some rows failed during import.'), 0, 1000)
                : null;
            $meta['processed_at'] = now()->toDateTimeString();
            $meta['summary'] = [
                'total' => $totalRecords,
                'successful' => $successCount,
                'failed' => $failedCount,
            ];
            if ($reason !== null) {
                $meta['processing_error'] = $reason;
            } else {
                unset($meta['processing_error']);
            }

            $import->update([
                $this->candidateImportService->totalRecords() => $totalRecords,
                $this->candidateImportService->successfulImports() => $successCount,
                $this->candidateImportService->failedImports() => $failedCount,
                $this->candidateImportService->status() => $status,
                $this->candidateImportService->reason() => $reason,
                $this->candidateImportService->errorLog() => json_encode($meta),
            ]);
        } catch (Throwable $e) {
            Log::error('Candidate import processing failed', [
                'import_id' => $import->{$this->candidateImportService->id()},
                'error' => $e->getMessage(),
            ]);

            $this->markImportAsFailed($import, $meta, $e->getMessage());
        }
    }

    protected function decodeMeta(?string $raw): array
    {
        if (!$raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    protected function markImportAsFailed($import, array $meta, string $reason): void
    {
        $meta['processing_error'] = $reason;
        $meta['processed_at'] = now()->toDateTimeString();

        $import->update([
            $this->candidateImportService->status() => ImportStatus::FAILED->value,
            $this->candidateImportService->reason() => mb_substr($reason, 0, 1000),
            $this->candidateImportService->errorLog() => json_encode($meta),
        ]);
    }

    protected function storeRowError(int $importId, int $rowNumber, string $message, array $rawData): void
    {
        $this->candiateImportErrorService->query()->create([
            $this->candiateImportErrorService->importId() => $importId,
            $this->candiateImportErrorService->rowNumber() => $rowNumber,
            $this->candiateImportErrorService->errorMessage() => mb_substr($message, 0, 2000),
            $this->candiateImportErrorService->rawData() => json_encode($rawData),
            $this->candiateImportErrorService->status() => ImportStatus::FAILED->value,
        ]);
    }

    protected function rowRules(int $clientId): array
    {
        return [
            'firstName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['nullable', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('candidates', 'email')->where(
                    fn($query) => $query->where('client_id', $clientId)
                ),
            ],
            'dialCode' => ['nullable', 'string', 'max:10'],
            'phoneNumber' => ['nullable', 'string', 'max:25'],
            'country' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:1000'],
            'postcode' => ['nullable', 'string', 'max:20'],
            'managerEmails' => ['nullable', 'array'],
            'managerEmails.*' => ['nullable', 'email', 'max:255'],
            'ip_address' => ['nullable', 'string'],
        ];
    }

    protected function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $value = is_string($header) ? trim($header) : '';
            return strtolower($value);
        }, $headers);
    }

    protected function mapRow(array $headers, array $row): array
    {
        $mapped = [];
        foreach ($headers as $index => $header) {
            if ($header === '') {
                continue;
            }
            $mapped[$header] = isset($row[$index]) ? trim((string) $row[$index]) : null;
        }

        return $mapped;
    }

    protected function buildPayload(array $mapped): array
    {
        $managerEmails = [];
        if (!empty($mapped['manageremails'])) {
            $managerEmails = preg_split('/,/', (string) $mapped['manageremails']) ?: [];
            $managerEmails = array_values(array_filter(array_map(
                static fn($value) => strtolower(trim((string) $value)),
                $managerEmails
            )));
        }

        return [
            'firstName' => $mapped['firstname'] ?? null,
            'lastName' => $mapped['lastname'] ?? null,
            'email' => $mapped['email'] ?? null,
            'dialCode' => $mapped['dialcode'] ?? null,
            'phoneNumber' => $mapped['phonenumber'] ?? null,
            'country' => $mapped['country'] ?? null,
            'state' => $mapped['state'] ?? null,
            'city' => $mapped['city'] ?? null,
            'address' => $mapped['address'] ?? null,
            'postcode' => $mapped['postcode'] ?? null,
            'managerEmails' => $managerEmails,
        ];
    }

    protected function resolveLocationIds(?string $countryName, ?string $stateName, ?string $cityName): array
    {
        $country = null;
        $state = null;
        $city = null;

        $countrySearch = $this->normalizedLocationName($countryName);
        $stateSearch = $this->normalizedLocationName($stateName);
        $citySearch = $this->normalizedLocationName($cityName);

        if ($countrySearch !== null) {
            $country = $this->countryService->query()
                ->whereRaw('LOWER(' . $this->countryService->name() . ') = ?', [$countrySearch])
                ->first();
        }

        if ($stateSearch !== null) {
            $stateQuery = $this->stateService->query()
                ->whereRaw('LOWER(' . $this->stateService->name() . ') = ?', [$stateSearch]);

            if ($country) {
                $stateQuery->where($this->stateService->countryId(), $country->{$this->countryService->id()});
            }

            $state = $stateQuery->first();
        }

        if ($citySearch !== null) {
            $cityQuery = $this->cityService->query()
                ->whereRaw('LOWER(' . $this->cityService->name() . ') = ?', [$citySearch]);

            if ($country) {
                $cityQuery->where($this->cityService->countryId(), $country->{$this->countryService->id()});
            }

            if ($state) {
                $cityQuery->where($this->cityService->stateId(), $state->{$this->stateService->id()});
            }

            $city = $cityQuery->first();
        }

        return [
            'country' => $country ? (int) $country->{$this->countryService->id()} : null,
            'state' => $state ? (int) $state->{$this->stateService->id()} : null,
            'city' => $city ? (int) $city->{$this->cityService->id()} : null,
        ];
    }

    protected function normalizedLocationName(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : mb_strtolower($value);
    }

    protected function readRows(string $storedPath): array
    {
        $extension = strtolower(pathinfo($storedPath, PATHINFO_EXTENSION));

        if ($extension === 'xlsx') {
            return $this->readXlsxRows($storedPath);
        }

        return $this->readCsvRows($storedPath);
    }

    protected function readCsvRows(string $storedPath): array
    {
        $rows = [];
        $absolutePath = Storage::path($storedPath);
        $handle = fopen($absolutePath, 'rb');

        if ($handle === false) {
            return $rows;
        }

        while (($data = fgetcsv($handle)) !== false) {
            if (isset($data[0])) {
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $data[0]);
            }
            $rows[] = $data;
        }

        fclose($handle);

        return $rows;
    }

    protected function readXlsxRows(string $storedPath): array
    {
        $rows = [];
        $zip = new ZipArchive();
        $openResult = $zip->open(Storage::path($storedPath));

        if ($openResult !== true) {
            return $rows;
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheetXml === false) {
            $zip->close();
            return $rows;
        }

        $sharedStrings = [];
        $sharedXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($sharedXml !== false) {
            $sharedDoc = simplexml_load_string($sharedXml);
            if ($sharedDoc !== false && isset($sharedDoc->si)) {
                foreach ($sharedDoc->si as $item) {
                    if (isset($item->t)) {
                        $sharedStrings[] = (string) $item->t;
                        continue;
                    }

                    $text = '';
                    if (isset($item->r)) {
                        foreach ($item->r as $run) {
                            $text .= (string) ($run->t ?? '');
                        }
                    }
                    $sharedStrings[] = $text;
                }
            }
        }

        $sheetDoc = simplexml_load_string($sheetXml);
        if ($sheetDoc === false) {
            $zip->close();
            return $rows;
        }

        $sheetDoc->registerXPathNamespace('main', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
        $rowNodes = $sheetDoc->xpath('//main:sheetData/main:row') ?: [];

        foreach ($rowNodes as $rowNode) {
            $cells = [];
            $cellNodes = $rowNode->xpath('main:c') ?: [];
            foreach ($cellNodes as $cellNode) {
                $ref = (string) ($cellNode['r'] ?? '');
                $columnLetters = preg_replace('/\d+/', '', $ref);
                $columnIndex = $this->columnIndexFromLetters($columnLetters);
                $type = (string) ($cellNode['t'] ?? '');
                $value = '';

                if ($type === 's') {
                    $sharedIndex = (int) ($cellNode->v ?? -1);
                    $value = $sharedStrings[$sharedIndex] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) ($cellNode->is->t ?? '');
                } else {
                    $value = (string) ($cellNode->v ?? '');
                }

                $cells[$columnIndex] = $value;
            }

            if (empty($cells)) {
                continue;
            }

            ksort($cells);
            $maxIndex = array_key_last($cells);
            $normalizedRow = [];

            for ($i = 0; $i <= $maxIndex; $i++) {
                $normalizedRow[] = $cells[$i] ?? '';
            }

            $rows[] = $normalizedRow;
        }

        $zip->close();

        return $rows;
    }

    protected function columnIndexFromLetters(string $letters): int
    {
        $letters = strtoupper($letters);
        $index = 0;

        for ($i = 0; $i < strlen($letters); $i++) {
            $index = ($index * 26) + (ord($letters[$i]) - 64);
        }

        return max(0, $index - 1);
    }
}
