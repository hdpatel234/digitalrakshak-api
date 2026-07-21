<?php

namespace App\Services\ApiService\Client;

use App\Enums\BaseStatus;
use App\Enums\CandidateStatus;
use App\Repositories\CandidateRepository;
use App\Services\CandidateService as CoreCandidateService;
use App\Repositories\CityRepository;
use App\Repositories\CountryRepository;
use App\Repositories\StateRepository;
use App\Repositories\CandidateImportHistoryRepository;
use App\Services\Webhook\ClientWebhookDispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ZipArchive;
use App\Services\BaseService;

class CandidateService extends BaseService
{
    public function __construct(
        protected CandidateRepository $candidateRepo,
        protected CoreCandidateService $coreCandidateService,
        protected CountryRepository $countryRepo,
        protected StateRepository $stateRepo,
        protected CityRepository $cityRepo,
        protected CandidateImportHistoryRepository $candidateImportHistoryRepo,
        protected ClientWebhookDispatcher $clientWebhookDispatcher
    ) {}

    public function getCandidates(array $params, ?object $user): array
    {
        $query = $this->candidateRepo->query()->with(['packages', 'candidateRepos']);

        if ($user && isset($user->client_id) && $user->client_id !== null) {
            $query->where($this->candidateRepo->clientId(), $user->client_id);
        }

        if (!empty($params['package_id'])) {
            $query->whereHas('packages', function ($q) use ($params) {
                $q->where('candidate_packages.package_id', $params['package_id']);
            });
        }

        $result = $this->candidateRepo->datatable(
            query: $query,
            params: $params,
            config: [
                'searchable' => [
                    $this->candidateRepo->firstName(),
                    $this->candidateRepo->lastName(),
                    $this->candidateRepo->email(),
                    $this->candidateRepo->phone(),
                    $this->candidateRepo->alternatePhone(),
                    $this->candidateRepo->source(),
                ],
                'status_column' => $this->candidateRepo->status(),
                'date_column' => $this->candidateRepo->createdAt(),
                'allowed_filters' => [
                    'client_id' => $this->candidateRepo->clientId(),
                    'source' => $this->candidateRepo->source(),
                    'gender' => $this->candidateRepo->gender(),
                    'country' => $this->candidateRepo->country(),
                    'state' => $this->candidateRepo->state(),
                    'city' => $this->candidateRepo->city(),
                ],
                'allowed_sorts' => [
                    $this->candidateRepo->id(),
                    $this->candidateRepo->firstName(),
                    $this->candidateRepo->lastName(),
                    $this->candidateRepo->email(),
                    $this->candidateRepo->status(),
                    $this->candidateRepo->source(),
                    $this->candidateRepo->createdAt(),
                    $this->candidateRepo->updatedAt(),
                ],
                'default_sort_by' => $this->candidateRepo->createdAt(),
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
                ->filter(fn($row) => empty($row[$this->candidateRepo->country()] ?? null) && !empty($row[$this->candidateRepo->countryId()] ?? null))
                ->pluck($this->candidateRepo->countryId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $missingStateIds = $list
                ->filter(fn($row) => empty($row[$this->candidateRepo->state()] ?? null) && !empty($row[$this->candidateRepo->stateId()] ?? null))
                ->pluck($this->candidateRepo->stateId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $missingCityIds = $list
                ->filter(fn($row) => empty($row[$this->candidateRepo->city()] ?? null) && !empty($row[$this->candidateRepo->cityId()] ?? null))
                ->pluck($this->candidateRepo->cityId())
                ->map(static fn($id) => (int) $id)
                ->filter(static fn($id) => $id > 0)
                ->unique()
                ->values()
                ->all();

            $countryNamesById = $missingCountryIds === []
                ? collect()
                : $this->countryRepo->query()
                ->whereIn($this->countryRepo->id(), $missingCountryIds)
                ->get()
                ->pluck($this->countryRepo->name(), $this->countryRepo->id());

            $stateNamesById = $missingStateIds === []
                ? collect()
                : $this->stateRepo->query()
                ->whereIn($this->stateRepo->id(), $missingStateIds)
                ->get()
                ->pluck($this->stateRepo->name(), $this->stateRepo->id());

            $cityNamesById = $missingCityIds === []
                ? collect()
                : $this->cityRepo->query()
                ->whereIn($this->cityRepo->id(), $missingCityIds)
                ->get()
                ->pluck($this->cityRepo->name(), $this->cityRepo->id());

            $result['list'] = $list
                ->map(function (array $row) use ($countryNamesById, $stateNamesById, $cityNamesById) {
                    if (empty($row[$this->candidateRepo->country()] ?? null)) {
                        $countryId = (int) ($row[$this->candidateRepo->countryId()] ?? 0);
                        if ($countryId > 0) {
                            $row[$this->candidateRepo->country()] = $countryNamesById->get($countryId);
                        }
                    }

                    if (empty($row[$this->candidateRepo->state()] ?? null)) {
                        $stateId = (int) ($row[$this->candidateRepo->stateId()] ?? 0);
                        if ($stateId > 0) {
                            $row[$this->candidateRepo->state()] = $stateNamesById->get($stateId);
                        }
                    }

                    if (empty($row[$this->candidateRepo->city()] ?? null)) {
                        $cityId = (int) ($row[$this->candidateRepo->cityId()] ?? 0);
                        if ($cityId > 0) {
                            $row[$this->candidateRepo->city()] = $cityNamesById->get($cityId);
                        }
                    }

                    if (!empty($row['packages']) && is_array($row['packages'])) {
                        $row['package_name'] = collect($row['packages'])->pluck('name')->filter()->join(', ') ?: collect($row['packages'])->pluck('package_name')->filter()->join(', ');
                        $row['package_id'] = collect($row['packages'])->pluck('id')->filter()->first();
                    }

                    $progress = 0;
                    if (!empty($row['candidate_services']) && is_array($row['candidate_services'])) {
                        $services = collect($row['candidate_services']);
                        $total = $services->count();
                        if ($total > 0) {
                            $completed = $services->filter(function ($s) {
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
        $created = $this->coreCandidateService->createWithAssociations(
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

        $import = $this->candidateImportHistoryRepo->query()->create([
            $this->candidateImportHistoryRepo->clientId() => $clientId,
            $this->candidateImportHistoryRepo->filename() => $file->getClientOriginalName(),
            $this->candidateImportHistoryRepo->totalRecords() => 0,
            $this->candidateImportHistoryRepo->successfulImports() => 0,
            $this->candidateImportHistoryRepo->failedImports() => 0,
            $this->candidateImportHistoryRepo->importedBy() => $user?->id,
            $this->candidateImportHistoryRepo->status() => BaseStatus::PENDING,
            $this->candidateImportHistoryRepo->errorLog() => json_encode([
                'stored_path' => $storedPath,
                'original_name' => $file->getClientOriginalName(),
                'uploaded_at' => now()->toDateTimeString(),
            ]),
        ]);

        // $this->clientWebhookDispatcher->dispatchForClient(
        //     $clientId,
        //     ClientWebhookDispatcher::EVENT_CANDIDATE_IMPORT_QUEUED,
        //     [
        //         'import_id' => $import->id,
        //         'filename' => $import->filename,
        //         'status' => $import->status,
        //         'uploaded_by' => $user?->id,
        //     ],
        //     [
        //         'triggered_by' => 'candidate.import',
        //     ]
        // );

        return [
            'import_id' => $import->id,
            'filename' => $import->filename,
            'status' => $import->status,
        ];
    }

    public function getImportHistory(int $clientId): array
    {
        return $this->candidateImportHistoryRepo->query()
            ->where('client_id', $clientId)
            ->orderByDesc('id')
            ->get()
            ->map(function ($import): array {
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
