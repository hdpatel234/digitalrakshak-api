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
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .footer { position: fixed; bottom: -30px; left: 0px; right: 0px; height: 30px; text-align: center; line-height: 30px; font-size: 10px; color: #777; }
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
        <h2>Candidate: {{ $candidate->first_name }} {{ $candidate->last_name }}</h2>
        <p><strong>Email:</strong> {{ $candidate->email }}</p>
        <p><strong>Phone:</strong> {{ $candidate->phone }}</p>

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
    </div>

</body>
</html>
