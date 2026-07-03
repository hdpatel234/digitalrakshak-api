<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PdfConfigurationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'config_key' => 'pdf_header_content',
                'config_value' => '<table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <td style="text-align: left; border: none; vertical-align: middle; width: 50%;">
                    <h2 style="margin: 0; color: #4a4a4a; font-size: 18px;">{{ $candidate->client->company_name ?? \'Company\' }} Experience</h2>
                </td>
                <td style="text-align: right; border: none; vertical-align: middle; width: 50%;">
                    <div style="text-align: right;">
                        <span style="color: #0056b3; font-weight: bold; font-size: 14px;">DigitalRakshak</span><br>
                        <span style="color: #0056b3; font-size: 16px; font-weight: bold;">Verification Report</span>
                    </div>
                </td>
            </tr>
        </table>
        
        <div style="text-align: center; margin-top: 20px; position: relative;">
            <hr style="border: none; border-top: 1px solid #d0d7ee; margin: 0;">
            <div style="width: 8px; height: 8px; background-color: #d0d7ee; border-radius: 50%; position: absolute; top: -4px; left: 50%; margin-left: -4px;"></div>
        </div>',
                'description' => 'Header content for Candidate Report PDF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'config_key' => 'pdf_footer_content',
                'config_value' => '<div class="footer-disclaimer">
            <div class="footer-left">
                A Product of {{ $candidate->client->company_name ?? \'DigitalRakshak\' }}<br>Pvt. Ltd.
            </div>
            <div class="footer-icon">
                <span style="color: #4da6ff; font-size: 16px;">&#128302;</span>
            </div>
            <div class="footer-right">
                This document is strictly confidential. Written permission from {{ $candidate->client->company_name ?? \'DigitalRakshak\' }} Pvt. Ltd. is required for any use beyond its intended purpose. Unauthorised use is prohibited.
            </div>
        </div>
        <div class="footer-bottom">
            <div class="page-info">
                <span class="page-number-box"><span class="pagenum"></span></span>
                Background verification report of {{ $candidate->first_name }} {{ $candidate->last_name }}
            </div>
            <div class="page-link">
                <a href="#" style="color: #0056b3; text-decoration: none; border-bottom: 1px solid #0056b3;">Go back to summary</a>
            </div>
        </div>',
                'description' => 'Footer content for Candidate Report PDF',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'config_key' => 'pdf_page_size',
                'config_value' => 'a4',
                'description' => 'PDF Page Size (e.g. a4, letter)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'config_key' => 'pdf_orientation',
                'config_value' => 'portrait',
                'description' => 'PDF Orientation (portrait or landscape)',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        foreach ($configs as $config) {
            \App\Models\Configuration::updateOrCreate(
                ['config_key' => $config['config_key']],
                $config
            );
        }
    }
}
