<div class="logo-container" style="{{ isset($marginTop) ? 'margin-top: ' . $marginTop . ';' : '' }}">
    <h2 style="color: #333; margin: 0; font-size: {{ $logoFontSize ?? '28px' }};">
        <span style="color: #38c8c4;">DIGITAL</span>RAKSHAK
    </h2>
</div>

<div class="{{ isset($isReport) && $isReport ? 'report-title-banner' : 'header-banner' }}">
    {{ strtoupper($title ?? 'REPORT') }}
</div>