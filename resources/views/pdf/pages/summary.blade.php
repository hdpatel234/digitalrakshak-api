@include('pdf.partials.header', ['title' => 'VERIFICATIONS SUMMARY'])

@include('pdf.partials.candidate_info', ['candidate' => $candidate])

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
