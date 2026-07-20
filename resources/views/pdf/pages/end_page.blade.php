<div class="page-break"></div>

<div class="end-page-content">
    <h2 class="end-title">END OF REPORT</h2>
    <p class="end-text">
        This concludes the background verification report for {{ $candidate->first_name }} {{ $candidate->last_name }}.
    </p>
    <div style="margin-top: 50px;">
        @include('pdf.partials.header', ['title' => 'THANK YOU', 'marginTop' => '0'])
    </div>
</div>
