<!doctype html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <title>{{ $title ?? env('APP_NAME') }}</title>

        <script src="{{ asset('assets/js/tailwind-index.global.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('assets/css/tailwind-flowbite.min.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/web/custom.css') }}">
        @yield('style')
    </head>

    <body class="bg-white">
        @include('web.include.header')
        
        @yield('content')

        @include('web.include.footer')
    
    </body>
    <script src="{{ asset('assets/js/tailwind-flowbite.min.js') }}"></script>

    @yield('script')
</html>
