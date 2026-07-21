<?php

namespace App\Services\Verification;

use App\Models\OrderItem;
use App\Models\EmploymentVerification;
use App\Models\CandidateServiceData;
use App\Mail\EmploymentVerificationMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmploymentVerificationService extends BaseVerificationService
{
    protected function performVerification(OrderItem $OrderItem): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating Employment Verification for candidate ID {$OrderItem->candidate_id}");
            // Sleep slightly to simulate processing
            usleep(100000); // 100ms
        } else {
            Log::info("Actual Employment Verification for candidate ID {$OrderItem->candidate_id}");

            // Gather candidate data for this service
            $serviceData = CandidateServiceData::with('field')
                ->where('order_item_id', $OrderItem->id)
                ->get();

            // Format data and try to find an email
            $formattedData = [];
            $companyEmail = 'hr@example.com'; // Default fallback

            foreach ($serviceData as $data) {
                $fieldName = $data->field ? $data->field->name : 'Unknown Field';
                $formattedData[$fieldName] = $data->field_value;

                // Very naive email detection for the sake of the flow
                if (str_contains(strtolower($fieldName), 'email') || filter_var($data->field_value, FILTER_VALIDATE_EMAIL)) {
                    if ($companyEmail === 'hr@example.com') { // only override if it's the fallback
                        $companyEmail = $data->field_value;
                    }
                }
            }

            // Generate token
            $token = Str::random(40);

            // Create verification record
            $verification = EmploymentVerification::create([
                'order_item_id' => $OrderItem->id,
                'token' => $token,
                'company_email' => $companyEmail,
                'candidate_data' => $formattedData,
                'status' => 'pending',
            ]);

            // Construct verification URL
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            $verificationUrl = rtrim($frontendUrl, '/') . '/verify-employment/' . $token;

            $candidateName = $OrderItem->candidate->first_name . ' ' . $OrderItem->candidate->last_name;

            $subject = 'Employment Verification Request - ' . $candidateName;
            $bodyHtml = view('emails.employment_verification', [
                'verificationUrl' => $verificationUrl,
                'candidateName' => $candidateName,
            ])->render();

            $emailManager = app(\App\Services\Email\EmailManager::class);
            $emailManager->send([
                'to_email' => $companyEmail,
                'subject' => $subject,
                'body_html' => $bodyHtml,
                'candidate_id' => $OrderItem->candidate_id,
                'email_type' => 'verification'
            ]);

            // Mark service as ON_HOLD while waiting for response
            $OrderItem->status = 'ON_HOLD'; // Assuming ON_HOLD is a valid status, if not we can use something else or keep it as PROCESSING. Let's use ON_HOLD.
            $OrderItem->save();

            Log::info("Sent employment verification email to {$companyEmail} for candidate {$OrderItem->candidate_id}");
        }
    }
}
