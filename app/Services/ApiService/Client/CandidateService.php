<?php

namespace App\Services\ApiService\Client;

use App\Enums\CandidateStatus;
use App\Models\CandidateImportHistory;
use App\Services\BaseService;
use App\Services\CandidateService as CoreCandidateService;
use App\Services\CityService;
use App\Services\CountryService;
use App\Services\StateService;
use App\Services\Webhook\ClientWebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class CandidateService extends BaseService
{
    public function __construct(
        protected CoreCandidateService $candidateService,
        protected CountryService $countryService,
        protected StateService $stateService,
        protected CityService $cityService,
        protected ClientWebhookDispatcher $clientWebhookDispatcher
    ) {}

    public function getCandidates(array $params, ?object $user): array
    {
        $query = $this->candidateService->query()->with(['packages', 'candidateServices']);

        if ($user && isset($user->client_id) && $user->client_id !== null) {
            $query->where($this->candidateService->clientId(), $user->client_id);
        }

        if (!empty($params['package_id'])) {
            $query->whereHas('packages', function ($q) use ($params) {
                $q->where('candidate_packages.package_id', $params['package_id']);
            });
        }

        $result = $this->candidateService->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $this->candidateService->firstName(),
                    $this->candidateService->lastName(),
                    $this->candidateService->email(),
                    $this->candidateService->phone(),
                    $this->candidateService->alternatePhone(),
                    $this->candidateService->city(),
                    $this->candidateService->state(),
                    $this->candidateService->country(),
                    $this->candidateService->source(),
                ],
                'status_column' => $this->candidateService->status(),
                'date_column' => $this->candidateService->createdAt(),
                'allowed_filters' => [
                    'client_id' => $this->candidateService->clientId(),
                    'source' => $this->candidateService->source(),
                    'gender' => $this->candidateService->gender(),
                    'country' => $this->candidateService->country(),
                    'state' => $this->candidateService->state(),
                    'city' => $this->candidateService->city(),
                ],
                'allowed_sorts' => [
                    $this->candidateService->id(),
                    $this->candidateService->firstName(),
                    $this->candidateService->lastName(),
                    $this->candidateService->email(),
                    $this->candidateService->status(),
                    $this->candidateService->source(),
                    $this->candidateService->createdAt(),
                    $this->candidateService->updatedAt(),
                ],
                'default_sort_by' => $this->candidateService->createdAt(),
                'default_sort_direction' => 'desc',
                'default_per_page' => 10,
                'max_per_page' => 100,
            ]
        );

        if (is_array($result) && isset($result['list']) && is_array($result['list'])) {
            $list = collect($result['list'])
                ->map(static fn($item) => is_array($item) ? $item : $item->toArray())
                ->values();

            $missingCountryIds = $list
                ->filter(fn($row) => empty($row[$this->candidateService->country()] ?? null) && !empty($row[$this->candidateService->countryId()] ?? null))
                ->pluck($this->candidateService->countryId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $missingStateIds = $list
                ->filter(fn($row) => empty($row[$this->candidateService->state()] ?? null) && !empty($row[$this->candidateService->stateId()] ?? null))
                ->pluck($this->candidateService->stateId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $missingCityIds = $list
                ->filter(fn($row) => empty($row[$this->candidateService->city()] ?? null) && !empty($row[$this->candidateService->cityId()] ?? null))
                ->pluck($this->candidateService->cityId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $countryNamesById = $missingCountryIds === []
                ? collect()
                : $this->countryService->query()
                ->whereIn($this->countryService->id(), $missingCountryIds)
                ->get()
                ->pluck($this->countryService->name(), $this->countryService->id());

            $stateNamesById = $missingStateIds === []
                ? collect()
                : $this->stateService->query()
                ->whereIn($this->stateService->id(), $missingStateIds)
                ->get()
                ->pluck($this->stateService->name(), $this->stateService->id());

            $cityNamesById = $missingCityIds === []
                ? collect()
                : $this->cityService->query()
                ->whereIn($this->cityService->id(), $missingCityIds)
                ->get()
                ->pluck($this->cityService->name(), $this->cityService->id());

            $result['list'] = $list
                ->map(function (array $row) use ($countryNamesById, $stateNamesById, $cityNamesById) {
                    if (empty($row[$this->candidateService->country()] ?? null)) {
                        $countryId = (int) ($row[$this->candidateService->countryId()] ?? 0);
                        if ($countryId > 0) {
                            $row[$this->candidateService->country()] = $countryNamesById->get($countryId);
                        }
                    }

                    if (empty($row[$this->candidateService->state()] ?? null)) {
                        $stateId = (int) ($row[$this->candidateService->stateId()] ?? 0);
                        if ($stateId > 0) {
                            $row[$this->candidateService->state()] = $stateNamesById->get($stateId);
                        }
                    }

                    if (empty($row[$this->candidateService->city()] ?? null)) {
                        $cityId = (int) ($row[$this->candidateService->cityId()] ?? 0);
                        if ($cityId > 0) {
                            $row[$this->candidateService->city()] = $cityNamesById->get($cityId);
                        }
                    }

                    if (!empty($row['packages']) && is_array($row['packages'])) {
                        $row['package_name'] = collect($row['packages'])->pluck('name')->filter()->join(', ') ?: collect($row['packages'])->pluck('package_name')->filter()->join(', ');
                    }

                    $progress = 0;
                    if (!empty($row['candidate_services']) && is_array($row['candidate_services'])) {
                        $services = collect($row['candidate_services']);
                        $total = $services->count();
                        if ($total > 0) {
                            $completed = $services->filter(function($s) {
                                $status = strtolower(trim((string) ($s['status'] ?? '')));
                                $pStatus = strtolower(trim((string) ($s['processing_status'] ?? '')));
                                $completedStatuses = ['completed', 'verified', 'approved', 'success'];
                                return in_array($status, $completedStatuses, true) || in_array($pStatus, $completedStatuses, true);
                            })->count();
                            $progress = (int) round(($completed / $total) * 100);
                        }
                    }
                    $row['progress'] = $progress;

                    return $row;
                })
                ->values()
                ->all();
        }

        $statusList = array_map(
            static fn(CandidateStatus $status): array => [
                'key' => $status->value,
                'name' => ucwords(str_replace('_', ' ', $status->value)),
            ],
            CandidateStatus::cases()
        );

        if (is_array($result)) {
            $result['status_list'] = $statusList;
        } else {
            $result = [
                'items' => $result,
                'status_list' => $statusList,
            ];
        }

        return $result;
    }

    public function storeCandidate(array $validatedData, int $clientId, ?string $ipAddress): array
    {
        $created = $this->candidateService->createWithAssociations(
            $validatedData,
            $clientId,
            $ipAddress
        );

        $candidate = $created['candidate'] ?? null;

        $this->clientWebhookDispatcher->dispatchForClient(
            $clientId,
            ClientWebhookDispatcher::EVENT_CANDIDATE_CREATED,
            [
                'candidate' => $candidate ? $candidate->toArray() : null,
                'manager_emails' => $created['manager_emails'] ?? [],
                'source' => $candidate?->source,
            ],
            [
                'triggered_by' => 'candidate.store',
            ]
        );

        return $created;
    }

    public function importCandidates(Request $request, int $clientId, ?object $user): array
    {
        $file = $request->file('file');
        $storedPath = $file->store('candidate-imports');

        $import = CandidateImportHistory::query()->create([
            'client_id' => $clientId,
            'filename' => $file->getClientOriginalName(),
            'total_records' => 0,
            'successful_imports' => 0,
            'failed_imports' => 0,
            'imported_by' => $user?->id,
            'status' => 'pending',
            'error_log' => json_encode([
                'stored_path' => $storedPath,
                'original_name' => $file->getClientOriginalName(),
                'uploaded_at' => now()->toDateTimeString(),
            ]),
        ]);

        $this->clientWebhookDispatcher->dispatchForClient(
            $clientId,
            ClientWebhookDispatcher::EVENT_CANDIDATE_IMPORT_QUEUED,
            [
                'import_id' => $import->id,
                'filename' => $import->filename,
                'status' => $import->status,
                'uploaded_by' => $user?->id,
            ],
            [
                'triggered_by' => 'candidate.import',
            ]
        );

        return [
            'import_id' => $import->id,
            'filename' => $import->filename,
            'status' => $import->status,
        ];
    }

    public function getImportHistory(int $clientId): array
    {
        return CandidateImportHistory::query()
            ->where('client_id', $clientId)
            ->orderByDesc('id')
            ->get()
            ->map(function (CandidateImportHistory $import): array {
                $meta = [];
                if (!empty($import->error_log)) {
                    $decoded = json_decode($import->error_log, true);
                    if (is_array($decoded)) {
                        $meta = $decoded;
                    }
                }

                return [
                    'id' => $import->id,
                    'file_name' => $meta['original_name'] ?? $import->filename,
                    'total_candidates' => (int) ($import->total_records ?? 0),
                    'successful_candidates' => (int) ($import->successful_imports ?? 0),
                    'failed_candidates' => (int) ($import->failed_imports ?? 0),
                    'status' => (string) $import->status,
                    'failed_reason' => $meta['processing_error'] ?? null,
                    'created_at' => $import->created_at,
                ];
            })
            ->values()
            ->all();
    }

    public function buildSimpleXlsx(array $headers, array $rows): string
    {
        $tmpPath = storage_path('app/' . Str::uuid() . '.xlsx');
        $zip = new ZipArchive();
        $zip->open($tmpPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', $this->xlsxContentTypesXml());
        $zip->addEmptyDir('_rels');
        $zip->addFromString('_rels/.rels', $this->xlsxRootRelsXml());
        $zip->addEmptyDir('xl');
        $zip->addFromString('xl/workbook.xml', $this->xlsxWorkbookXml());
        $zip->addEmptyDir('xl/_rels');
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->xlsxWorkbookRelsXml());
        $zip->addEmptyDir('xl/worksheets');
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->xlsxSheetXml($headers, $rows));
        $zip->close();

        $binary = file_get_contents($tmpPath) ?: '';
        @unlink($tmpPath);

        return $binary;
    }

    protected function xlsxContentTypesXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            . '</Types>';
    }

    protected function xlsxRootRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    protected function xlsxWorkbookXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
            . 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets><sheet name="Sample" sheetId="1" r:id="rId1"/></sheets>'
            . '</workbook>';
    }

    protected function xlsxWorkbookRelsXml(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            . '</Relationships>';
    }

    protected function xlsxSheetXml(array $headers, array $rows): string
    {
        $allRows = array_merge([$headers], $rows);
        $xmlRows = '';

        foreach ($allRows as $rowIndex => $row) {
            $excelRow = $rowIndex + 1;
            $xmlRows .= '<row r="' . $excelRow . '">';

            foreach ($row as $colIndex => $cellValue) {
                $colLetters = $this->excelColumnLetters($colIndex + 1);
                $cellRef = $colLetters . $excelRow;
                $escaped = htmlspecialchars((string) $cellValue, ENT_XML1);
                $xmlRows .= '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
            }

            $xmlRows .= '</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            . '<sheetData>' . $xmlRows . '</sheetData>'
            . '</worksheet>';
    }

    protected function excelColumnLetters(int $columnNumber): string
    {
        $letters = '';
        while ($columnNumber > 0) {
            $remainder = ($columnNumber - 1) % 26;
            $letters = chr(65 + $remainder) . $letters;
            $columnNumber = (int) (($columnNumber - 1) / 26);
        }

        return $letters;
    }
}
