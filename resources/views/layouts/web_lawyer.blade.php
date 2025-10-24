<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    <meta name="zoom-videosdk-key" content="{{ env('ZOOM_SDK_KEY') }}">
    <meta name="zoom-videosdk-secret" content="{{ env('ZOOM_SDK_SECRET') }}">
    <title>{{ $title ?? env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/web/custom.css') }}">


    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.14.0/css/bootstrap.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>





    <style>
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

        /* Zoom VideoSDK container sizing */
        video-player-container {
            display: block;
            width: 80%;
            margin: 0 auto 40px;
        }

        video-player-container>* {
            width: 100%;
            display: block;
            background: #000;
            aspect-ratio: 16/9;
        }

        video-player,
        video,
        canvas {
            width: 100% !important;
            height: auto !important;
        }
    </style>
    @yield('style')
    <!-- Tailwind Animation -->
    <style>
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <div class="flex min-h-screen bg-[#FDF8F4] text-[#1A1A1A] px-[50px] gradient-primary !pt-10">
        <!-- Sidebar -->
        @include('frontend.lawyer.common.sidebar')
        <!-- Main Content -->
        <main class="flex-1 p-6 pe-0 pt-0 h-full">
            <!-- Header -->
            @include('frontend.lawyer.common.header')

            @yield('content')

            <div class="flex flex-row self-center">
                <button id="start-btn" class="bg-blue-500 text-white font-bold py-2 px-4 rounded mb-4 w-64 self-center">
                    Join
                </button>
                <button id="stop-btn"
                    class=" bg-blue-500 text-white font-bold py-2 px-4 rounded mb-4 w-64 self-center">
                    Leave
                </button>
            </div>
            <div class="flex flex-row self-center m-2">
                <button id="toggle-video-btn"
                    class="hidden bg-blue-500 text-white py-2 text-sm px-2 rounded w-48 self-center">
                    Toggle Video
                </button>
            </div>
            <video-player-container></video-player-container>
            <div class="text-center absolute bottom-2 w-full">
                Do not expose your SDK Secret to the client, when using this in production
                please make sure to use a backend service to sign tokens.
            </div>

        </main>
    </div>

    <!-- Banner -->
    @yield('ads')
    <script src="https://source.zoom.us/videosdk/zoom-video-2.18.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsrsasign/10.8.0/jsrsasign-all-min.js"></script>
    @include('frontend.include.footer')


    <script src="/coi-serviceworker.js"></script>
    <script src="{{ asset('assets/js/videosdk-main.js') }}"></script>

    @yield('script')
</body>

</html>
