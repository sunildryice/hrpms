<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <link rel="stylesheet" href="{!! asset('css/app.css') !!}">
    <link rel="stylesheet" href="{{ asset('stylesheets/style.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/bootstrap-print-css/css/bootstrap-print.min.css?ver=') }}?ver={{ now() }} ">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Mukta:wght@200;500;600;800&display=swap" rel="stylesheet">
    <style>
        .print-info {
            font-family: Arial, sans-serif;
        }

        .table {
            white-space: pre-wrap;
        }

        .table> :not(:first-child) {
            border-top: 1px solid currentColor;
        }

        .print-title {
            position: absolute;
            top: 55px;
            left: 50%;
        }

        .logo-img {
            width: 200px;
        }
    </style>
    @yield('page_css')
</head>

<body class="position-relative">
    <div class="container ">
        {{-- <section class="print-info bg-white p-3 position-relative" id="print-info"> --}}
        {{--     <div style="min-height: 91vh;"> --}}
        {{--         @yield('page-content') --}}
        {{--     </div> --}}
        {{--     <div class=" bottom-0 border-top"> --}}
        {{--         <p class="mt-2">This electronic signature/approval is recognised as valid.</p> --}}
        {{--     </div> --}}
        {{-- </section> --}}
        <section class="p-3 bg-white print-info flex-grow-1 d-flex flex-column" id="print-info">
            <div class="flex-grow-1">
                @yield('page-content')
            </div>
            <div class="pt-2 mt-3 border-top">
                <p class="m-0">This electronic signature/approval is recognised as valid.</p>
            </div>
        </section>
    </div>

</body>

</html>
