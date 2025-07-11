<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://unpkg.com/flowbite@latest/dist/flowbite.min.css" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/web/custom.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

    @yield('style')
</head>

<body class="min-h-screen flex flex-col">
    <div class="flex min-h-screen bg-[#FDF8F4] text-[#1A1A1A] px-[50px] gradient-primary !pt-10">
        <!-- Sidebar -->
        @include('frontend.user.common.sidebar')
        <!-- Main Content -->
        <main class="flex-1 p-6 pe-0 pt-0 h-full">
            <!-- Header -->
            @include('frontend.user.common.header')
            
            @yield('content')
        </main>
    </div>

    <!-- Banner -->
    @yield('ads')

    @include('frontend.include.footer')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true, // Adds the close (×) button
            "progressBar": true, // Shows the loading/progress bar
            "timeOut": "5000", // Auto-close after 5 seconds
            "extendedTimeOut": "1000", // Extra time when hovered
            "positionClass": "toast-top-right", // Position (you can change this)
            "showDuration": "300",
            "hideDuration": "1000",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif

        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        @if (session('info'))
            toastr.info("{{ session('info') }}");
        @endif

        @if (session('warning'))
            toastr.warning("{{ session('warning') }}");
        @endif
    </script>

    @yield('script')
</body>

</html>