<!doctype html>
<html>

    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">
        <title>{{ $title ?? env('APP_NAME') }}</title>

        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link href="https://unpkg.com/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />

        <link rel="stylesheet" href="{{ asset('assets/css/web/custom.css') }}">
        @yield('style')
    </head>

    <body class="bg-white">
        @include('web.include.header')
        
        @yield('content')

        @include('web.include.footer')
    
    </body>
    <script src="https://unpkg.com/flowbite@latest/dist/flowbite.min.js"></script>

    @yield('script')
</html>
