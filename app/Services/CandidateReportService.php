<?php

namespace App\Services;

use App\Models\Candidate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CandidateReportService
{
    /**
     * Generate a PDF report for a completed candidate.
     *
     * @param Candidate $candidate
     * @return string|false The path to the generated PDF or false on failure.
     */
    public function generateForCandidate(Candidate $candidate)
    {
        try {
            // Eager load necessary relationships
            $candidate->load([
                'candidateServices.service',
                'candidateServices.serviceData.field',
            ]);

            // Generate HTML using a blade view
            $pdf = Pdf::loadView('pdf.candidate_report', ['candidate' => $candidate]);

            // Apply PDF encryption to prevent editing and copying
            $pdf->setEncryption('', '', ['print']);

            $fileName = 'reports/Candidate_' . $candidate->id . '_' . time() . '.pdf';
            
            // Save the file
            Storage::disk('local')->put($fileName, $pdf->output());
            
            // Log successful generation
            Log::info("Generated PDF report for Candidate ID {$candidate->id} at {$fileName}");

            return $fileName;
        } catch (\Exception $e) {
            Log::error("Failed to generate PDF report for Candidate ID {$candidate->id}: " . $e->getMessage());
            return false;
        }
    }
}
