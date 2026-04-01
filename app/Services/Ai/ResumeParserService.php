<?php

namespace App\Services\Ai;

use App\Models\AiAccount;
use App\Models\AiApiConfig;
use App\Models\AiPrompt;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser as PdfParser;

class ResumeParserService
{
    protected AiManager $aiManager;

    public function __construct(AiManager $aiManager)
    {
        $this->aiManager = $aiManager;
    }

    public function parseResumeFile(string $tempPath, string $extension, string $originalName, string $promptCode): array
    {
        $extractionResult = $this->extractTextFromFile($tempPath, $extension);
        
        if (!$extractionResult['success']) {
            addErrorLog('Failed to extract readable text from resume', ['details' => $extractionResult['message'] ?? null]);
            return [
                'success' => false,
                'error' => 'Could not extract readable text.',
                'details' => $extractionResult['message'] ?? null,
                'status_code' => 422,
            ];
        }

        $prompt = AiPrompt::query()
            ->where('prompt_code', $promptCode)
            ->whereIn('is_active', [true, 1, '1', 'active'])
            ->orderByDesc('version')
            ->first();

        if (!$prompt) {
            addErrorLog('AI prompt is not configured', ['prompt_code' => $promptCode]);
            return ['success' => false, 'error' => 'AI prompt is not configured.', 'status_code' => 500];
        }

        $providerId = (int) ($prompt->provider_id ?? 0);
        if ($providerId <= 0) {
            $config = AiApiConfig::query()
                ->whereIn('is_active', [true, 1, '1', 'active'])
                ->where(function ($query) {
                    $env = config('app.env', 'production');
                    $query->whereNull('environment')
                          ->orWhere('environment', $env);
                })
                ->orderByDesc('is_default')
                ->orderByDesc('updated_at')
                ->first();
            $providerId = (int) ($config->provider_id ?? 0);
        }

        if ($providerId <= 0) {
            addErrorLog('AI provider is not configured after all fallbacks');
            return ['success' => false, 'error' => 'AI provider is not configured.', 'status_code' => 500];
        }

        $maxRetries = 3;
        $attempts = 0;
        $aiResult = null;
        $lastError = null;
        
        while ($attempts < $maxRetries) {
            $attempts++;

            $account = AiAccount::availableForProvider($providerId)->first();
            
            if (!$account) {
                addWarningLog("No available AI account found for provider", ['provider_id' => $providerId, 'attempt' => $attempts]);
            }
            
            $options = [];
            if ($account) {
                $options['api_key'] = $account->api_key;
            }
            
            $aiResult = $this->aiManager->generateFromPrompt($promptCode, [
                'filename' => $originalName,
                'resume_text' => $extractionResult['text'] ?? '',
            ], $options);

            if ($aiResult['success']) {
                // Success! Increment usage and break
                if ($account) {
                    $account->incrementUsage();
                }
                break;
            } else {
                $lastError = $aiResult['error'] ?? 'AI parsing failed.';
                addErrorLog('AI Parsing attempt failed', ['error' => $lastError, 'attempt' => $attempts, 'account_id' => $account?->id]);
                Log::warning('AI Parsing attempt failed', ['error' => $lastError, 'attempt' => $attempts, 'account_id' => $account?->id]);
                
                $isQuotaError = str_contains(strtolower($lastError), '429') || 
                                str_contains(strtolower($lastError), 'quota') ||
                                str_contains(strtolower($lastError), 'too many requests') ||
                                str_contains(strtolower($lastError), 'exhausted');
                                
                if ($account && $isQuotaError) {
                    $account->markAsExhaustedForToday();
                } elseif (!$account) {
                    break;
                }
            }
        }

        if (!$aiResult || !$aiResult['success']) {
            addErrorLog('AI parsing failed after retries', ['last_error' => $lastError, 'total_attempts' => $attempts]);
            return ['success' => false, 'error' => $lastError ?? 'AI parsing failed after retries.', 'status_code' => 500];
        }

        $payload = $aiResult['data'] ?? [];
        if (!is_array($payload)) {
            $payload = [];
        }

        if (isset($payload['raw']) && is_string($payload['raw'])) {
            $rawText = trim($payload['raw']);
            $jsonText = $rawText;

            if (preg_match('/```(?:json)?\s*(.*?)(?:```|$)/is', $rawText, $matches)) {
                $jsonText = trim($matches[1]);
            } else if (preg_match('/\{.*/is', $rawText, $matches)) {
                $jsonText = trim($matches[0]);
            }

            $openBraces = substr_count($jsonText, '{');
            $closeBraces = substr_count($jsonText, '}');
            
            if ($openBraces > $closeBraces) {
                $jsonText = rtrim($jsonText, " \t\n\r\0\x0B,");
                $jsonText .= str_repeat('}', $openBraces - $closeBraces);
            }

            $decoded = json_decode($jsonText, true);
            if (is_array($decoded)) {
                $payload = array_merge($payload, $decoded);
            }
        }

        addInfoLog('Before Normalizing parsed resume payload', $payload);
        $payload = $this->normalizeResumePayload($payload);

        $payload['_metadata'] = [
            'extraction_method' => $extractionResult['method_used'] ?? null,
            'text_length' => strlen((string) ($extractionResult['text'] ?? '')),
            'parsed_at' => now()->format('Y-m-d H:i:s'),
            'model_used' => data_get($aiResult, 'meta.model_code'),
            'provider' => data_get($aiResult, 'meta.provider_code'),
            'prompt_code' => data_get($aiResult, 'meta.prompt_code'),
            'attempts' => $attempts,
        ];

        return [
            'success' => true,
            'data' => $payload
        ];
    }

    protected function normalizeResumePayload(array $data): array
    {
        $schema = [
            'personalInformation' => [
                'fullName' => null,
                'firstName' => null,
                'middleName' => null,
                'lastName' => null,
                'dateOfBirth' => null,
                'email' => null,
                'phone' => [
                    'countryCode' => null,
                    'number' => null,
                    'full' => null,
                ],
                'website' => null,
                'linkedin' => null,
                'github' => null,
            ],
            'address' => [
                'residential' => [
                    'tehsil' => null,
                    'district' => null,
                    'city' => null,
                    'state' => null,
                    'country' => null,
                    'pincode' => null,
                    'full' => null,
                ],
                'permanent' => [
                    'tehsil' => null,
                    'district' => null,
                    'city' => null,
                    'state' => null,
                    'country' => null,
                    'pincode' => null,
                    'full' => null,
                ],
            ],
            'education' => [
                [
                    'institution' => null,
                    'degree' => null,
                    'fieldOfStudy' => null,
                    'startDate' => [
                        'month' => null,
                        'year' => null,
                        'full' => null,
                    ],
                    'endDate' => [
                        'month' => null,
                        'year' => null,
                        'full' => null,
                    ],
                    'grades' => null,
                    'location' => null,
                ],
            ],
            'workExperience' => [
                [
                    'company' => null,
                    'position' => null,
                    'location' => null,
                    'startDate' => [
                        'month' => null,
                        'year' => null,
                        'full' => null,
                    ],
                    'endDate' => [
                        'month' => null,
                        'year' => null,
                        'full' => null,
                    ],
                    'isCurrentJob' => null,
                    'responsibilities' => [],
                    'achievements' => [],
                    'technologiesUsed' => [],
                ],
            ],
        ];

        return $this->fillSchema($schema, $data);
    }

    protected function fillSchema($schema, $data)
    {
        if (is_array($schema)) {
            $schemaIsList = array_keys($schema) === range(0, count($schema) - 1);

            if ($schemaIsList) {
                $itemSchema = $schema[0] ?? null;
                if (!is_array($data)) {
                    return [];
                }

                if (count($data) > 0 && array_keys($data) !== range(0, count($data) - 1)) {
                    $isSingleItem = true;
                    if (is_array($itemSchema)) {
                        foreach (array_keys($data) as $key) {
                            if (!array_key_exists($key, $itemSchema)) {
                                $isSingleItem = false;
                                break;
                            }
                        }
                    } else {
                        $isSingleItem = false;
                    }

                    if ($isSingleItem) {
                        $data = [$data];
                    } else {
                        $data = array_values($data);
                    }
                }

                return array_map(function ($item) use ($itemSchema) {
                    if ($itemSchema === null) {
                        return $item;
                    }

                    return $this->fillSchema($itemSchema, is_array($item) ? $item : []);
                }, $data);
            }

            $result = [];
            foreach ($schema as $key => $default) {
                $value = is_array($data) && array_key_exists($key, $data) ? $data[$key] : null;
                $result[$key] = $this->fillSchema($default, $value);
            }

            return $result;
        }

        if (is_array($data) || is_object($data)) {
            return $data;
        }

        return $data ?? $schema;
    }

    protected function extractTextFromFile(string $filepath, string $ext): array
    {
        $result = [
            'success' => false,
            'text' => '',
            'message' => '',
            'method_used' => '',
            'methods_tried' => [],
        ];

        if ($ext === 'pdf') {
            $result['methods_tried'][] = 'smalot_pdfparser';
            $smalotResult = $this->extractWithSmalotPdfParser($filepath);
            if ($smalotResult['success']) {
                $result['success'] = true;
                $result['text'] = $smalotResult['text'];
                $result['method_used'] = 'smalot/pdfparser';
                return $result;
            }

            $result['methods_tried'][] = 'pdftotext';
            $pdftotextResult = $this->extractWithPdftotext($filepath);
            if ($pdftotextResult['success']) {
                $result['success'] = true;
                $result['text'] = $pdftotextResult['text'];
                $result['method_used'] = 'pdftotext';
                return $result;
            }

            $result['message'] = 'All PDF extraction methods failed';
        } elseif ($ext === 'docx') {
            $result['methods_tried'][] = 'docx_extraction';
            $docxResult = $this->extractFromDocx($filepath);
            if ($docxResult['success']) {
                $result['success'] = true;
                $result['text'] = $docxResult['text'];
                $result['method_used'] = 'docx_parser';
                return $result;
            }
            $result['message'] = 'Failed to extract text from DOCX';
        } elseif ($ext === 'txt') {
            $result['methods_tried'][] = 'direct_read';
            $content = file_get_contents($filepath);
            if (strlen((string) $content) > 50) {
                $result['success'] = true;
                $result['text'] = (string) $content;
                $result['method_used'] = 'direct_read';
                return $result;
            }
            $result['message'] = 'TXT file is empty';
        } else {
            $result['message'] = 'Unsupported file type';
        }

        return $result;
    }

    protected function extractWithSmalotPdfParser(string $filepath): array
    {
        $result = ['success' => false, 'text' => ''];

        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($filepath);
            $text = $pdf->getText();
            $text = $this->cleanExtractedText($text);

            if (strlen($text) > 100) {
                $result['success'] = true;
                $result['text'] = $text;
            }
        } catch (\Exception $e) {
            Log::warning('Smalot PDF Parser error', ['message' => $e->getMessage()]);
        }

        return $result;
    }

    protected function extractWithPdftotext(string $filepath): array
    {
        $result = ['success' => false, 'text' => ''];

        $pdftotext = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'pdftotext.exe' : 'pdftotext';
        $options = ['-layout -nopgbrk', '-raw', '-table'];

        foreach ($options as $option) {
            $output = [];
            $returnCode = 0;
            $cmd = "$pdftotext $option " . escapeshellarg($filepath) . " - 2>&1";
            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 && !empty($output)) {
                $text = implode("\n", $output);
                $text = $this->cleanExtractedText($text);

                if (strlen($text) > 100) {
                    $result['success'] = true;
                    $result['text'] = $text;
                    return $result;
                }
            }
        }

        return $result;
    }

    protected function extractFromDocx(string $filepath): array
    {
        $result = ['success' => false, 'text' => ''];

        if (!class_exists('ZipArchive')) {
            return $result;
        }

        $zip = new \ZipArchive();
        if ($zip->open($filepath) === true) {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml) {
                $xml = str_replace('</w:p>', "\n", $xml);
                $xml = str_replace('</w:r>', ' ', $xml);
                $xml = strip_tags($xml, '<w:t>');

                preg_match_all('/<w:t[^>]*>(.*?)<\/w:t>/', $xml, $matches);
                if (!empty($matches[1])) {
                    $text = implode(' ', $matches[1]);
                    $text = $this->cleanExtractedText($text);

                    if (strlen($text) > 100) {
                        $result['success'] = true;
                        $result['text'] = $text;
                    }
                }
            }
            $zip->close();
        }

        return $result;
    }

    protected function cleanExtractedText(string $text): string
    {
        $text = preg_replace('/[^\x20-\x7E\x0A\x0D\x09]/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', (string) $text);

        $lines = explode("\n", (string) $text);
        $cleanLines = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if (strlen($line) > 15 || preg_match('/[a-zA-Z]{4,}/', $line)) {
                $cleanLines[] = $line;
            }
        }

        return implode("\n", $cleanLines);
    }
}
