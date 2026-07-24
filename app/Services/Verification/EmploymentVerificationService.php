<?php

namespace App\Services\Verification;

use App\Models\OrderItem;
use App\Models\EmploymentVerification;
use App\Models\CandidateServiceData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EmploymentVerificationService extends BaseVerificationService
{
    protected function performVerification(OrderItem $OrderItem): void
    {
        if ($this->isTestMode()) {
            Log::info("Simulating Employment Verification for candidate ID {$OrderItem->candidate_id}");
            usleep(100000);
        } else {
            Log::info("Actual Employment Verification for candidate ID {$OrderItem->candidate_id}");

            $serviceData = CandidateServiceData::with('field')
                ->where('order_item_id', $OrderItem->id)
                ->get();

            $formattedData = [];
            $companyEmail = 'hr@example.com';

            foreach ($serviceData as $data) {
                $fieldName = $data->field ? $data->field->name : 'Unknown Field';
                $formattedData[$fieldName] = $data->field_value;

                if (str_contains(strtolower($fieldName), 'email') || filter_var($data->field_value, FILTER_VALIDATE_EMAIL)) {
                    if ($companyEmail === 'hr@example.com') {
                        $companyEmail = $data->field_value;
                    }
                }
            }

            $token = Str::random(40);

            EmploymentVerification::create([
                'order_item_id' => $OrderItem->id,
                'token' => $token,
                'company_email' => $companyEmail,
                'candidate_data' => $formattedData,
                'status' => 'pending',
            ]);

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

            $OrderItem->status = 'ON_HOLD';
            $OrderItem->save();

            Log::info("Sent employment verification email to {$companyEmail} for candidate {$OrderItem->candidate_id}");
        }
    }
}
