<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candidate Services Report</title>
    <style>
        body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; font-size: 12px; margin: 0; padding: 0; color: #333; }
        .page-break { page-break-after: always; }
        .header-banner { background-color: #38c8c4; color: white; text-align: center; padding: 15px; font-size: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .logo-container { text-align: center; margin-bottom: 20px; margin-top: 20px; }
        .logo-container img { height: 40px; }
        
        /* Candidate Info Box */
        .info-box { border: 1px solid #c2e0e0; margin: 20px 40px; padding: 20px; position: relative; }
        .profile-img-col { width: 150px; text-align: center; float: left; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; border: 3px solid #eee; display: inline-block; overflow: hidden; background-color: #f5f5f5; }
        .profile-name { font-size: 18px; font-weight: bold; margin-top: 10px; }
        
        .details-col { margin-left: 170px; }
        .detail-row { margin-bottom: 15px; overflow: hidden; }
        .detail-item { float: left; width: 45%; margin-right: 5%; }
        .detail-label { color: #888; font-size: 11px; margin-bottom: 2px;}
        .detail-label img { vertical-align: middle; width: 12px; margin-right: 5px; }
        .detail-value { font-size: 13px; font-weight: bold; background-color: #e5e5e5; padding: 8px; margin-top: 5px; min-height: 20px; }
        .detail-value-transparent { font-size: 13px; font-weight: bold; margin-top: 5px; padding: 8px 0; }
        
        /* Status row */
        .status-row { margin: 20px 40px; display: table; width: calc(100% - 80px); }
        .status-box { display: table-cell; border: 1px solid #c2e0e0; font-size: 14px; text-align: center; width: 33%; vertical-align: middle;}
        .status-box .lbl { padding: 10px; display: inline-block; font-weight: bold; background-color: #f9f9f9; }
        .status-box .val { padding: 10px; display: inline-block; font-weight: bold; }
        .bg-success { background-color: #00ff00 !important; color: #000; }
        
        /* Clearfix */
        .clearfix::after { content: ""; clear: both; display: table; }

        /* Document Uploads */
        .doc-uploads { margin: 20px 40px; border: 1px solid #c2e0e0; display: table; width: calc(100% - 80px); }
        .doc-col-header { display: table-cell; width: 20%; background-color: #f5f9fc; padding: 15px; vertical-align: middle; text-align: center; border-right: 1px solid #c2e0e0; }
        .doc-col-header span { font-weight: bold; font-size: 14px; }
        .doc-col-content { display: table-cell; width: 80%; padding: 15px; vertical-align: middle; }
        
        .doc-item { float: left; width: 30%; font-size: 12px; margin-right: 15px;}
        .doc-item-title { font-weight: bold; color: #333; margin-bottom: 5px; }
        .doc-item-subtitle { color: #888; font-size: 11px; line-height: 1.3;}
        
        /* Verifications Table */
        .verifications-wrapper { margin: 20px 40px; }
        .verifications-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .verifications-table th { border-bottom: 1px solid #eee; padding: 12px 10px; text-align: center; font-size: 11px; color: #000; font-weight: bold; }
        .verifications-table th:first-child { text-align: left; }
        .verifications-table td { padding: 12px 10px; border-bottom: 1px solid #f9f9f9; font-size: 12px; text-align: center; }
        .verifications-table td:first-child { text-align: left; font-weight: bold; font-size: 13px; }
        
        .status-badge { background-color: #38c8c4; color: white; padding: 3px 10px; border-radius: 12px; font-size: 10px; display: inline-block; margin-top: 3px; }
        .status-text { color: #00ff00; font-weight: bold; font-size: 14px; }
        .status-icon { color: #38c8c4; margin-right: 8px; font-size: 16px; vertical-align: middle; display: inline-block; width: 20px; text-align: center; }

        /* Report Details Page */
        .report-page { margin: 20px 40px; }
        .report-title-banner { background-color: #38c8c4; color: white; text-align: center; padding: 12px; font-size: 18px; text-transform: uppercase; margin-bottom: 20px; margin-top: 0;}
        .report-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; border: 1px solid #ddd;}
        .report-table th, .report-table td { padding: 10px 12px; border: 1px solid #ddd; text-align: left; font-size: 12px; }
        .report-table th { background-color: #f9f9f9; width: 35%; color: #333; font-weight: bold; text-transform: uppercase; font-size: 11px;}
        .report-result-row td { font-weight: bold; font-size: 13px; }
        .report-result-success { background-color: #00ff00; color: #000; text-transform: uppercase; text-align: left !important; }
        
        .footer { position: fixed; bottom: 0px; left: 0px; right: 0px; height: 60px; background-color: #f4f4f4; border-top: 1px solid #ddd; }
        .footer-disclaimer { font-size: 8px; color: #555; text-align: justify; padding: 15px 40px; margin: 0;}

        @page { margin: 20px 0px 80px 0px; }
        .content { padding: 0px 0px; }
        
        /* Table Headers specific styles */
        .sub-header-row th { background-color: #f2f2f2; font-weight: bold; text-align: left !important; text-transform: uppercase; font-size: 10px; padding: 8px 12px; }

        .image-container { text-align: center; margin-top: 20px; }
        .image-container img { max-width: 100%; max-height: 700px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <div class="footer">
        <div class="footer-disclaimer">
            {!! $footerContent ?? 'This document is the property of DigitalRakshak. If found anywhere, please contact us.' !!}
        </div>
    </div>

    <!-- PAGE 1: VERIFICATIONS SUMMARY -->
    <div class="logo-container">
        <!-- Using a placeholder logo text since actual logo image path is unknown, or we can just style it nicely -->
        <h2 style="color: #333; margin: 0; font-size: 28px;"><span style="color: #38c8c4;">DIGITAL</span>RAKSHAK</h2>
    </div>

    <div class="header-banner">
        VERIFICATIONS SUMMARY
    </div>

    <div class="info-box clearfix">
        <div class="profile-img-col">
            <div class="profile-img">
                <svg viewBox="0 0 100 100" style="width:100%; height:100%; fill:#e0e0e0;"><path d="M50,10 A20,20 0 1,0 70,30 A20,20 0 0,0 50,10 Z M50,55 C25,55 10,75 10,95 L90,95 C90,75 75,55 50,55 Z"/></svg>
            </div>
            <div class="profile-name">{{ $candidate->first_name }} {{ $candidate->last_name }}</div>
        </div>
        
        <div class="details-col">
            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">Staff ID</div>
                    <div class="detail-value">{{ $candidate->id ? 'ID-' . $candidate->id : 'N/A' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Mobile</div>
                    <div class="detail-value-transparent">{{ $candidate->phone ?? 'N/A' }}</div>
                </div>
            </div>
            
            <div class="detail-row">
                <div class="detail-item">
                    <div class="detail-label">Current Address</div>
                    <div class="detail-value">
                        {{ $candidate->address ?? 'N/A' }} 
                        {{ $candidate->city ?? '' }} 
                        {{ $candidate->state ?? '' }} 
                        {{ $candidate->pincode ?? '' }}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Permanent Address</div>
                    <div class="detail-value">
                        {{ $candidate->address ?? 'N/A' }} 
                        {{ $candidate->city ?? '' }} 
                        {{ $candidate->state ?? '' }} 
                        {{ $candidate->pincode ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="status-row clearfix">
        <div class="status-box" style="width: 20%; padding:0;">
            <span class="lbl" style="width:40%; text-align:center;">Status</span>
            <span class="val bg-success" style="width:50%; text-align:center;">SUCCESS</span>
        </div>
        <div class="status-box" style="width: 35%; padding:0; margin-left:2%;">
            <span class="lbl" style="width:45%; text-align:center;">Initiation Date</span>
            <span class="val" style="width:45%; text-align:center;">{{ $candidate->created_at ? $candidate->created_at->format('d M Y') : 'N/A' }}</span>
        </div>
        <div class="status-box" style="width: 35%; padding:0; margin-left:2%;">
            <span class="lbl" style="width:45%; text-align:center;">Completion Date</span>
            <span class="val" style="width:45%; text-align:center;">{{ $candidate->updated_at ? $candidate->updated_at->format('d M Y') : 'N/A' }}</span>
        </div>
    </div>

    <div class="doc-uploads">
        <div class="doc-col-header">
            <span>Uploaded<br>Documents</span>
        </div>
        <div class="doc-col-content clearfix">
            <!-- Mocking document types, could be dynamic if candidate->documents exist -->
            <div class="doc-item">
                <div class="doc-item-title">Education</div>
                <div class="doc-item-subtitle">Graduate</div>
            </div>
            <div class="doc-item">
                <div class="doc-item-title">ID Proof</div>
                <div class="doc-item-subtitle">Aadhaar Card</div>
            </div>
        </div>
    </div>

    <div class="verifications-wrapper">
        <table class="verifications-table">
            <thead>
                <tr>
                    <th>Verifications</th>
                    <th>Created</th>
                    <th>Sufficient</th>
                    <th>Last Updated</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($candidate->candidateServices as $cs)
                <tr>
                    <td>
                        <span class="status-icon">&#9632;</span>
                        {{ $cs->service->name ?? 'Verification Service' }}
                        @if($cs->serviceData && $cs->serviceData->count() > 0)
                            <div style="font-weight:normal; font-size:11px; margin-top:3px; padding-left: 28px; color: #555;">Data recorded</div>
                        @endif
                    </td>
                    <td>{{ $cs->created_at ? $cs->created_at->format('d M Y') : '-' }}</td>
                    <td>{{ $cs->created_at ? $cs->created_at->format('d M Y') : '-' }}</td>
                    <td>
                        {{ $cs->updated_at ? $cs->updated_at->format('d M Y') : '-' }}<br>
                        <span class="status-badge">Completed</span>
                    </td>
                    <td>
                        <span class="status-text">{{ strtoupper($cs->status) }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- DETAIL PAGES -->
    @foreach($candidate->candidateServices as $cs)
    <div class="page-break"></div>
    <div class="logo-container" style="margin-top: 30px;">
        <h2 style="color: #333; margin: 0; font-size: 24px;"><span style="color: #38c8c4;">DIGITAL</span>RAKSHAK</h2>
    </div>
    
    <div class="report-title-banner">
        {{ strtoupper($cs->service->name ?? 'VERIFICATION') }} REPORT
    </div>

    <div class="report-page">
        <table class="report-table">
            <tr>
                <th>NAME OF CANDIDATE</th>
                <td>{{ $candidate->first_name }} {{ $candidate->last_name }}</td>
            </tr>
            <tr>
                <th>MOBILE</th>
                <td>{{ $candidate->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>STAFF ID</th>
                <td>{{ $candidate->id ? 'ID-' . $candidate->id : 'N/A' }}</td>
            </tr>
        </table>
        
        <h3 style="margin-bottom: 10px; font-size: 14px;">Verification Result</h3>
        <table class="report-table">
            @if($cs->serviceData && $cs->serviceData->count() > 0)
                @foreach($cs->serviceData as $data)
                <tr>
                    <th>{{ strtoupper($data->field->name ?? 'Field') }}</th>
                    <td>{{ $data->field_value }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="2" style="text-align: center; color: #777;">No details available for this verification.</td>
                </tr>
            @endif
            
            <tr class="report-result-row">
                <th>RESULT</th>
                <td class="report-result-success">{{ ucfirst($cs->status) }}</td>
            </tr>
            <tr>
                <th>DATE OF VERIFICATION</th>
                <td>{{ $cs->updated_at ? $cs->updated_at->format('d M Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    @endforeach

    <!-- SUPPORTING DOCUMENTS -->
    @if($candidate->documents && $candidate->documents->count() > 0)
        @foreach($candidate->documents as $doc)
        <div class="page-break"></div>
        <div class="logo-container" style="margin-top: 30px;">
            <h2 style="color: #333; margin: 0; font-size: 24px;"><span style="color: #38c8c4;">DIGITAL</span>RAKSHAK</h2>
        </div>
        <div class="report-title-banner">
            SUPPORTING DOCUMENT
        </div>
        
        <div class="report-page">
            <h3 style="text-align: center;">{{ $doc->type ?? 'Document' }}</h3>
            @if($doc->file_path && preg_match('/\.(jpg|jpeg|png)$/i', $doc->file_path))
                <div class="image-container">
                    <img src="{{ storage_path('app/' . $doc->file_path) }}" alt="Supporting Document">
                </div>
            @else
                <p style="text-align: center; color: #777;">[ Document attached: {{ basename($doc->file_path) }} ]</p>
            @endif
        </div>
        @endforeach
    @endif
</body>
</html>
