<?php
namespace App\Http\Controllers\Api\Client\Candidate;

use App\Enums\CandidateStatus;
use App\Http\Controllers\Api\Client\BaseController;
use App\Http\Requests\Api\Client\Candidate\StoreCandidateImportRequest;
use App\Http\Requests\Api\Client\Candidate\StoreCandidateRequest;
use App\Models\CandidateImportHistory;
use App\Services\CandidateService;
use App\Services\Webhook\ClientWebhookDispatcher;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use ZipArchive;

class CandidatesController extends BaseController
{
    use ApiResponse;
    protected CandidateService $candidateService;
    protected ClientWebhookDispatcher $clientWebhookDispatcher;

    public function __construct(CandidateService $candidateService, ClientWebhookDispatcher $clientWebhookDispatcher)
    {
        $this->candidateService = $candidateService;
        $this->clientWebhookDispatcher = $clientWebhookDispatcher;
    }

    public function index(Request $request)
    {
        $query = $this->candidateService->query();

        $user = $request->user('api') ?? $request->user();
        if ($user && isset($user->client_id) && $user->client_id !== null) {
            $query->where($this->candidateService->clientId(), $user->client_id);
        }

        $result = $this->candidateService->datatable(
            query: $query,
            params: $request->all(),
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

        $statusList = array_map(
            static fn (CandidateStatus $status): array => [
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

        return $this->success('message', $result);
    }

    public function store(StoreCandidateRequest $request)
    {
        $user = Auth::user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        try {
            $created = $this->candidateService->createWithAssociations(
                $request->validated(),
                $clientId,
                $request->ip_address
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

            return $this->success('Candidate added successfully.', $created, 201);
        } catch (ValidationException $e) {
            return $this->validationError($e->errors(), 'Validation failed.');
        } catch (\Throwable $e) {
            Log::error('Candidate create failed', [
                'error' => $e->getMessage(),
                'payload' => $request->all(),
            ]);

            return $this->error('Failed to create candidate.', 500);
        }
    }

    public function importSample(Request $request): Response
    {
        $format = strtolower((string) $request->query('format', 'csv'));
        if (!in_array($format, ['csv', 'xlsx'], true)) {
            return $this->error('Invalid format. Allowed formats: csv, xlsx.', 422);
        }

        $headers = [
            'firstName',
            'lastName',
            'email',
            'dialCode',
            'phoneNumber',
            'country',
            'state',
            'city',
            'address',
            'postcode',
            'managerEmails',
        ];

        $sampleRows = [[
            'John',
            'Doe',
            'john.doe@example.com',
            '+91',
            '9876543210',
            'India',
            'Maharashtra',
            'Mumbai',
            'Sector 1',
            '110001',
            'manager1@example.com,manager2@example.com',
        ]];

        if ($format === 'csv') {
            $filename = 'candidate-import-sample.csv';
            return $this->downloadResponse(function () use ($headers, $sampleRows): void {
                $stream = fopen('php://output', 'wb');
                fputcsv($stream, $headers);
                foreach ($sampleRows as $row) {
                    fputcsv($stream, $row);
                }
                fclose($stream);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $filename = 'candidate-import-sample.xlsx';
        $binary = $this->buildSimpleXlsx($headers, $sampleRows);

        return $this->downloadResponse(
            static function () use ($binary): void {
                echo $binary;
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    public function import(StoreCandidateImportRequest $request): JsonResponse
    {
        $user = Auth::user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

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

        return $this->success('Candidate import queued successfully.', [
            'import_id' => $import->id,
            'filename' => $import->filename,
            'status' => $import->status,
        ], 201);
    }

    public function imports(Request $request): JsonResponse
    {
        $user = $request->user('api') ?? $request->user();
        $clientId = (int) ($user?->client_id ?? 0);

        if ($clientId <= 0) {
            return $this->error('Client context not found for this user.', 422);
        }

        $imports = CandidateImportHistory::query()
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
            ->values();

        return $this->success('Candidate imports fetched successfully.', $imports);
    }

    protected function buildSimpleXlsx(array $headers, array $rows): string
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
