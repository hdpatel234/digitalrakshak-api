<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedEmploymentVerificationService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:seed-employment-verification-service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Seeding Employment Verification Service...');

        $category = \App\Models\ServiceCategory::where('category_name', 'Verification')->first();
        if (!$category) {
            $category = \App\Models\ServiceCategory::create([
                'category_name' => 'Verification',
                'category_code' => 'VERIFICATION',
                'category_slug' => 'verification',
                'status' => 1,
                'created_by' => 1
            ]);
        }

        // Check if service already exists
        $service = \App\Models\Service::where(\App\Models\Service::SERVICE_CODE, 'EMP_VER')->first();

        if (!$service) {
            $service = \App\Models\Service::create([
                \App\Models\Service::SERVICE_CATEGORY => $category->id,
                \App\Models\Service::SERVICE_NAME => 'Employment Verification',
                \App\Models\Service::SERVICE_CODE => 'EMP_VER',
                \App\Models\Service::DESCRIPTION => 'Verification of employment history',
                \App\Models\Service::BASE_PRICE => 0,
                \App\Models\Service::STATUS => 1,
                \App\Models\Service::CREATED_BY => 1,
            ]);
            $this->info('Created Service: Employment Verification');
        } else {
            $this->info('Service already exists. Skipping service creation.');
        }

        $fields = [
            [
                'field_name' => 'company_name',
                'field_label' => 'Company Name',
                'field_type' => 'text',
                'is_required' => 1,
                'display_order' => 1,
            ],
            [
                'field_name' => 'employee_id',
                'field_label' => 'Employee ID',
                'field_type' => 'text',
                'is_required' => 0,
                'display_order' => 2,
            ],
            [
                'field_name' => 'designation',
                'field_label' => 'Designation',
                'field_type' => 'text',
                'is_required' => 1,
                'display_order' => 3,
            ],
            [
                'field_name' => 'department',
                'field_label' => 'Department',
                'field_type' => 'text',
                'is_required' => 0,
                'display_order' => 4,
            ],
            [
                'field_name' => 'employment_type',
                'field_label' => 'Employment Type',
                'field_type' => 'text',
                'is_required' => 0,
                'display_order' => 5,
            ],
            [
                'field_name' => 'joining_date',
                'field_label' => 'Joining Date',
                'field_type' => 'date',
                'is_required' => 1,
                'display_order' => 6,
            ],
            [
                'field_name' => 'last_working_date',
                'field_label' => 'Last Working Date',
                'field_type' => 'date',
                'is_required' => 1,
                'display_order' => 7,
            ],
            [
                'field_name' => 'current_status',
                'field_label' => 'Current Status',
                'field_type' => 'text',
                'is_required' => 0,
                'display_order' => 8,
            ],
            [
                'field_name' => 'work_location',
                'field_label' => 'Work Location',
                'field_type' => 'text',
                'is_required' => 0,
                'display_order' => 9,
            ],
            [
                'field_name' => 'reporting_manager_name',
                'field_label' => 'Reporting Manager Name',
                'field_type' => 'text',
                'is_required' => 0,
                'display_order' => 10,
            ],
            [
                'field_name' => 'hr_email',
                'field_label' => 'HR Email',
                'field_type' => 'email',
                'is_required' => 0,
                'display_order' => 11,
            ],
            [
                'field_name' => 'hr_phone',
                'field_label' => 'HR Phone',
                'field_type' => 'text',
                'is_required' => 0,
                'display_order' => 12,
            ],
            [
                'field_name' => 'consent',
                'field_label' => 'Consent',
                'field_type' => 'checkbox',
                'is_required' => 1,
                'display_order' => 13,
            ],
            [
                'field_name' => 'appointment_letter',
                'field_label' => 'Appointment Letter',
                'field_type' => 'file',
                'is_required' => 0,
                'display_order' => 14,
            ],
            [
                'field_name' => 'experience_letter',
                'field_label' => 'Experience Letter',
                'field_type' => 'file',
                'is_required' => 0,
                'display_order' => 15,
            ],
            [
                'field_name' => 'salary_slip',
                'field_label' => 'Salary Slip',
                'field_type' => 'file',
                'is_required' => 0,
                'display_order' => 16,
            ],
            [
                'field_name' => 'relieving_letter',
                'field_label' => 'Relieving Letter',
                'field_type' => 'file',
                'is_required' => 0,
                'display_order' => 17,
            ],
            [
                'field_name' => 'employee_id_card',
                'field_label' => 'Employee ID Card',
                'field_type' => 'file',
                'is_required' => 0,
                'display_order' => 18,
            ]
        ];

        foreach ($fields as $fieldData) {
            $existingField = \App\Models\ServicesField::where(\App\Models\ServicesField::SERVICE_ID, $service->id)
                ->where(\App\Models\ServicesField::FIELD_NAME, $fieldData['field_name'])
                ->first();

            if (!$existingField) {
                \App\Models\ServicesField::create(array_merge($fieldData, [
                    \App\Models\ServicesField::SERVICE_ID => $service->id,
                    \App\Models\ServicesField::STATUS => 1,
                    \App\Models\ServicesField::CREATED_BY => 1,
                ]));
                $this->info("Added field: " . $fieldData['field_name']);
            } else {
                $this->info("Field already exists: " . $fieldData['field_name']);
            }
        }

        $this->info('Seeding completed successfully!');
    }
}
