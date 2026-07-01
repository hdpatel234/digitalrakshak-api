<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceAndFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // 1. Identity Verification - PAN
        $panServiceId = DB::table('services')->insertGetId([
            'service_name' => 'Identity Verification - PAN',
            'service_code' => 'ID_VERIFY_PAN',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('services_fields')->insert([
            ['service_id' => $panServiceId, 'field_name' => 'pan_card_number', 'field_label' => 'PAN Card Number', 'field_type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['service_id' => $panServiceId, 'field_name' => 'pan_card_file', 'field_label' => 'PAN Card File Upload', 'field_type' => 'file', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 2. Identity Verification - Aadhar
        $aadharServiceId = DB::table('services')->insertGetId([
            'service_name' => 'Identity Verification - Aadhar',
            'service_code' => 'ID_VERIFY_AADHAR',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('services_fields')->insert([
            ['service_id' => $aadharServiceId, 'field_name' => 'aadhar_card_number', 'field_label' => 'Aadhar Card Number', 'field_type' => 'text', 'created_at' => $now, 'updated_at' => $now],
            ['service_id' => $aadharServiceId, 'field_name' => 'aadhar_card_file', 'field_label' => 'Aadhar Card File Upload', 'field_type' => 'file', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // 3. Court Verification
        DB::table('services')->insert([
            'service_name' => 'Court Verification',
            'service_code' => 'COURT_VERIFY',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Address Verification (Physical)
        $physicalAddressServiceId = DB::table('services')->insertGetId([
            'service_name' => 'Address Verification (Physical)',
            'service_code' => 'ADDRESS_VERIFY_PHYSICAL',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $addressFields = [
            ['field_name' => 'country', 'field_label' => 'Country', 'field_type' => 'text'],
            ['field_name' => 'state', 'field_label' => 'State', 'field_type' => 'text'],
            ['field_name' => 'city', 'field_label' => 'City', 'field_type' => 'text'],
            ['field_name' => 'pincode', 'field_label' => 'Pincode', 'field_type' => 'text'],
            ['field_name' => 'address_type', 'field_label' => 'Address Type (Permanent / Temp)', 'field_type' => 'select'],
            ['field_name' => 'residence_type', 'field_label' => 'Residence Type (Own / Parents)', 'field_type' => 'select'],
            ['field_name' => 'period_of_stay', 'field_label' => 'Period of Stay (in years)', 'field_type' => 'number'],
        ];

        foreach ($addressFields as $field) {
            DB::table('services_fields')->insert([
                'service_id' => $physicalAddressServiceId,
                'field_name' => $field['field_name'],
                'field_label' => $field['field_label'],
                'field_type' => $field['field_type'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 5. Address Verification (Digital)
        $digitalAddressServiceId = DB::table('services')->insertGetId([
            'service_name' => 'Address Verification (Digital)',
            'service_code' => 'ADDRESS_VERIFY_DIGITAL',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        foreach ($addressFields as $field) {
            DB::table('services_fields')->insert([
                'service_id' => $digitalAddressServiceId,
                'field_name' => $field['field_name'],
                'field_label' => $field['field_label'],
                'field_type' => $field['field_type'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 6. Education Verification
        $educationServiceId = DB::table('services')->insertGetId([
            'service_name' => 'Education Verification',
            'service_code' => 'EDU_VERIFY',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $eduFields = [
            ['field_name' => 'institute', 'field_label' => 'Institute', 'field_type' => 'text'],
            ['field_name' => 'course_type', 'field_label' => 'Course Type', 'field_type' => 'text'],
            ['field_name' => 'course_name', 'field_label' => 'Course Name', 'field_type' => 'text'],
            ['field_name' => 'specialization', 'field_label' => 'Specialization', 'field_type' => 'text'],
            ['field_name' => 'attendance_type', 'field_label' => 'Attendance Type', 'field_type' => 'text'],
            ['field_name' => 'university', 'field_label' => 'University', 'field_type' => 'text'],
            ['field_name' => 'enrollment_number', 'field_label' => 'Enrollment Number', 'field_type' => 'text'],
            ['field_name' => 'course_start_date', 'field_label' => 'Course Start Date', 'field_type' => 'date'],
            ['field_name' => 'course_end_date', 'field_label' => 'Course End Date (or still in)', 'field_type' => 'date'],
        ];

        foreach ($eduFields as $field) {
            DB::table('services_fields')->insert([
                'service_id' => $educationServiceId,
                'field_name' => $field['field_name'],
                'field_label' => $field['field_label'],
                'field_type' => $field['field_type'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
