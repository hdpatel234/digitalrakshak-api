<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Candidate Services Report</title>
    @include('pdf.partials.styles')
</head>
<body>
    @include('pdf.partials.footer')

    <div class="content">
        @yield('content')
    </div>
</body>
</html>
