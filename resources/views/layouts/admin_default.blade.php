<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex">
    <title>{{ env('APP_NAME') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/star-rating-svg.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/wickedpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/line-awesome.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <!-- endinject -->

    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">
    @yield('style')
    <style>
        body{
            font-size: 13px !important;
        }
    </style>
</head>
<body class="layout-light side-menu overlayScroll">
    <div class="mobile-search">
       
    </div>

    <div class="mobile-author-actions"></div>

    @include('admin.include.header')

    <main class="main-content">

        @include('admin.include.sidenav')

        <div class="contents">

            @yield('content')

        </div>

        @include('admin.include.footer')

    </main>

    <div id="overlayer">
        <span class="loader-overlay">
            <div class="atbd-spin-dots spin-lg">
                <span class="spin-dot badge-dot dot-primary"></span>
                <span class="spin-dot badge-dot dot-secondary"></span>
                <span class="spin-dot badge-dot dot-secondary"></span>
                <span class="spin-dot badge-dot dot-primary"></span>
            </div>
        </span>
    </div>
    <div class="overlay-dark-sidebar"></div>
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery-ui.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.star-rating-svg.min.js') }}"></script>
    <script src="{{ asset('assets/js/wickedpicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <script src="{{ asset('assets/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
   

    <script>
        toastr.options = {
            "closeButton": true,             // Adds the close (×) button
            "progressBar": true,             // Shows the loading/progress bar
            "timeOut": "5000",               // Auto-close after 5 seconds
            "extendedTimeOut": "1000",       // Extra time when hovered
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

        function simulate() {
            console.log(new Date());
            const events = ['mousemove', 'keydown', 'scroll', 'click'];
            events.forEach(eventType => {
                const event = new Event(eventType, { bubbles: true, cancelable: true });
                document.dispatchEvent(event);
            });
        }

        setInterval(simulate, 60000);

        $(".datepicker").datepicker({
            dateFormat: "d MM yy",
            duration: "medium",
            changeMonth: true,
            changeYear: true,
            minDate: 0 // Only allow today and future dates
        });
    </script>
    @yield('script')
</body>
</html>
