<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candidate Services Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h1, h2, h3 { color: #333; }
        .page-break { page-break-after: always; }
        .candidate-section { margin-bottom: 30px; }
        .service-section { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; }
        .service-title { background-color: #f5f5f5; padding: 5px; margin-top: 0; border-bottom: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #f9f9f9; width: 30%; }
        .header { margin-bottom: 30px; }
        @page { margin: 40px 40px 140px 40px; }
        .footer { position: fixed; bottom: -140px; left: 0px; right: 0px; height: 110px; text-align: left; }
        .footer-disclaimer { background-color: #f5f9fc; padding: 12px; display: table; width: 100%; border-radius: 5px; margin-bottom: 15px; box-sizing: border-box; }
        .footer-left { display: table-cell; width: 30%; vertical-align: middle; font-size: 10px; color: #555; }
        .footer-icon { display: table-cell; width: 5%; vertical-align: middle; text-align: center; }
        .footer-right { display: table-cell; width: 65%; vertical-align: middle; font-size: 9px; color: #777; padding-left: 5px; }
        .footer-bottom { display: table; width: 100%; }
        .page-info { display: table-cell; width: 80%; vertical-align: middle; font-size: 12px; color: #333; }
        .page-link { display: table-cell; width: 20%; vertical-align: middle; text-align: right; font-size: 12px; }
        .page-number-box { display: inline-block; background-color: #f0f4fc; color: #333; font-weight: bold; border-radius: 4px; padding: 4px 10px; font-size: 12px; margin-right: 10px; }
        .pagenum:before { content: counter(page); }
        /* Candidate Details Styles */
        .section-title { background-color: #f0f4fc; color: #333; padding: 10px 15px; font-size: 16px; font-weight: bold; border-radius: 5px; margin-bottom: 15px; }
        .candidate-card { border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .status-badge { display: inline-block; background-color: #e6f4ea; color: #1e8e3e; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; }
        .label-text { color: #777; font-size: 11px; margin-bottom: 3px; }
        .name-text { font-size: 16px; font-weight: bold; color: #333; }
        .package-text { font-size: 11px; color: #555; margin-top: 10px; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 5px 10px; border: none; }
        .info-grid-label { color: #777; font-size: 11px; margin-bottom: 2px; }
        .info-grid-value { color: #333; font-size: 13px; font-weight: bold; }
        .avatar-col { width: 80px; text-align: center; }
        .avatar-circle { width: 60px; height: 60px; border-radius: 50%; border: 1px solid #e0e0e0; display: inline-block; position: relative; overflow: hidden; background-color: #fff; margin-top: 5px; }
        /* Audit Trail Styles */
        .audit-trail-container { margin-top: 40px; }
        .audit-trail-header { margin-bottom: 20px; border-bottom: 1px solid #f0f0f0; padding-bottom: 10px; }
        .audit-trail-title { font-size: 16px; font-weight: bold; color: #111827; margin: 0 0 5px 0; }
        .audit-trail-subtitle { font-size: 11px; color: #6b7280; margin: 0; }
        .timeline { position: relative; padding-left: 20px; margin-top: 15px; }
        .timeline::before { content: ''; position: absolute; left: 8px; top: 0; bottom: 0; width: 2px; background-color: #e5e7eb; }
        .timeline-item { position: relative; margin-bottom: 25px; page-break-inside: avoid; }
        .timeline-icon { position: absolute; left: -26px; top: 0; width: 14px; height: 14px; border-radius: 50%; background-color: #10b981; border: 3px solid #fff; z-index: 1; display: flex; align-items: center; justify-content: center; }
        .timeline-icon::after { content: '\2713'; color: white; font-size: 9px; font-weight: bold; }
        .timeline-content { padding-left: 15px; }
        .timeline-title { font-size: 12px; font-weight: bold; color: #374151; margin: 0 0 3px 0; }
        .timeline-meta { font-size: 10px; color: #9ca3af; margin: 0 0 5px 0; font-family: monospace; }
        .timeline-desc { font-size: 11px; color: #6b7280; margin: 0; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="header">
        {!! \Illuminate\Support\Facades\Blade::render($headerContent, ['candidate' => $candidate]) !!}
    </div>
    
    <div class="footer">
        {!! \Illuminate\Support\Facades\Blade::render($footerContent, ['candidate' => $candidate]) !!}
    </div>

    <div class="candidate-section">
        <div class="section-title">Candidate Details</div>

        <div class="candidate-card">
            <table style="width: 100%; border: none; margin: 0; padding: 0;">
                <tr>
                    <td style="border: none; padding: 0; vertical-align: top;">
                        <div class="label-text">Applicant name</div>
                        <div class="name-text">{{ $candidate->first_name }} {{ $candidate->last_name }}</div>
                    </td>
                    <td style="border: none; padding: 0; text-align: right; vertical-align: top;">
                        <span class="status-badge">&#10003; {{ ucfirst($candidate->status ?? 'Completed') }}</span>
                    </td>
                </tr>
            </table>
            <div class="package-text">
                Package: 
                @if($candidate->candidateServices->isNotEmpty())
                    @foreach($candidate->candidateServices as $index => $candidateService)
                        {{ $candidateService->service->name ?? 'Unknown' }}@if(!$loop->last) + @endif
                    @endforeach
                @else
                    N/A
                @endif
            </div>
        </div>

        <div class="candidate-card">
            <table class="info-grid" style="margin: 0;">
                <tr>
                    <td class="avatar-col" rowspan="2" style="border-right: 1px solid #eee;">
                        <div class="avatar-circle">
                            <div style="width: 20px; height: 20px; background-color: #ccc; border-radius: 50%; margin: 10px auto 3px;"></div>
                            <div style="width: 36px; height: 20px; background-color: #ccc; border-radius: 18px 18px 0 0; margin: 0 auto;"></div>
                        </div>
                    </td>
                    <td style="padding-left: 20px;">
                        <div class="info-grid-label">Phone</div>
                        <div class="info-grid-value">{{ $candidate->phone ?? 'N/A' }}</div>
                    </td>
                    <td>
                        <div class="info-grid-label">Email</div>
                        <div class="info-grid-value">{{ $candidate->email ?? 'N/A' }}</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding-left: 20px;">
                        <div class="info-grid-label">EMP ID</div>
                        <div class="info-grid-value"></div>
                    </td>
                    <td>
                        <div class="info-grid-label">SVID</div>
                        <div class="info-grid-value">{{ $candidate->id }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <div class="section-title" style="margin-top: 30px;">Summary</div>

        @if($candidate->candidateServices->isEmpty())
            <p>No services found for this candidate.</p>
        @else
            @foreach($candidate->candidateServices as $candidateService)
                <div class="service-section">
                    <h3 class="service-title">{{ $candidateService->service->name ?? 'Unknown Service' }}</h3>
                    <p><strong>Status:</strong> {{ ucfirst($candidateService->status) }}</p>
                    
                    @if($candidateService->serviceData && $candidateService->serviceData->isNotEmpty())
                        <table>
                            <tbody>
                                @foreach($candidateService->serviceData as $data)
                                    <tr>
                                        <th>{{ $data->field->name ?? 'Field ID: ' . $data->field_id }}</th>
                                        <td>{{ $data->field_value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No data recorded for this service.</p>
                    @endif
                </div>
            @endforeach
        @endif
        
        @if($candidate->serviceLogs && $candidate->serviceLogs->isNotEmpty())
            <div class="page-break"></div>
            <div class="audit-trail-container">
                <div class="audit-trail-header">
                    <h2 class="audit-trail-title">Audit Trail & Digital Signatures</h2>
                    <p class="audit-trail-subtitle">The immutable block ledger recording every stage of this verification.</p>
                </div>
                
                <div class="timeline">
                    @foreach($candidate->serviceLogs as $log)
                        <div class="timeline-item">
                            <div class="timeline-icon"></div>
                            <div class="timeline-content">
                                <h3 class="timeline-title">{{ $log->title }}</h3>
                                <p class="timeline-meta">{{ $log->description ?? 'System Automated Agent' }} &bull; {{ $log->created_at->format('Y-m-d h:i A') }}</p>
                                <p class="timeline-desc">
                                    @if($log->title === 'Verification Report Cryptographically Sealed')
                                        Generated final background report with cryptographic signature integrity checks passed.
                                    @else
                                        {{ explode(':', $log->title)[1] ?? 'Service' }} verified successfully.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

</body>
</html>
