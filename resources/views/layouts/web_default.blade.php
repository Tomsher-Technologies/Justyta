<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/web/custom.css') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>

    <style>
        .ad-video {
            width: 100%;
            height: 500px;
        }

        @media (max-width: 768px) {
            .ad-video {
                height: 100%;
            }

            .min-h-screen {
                min-height: 50vh;
            }
        }
        /* Target the Select2 control box */
        .select2-container--default .select2-selection--single {
            background-color: #F9F9F9 !important;
            border: 1px solid #D1D5DB !important;
            /* border-gray-300 */
            border-radius: 10px !important;
            padding: 0.875rem 1rem !important;
            /* matches p-3.5 */
            height: auto !important;
            min-height: 48px !important;
            /* consistent with Tailwind input height */
            display: flex !important;
            align-items: center !important;
        }

        /* Remove the default arrow spacing */
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 50% !important;
            transform: translateY(-50%) !important;
            right: 1rem !important;
        }

        /* Style the selected text */
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #1F2937 !important;
            /* text-gray-900 */
            font-size: 0.875rem !important;
            /* text-sm */
            line-height: 1.5 !important;
            padding: 0 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #000;
        }

        .select2-container {
            /* width: 100% !important; */
        }
    </style>
    @yield('style')
</head>

<body class="min-h-screen flex flex-col !m-0 !m-0">
    <div class="lg:flex min-h-screen flex-wrap bg-[#FDF8F4] text-[#1A1A1A] px-[0px] xl:px-[50px] gradient-primary xl:!pt-10">
        <!-- Sidebar -->
        @include('frontend.user.common.sidebar')
        <!-- Main Content -->
        <main class="flex-1 p-4 xl:p-6 xl:pe-0 pt-0 h-full ">
            <!-- Header -->
            @include('frontend.user.common.header')

            @yield('content')
        </main>
    </div>

    <!-- Banner -->
    @yield('ads')

    @include('frontend.include.footer')

    <script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('assets/js/additional-methods.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>

    <script>
        

        $('.select2').select2({
            width: '100%',
            placeholder: "{{ __('frontend.choose_option') }}"
        });

        document.addEventListener("DOMContentLoaded", function() {
            toastr.options = {
                closeButton: true,
                progressBar: true,
                timeOut: "5000",
                extendedTimeOut: "1000",
                positionClass: "toast-top-right",
                showDuration: "300",
                hideDuration: "1000",
                showMethod: "fadeIn",
                hideMethod: "fadeOut"
            };

            @if(session('success'))
            toastr.success("{{ session('success') }}");
            @endif

            @if(session('error'))
            toastr.error("{{ session('error') }}");
            @endif

            @if(session('info'))
            toastr.info("{{ session('info') }}");
            @endif

            @if(session('warning'))
            toastr.warning("{{ session('warning') }}");
            @endif
        });

        $(document).ready(function() {
            $('#search-navbar').on('input', function() {
                let query = $(this).val();
                if (query.length < 2) {
                    $('#search-suggestions').hide();
                    return;
                }

                $.ajax({
                    url: "{{ route('user.search.services') }}",
                    method: 'GET',
                    data: {
                        q: query
                    },
                    success: function(response) {
                        let suggestions = $('#search-suggestions');
                        suggestions.empty();

                        if (response.length > 0) {
                            response.forEach(service => {

                                let url = `/user/service-request/${service.slug}`;

                                if (service.slug === 'online-live-consultancy') {
                                    url = `/user/online-live-consultancy`; // <-- your custom route here
                                }

                                suggestions.append(`
                                    <a href="${url}" 
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    ${service.title}
                                    </a>
                                `);
                            });
                            suggestions.show();
                        } else {
                            suggestions.append(`<div class="px-4 py-2 text-sm text-gray-500">{{ __('frontend.no_result_found') }}</div>`);
                            suggestions.show();
                        }
                    }
                });
            });

            // Optional: hide suggestions when clicking outside
            $(document).click(function(e) {
                if (!$(e.target).closest('#search-navbar, #search-suggestions').length) {
                    $('#search-suggestions').hide();
                }
            });

            
        });
    </script>

    @yield('script')

    <script>
        // document.addEventListener("DOMContentLoaded", function() {
        //     const serverLang = "{{ app()->getLocale() }}";
        //     const localLang = localStorage.getItem('lang');

        //     if (localLang && localLang !== serverLang) {
        //         window.location.href = "{{ url('/lang') }}/" + localLang;
        //     }

            
        // });

        function toggleMute(videoId) {
            const video = document.getElementById(videoId);
            const muteIcon = document.getElementById('muteIcon');
            const unmuteIcon = document.getElementById('unmuteIcon');

            if (!video) return;

            video.muted = !video.muted;

            if (video.muted) {
                muteIcon.classList.remove('hidden');
                unmuteIcon.classList.add('hidden');
            } else {
                muteIcon.classList.add('hidden');
                unmuteIcon.classList.remove('hidden');
            }
        }
    </script>
</body>

</html>