@extends('pdf.layouts.master')

@section('content')

    <!-- PAGE 1: VERIFICATIONS SUMMARY -->
    @include('pdf.pages.summary')

    <!-- PAGE 2: DISCLAIMER -->
    @include('pdf.pages.disclaimer')

    <!-- PAGE 3: VERIFICATION REPORTS COVER -->
    @include('pdf.pages.reports_cover')

    <!-- DYNAMIC VERIFICATION SERVICE REPORTS -->
    @foreach($candidate->candidateServices as $cs)
        @php
            // Normalize service name to map to a blade template (e.g. "Aadhaar Verification" -> "aadhaar_verification")
            $serviceViewName = 'pdf.services.' . Str::slug($cs->service->name ?? 'default', '_');
        @endphp

        @if(view()->exists($serviceViewName))
            @include($serviceViewName, ['cs' => $cs, 'candidate' => $candidate])
        @else
            @include('pdf.services.default', ['cs' => $cs, 'candidate' => $candidate])
        @endif
    @endforeach

    <!-- FINAL PAGE: CLOSING REMARKS -->
    @include('pdf.pages.end_page')

@endsection
