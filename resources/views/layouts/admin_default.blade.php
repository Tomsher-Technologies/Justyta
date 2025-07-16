<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? env('APP_NAME') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/fontawesome.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
 
    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
      
    <link rel="stylesheet" href="{{ asset('assets/css/star-rating-svg.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/wickedpicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/line-awesome.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/daterangepicker.css') }}" />
     <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <!-- endinject -->

    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icon-css@4.1.7/css/flag-icons.min.css">
    @yield('style')
    <style>
        body {
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

    @yield('script_first')

    <script src="{{ asset('assets/js/bootstrap/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.star-rating-svg.min.js') }}"></script>
    <script src="{{ asset('assets/js/wickedpicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    {{-- <script src="{{ asset('assets/js/daterangepicker.js') }}"></script> --}}
    <script src="{{ asset('assets/js/bootstrap/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>

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

        function simulate() {
            console.log(new Date());
            const events = ['mousemove', 'keydown', 'scroll', 'click'];
            events.forEach(eventType => {
                const event = new Event(eventType, {
                    bubbles: true,
                    cancelable: true
                });
                document.dispatchEvent(event);
            });
        }

        setInterval(simulate, 60000);

        $(".alldatepicker").datepicker({
            dateFormat: "d MM yy",
            duration: "medium",
            changeMonth: true,
            changeYear: true,
            maxDate: 0,
            yearRange: "c-80:c"
        });

        $(".datepicker").datepicker({
            dateFormat: "d MM yy",
            duration: "medium",
            changeMonth: true,
            changeYear: true,
            minDate: 0 // Only allow today and future dates
        });

        $('.date-range-picker').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            opens: 'left', // or 'right'
            autoUpdateInput: false,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        $('.date-range-picker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
        });

        $('.date-range-picker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        $('.select2').select2({
            width: '100%',
            placeholder: 'Select options'
        });
    </script>
    @yield('script')
</body>

</html>
