<div class="page-break"></div>

@include('pdf.partials.header', [
    'title' => strtoupper($cs->service->name ?? 'VERIFICATION') . ' REPORT',
    'isReport' => true
])

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
