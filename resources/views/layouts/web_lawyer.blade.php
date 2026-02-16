<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">
    {{-- <meta name="zoom-videosdk-key" content="{{ env('ZOOM_SDK_KEY') }}">
    <meta name="zoom-videosdk-secret" content="{{ env('ZOOM_SDK_SECRET') }}"> --}}
    <title>{{ $title ?? env('APP_NAME') }}</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/web/custom.css') }}">


    {{-- <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.14.0/css/bootstrap.css" /> --}}

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>

    <style>
        .select2-container{
            width: 100% !important;
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

        video-player-container {
            display: block;
            width: 100%;
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

        #remote-video video,
        #local-video video {
            position: absolute !important;
            inset: 0 !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
        }


        

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
    @yield('style')
 
</head>

<body class="min-h-screen flex flex-col !m-0">
    <div class="lg:flex min-h-screen flex-wrap bg-[#FDF8F4] text-[#1A1A1A] px-[0px] xl:px-[50px] gradient-primary xl:!pt-10">
        <!-- Sidebar -->
        @include('frontend.lawyer.common.sidebar')
        <!-- Main Content -->
        <main class="flex-1 p-4 xl:p-6 xl:pe-0 pt-0 h-full ">
            <!-- Header -->
            @include('frontend.lawyer.common.header')

            <div id="waitingMessage" class="">
                @yield('content')
            </div>
            
            <div class="hidden grid grid-cols-1 gap-6" id="video-call-container">
                <div class="bg-white p-4 rounded-[20px] border !border-[#FFE9B1] flex flex-col h-[calc(100vh-150px)] min-h-0">
                    <div  class=" flex flex-col flex-1 items-center space-y-6 bg-gradient-to-b from-gray-50  rounded-2xl w-full  min-h-0">
                        <div class="relative w-full flex-1 max-w-5xl  min-h-0">
                            <!-- Remote Video Large -->
                            <video-player-container id="remote-video"
                                class="relative w-full h-full bg-black rounded-2xl overflow-hidden shadow-[0_8px_30px_rgba(2,6,23,0.3)]">
                                <div id="guest-name" class="absolute  h-10 bottom-3 left-3 bg-black/60 backdrop-blur-sm text-white text-sm font-medium px-3 py-2 rounded-lg pointer-events-none max-w-[120px] truncate">
                                </div>

                                <!-- Local Video Small Floating INSIDE remote video -->
                                <video-player-container id="local-video"
                                    class="absolute z-50 right-4 bottom-4 w-28 md:w-40 lg:w-52 aspect-video bg-black rounded-xl overflow-hidden shadow-[0_6px_18px_rgba(0,0,0,0.45)] border border-white/30 transition-transform transform-gpu hover:scale-[1.03] touch-none" style="position: absolute !important;">
                                    <div id="user-name"
                                        class="absolute  h-10 bottom-1 left-1 bg-black/65 text-white text-xs px-2 py-2 rounded max-w-[90px] truncate">
                                    </div>
                                </video-player-container>
                            </video-player-container>
                        </div>

                    <!-- Controls -->
                    <div class="flex items-center gap-8 bg-white border border-gray-200 drop-shadow-sm px-6 py-4 rounded-full backdrop-blur-sm">
                        <button id="toggle-audio-btn"
                            class="flex flex-col items-center text-gray-600 hover:text-blue-600 transition transform hover:scale-110">
                            <i id="audio-icon" class="fa-solid fa-microphone text-2xl"></i>
                            <span id="audio-label" class="text-xs mt-1 font-medium">{{ __('frontend.mute') }}</span>
                        </button>
                        <button id="toggle-video-btn"
                            class="flex flex-col items-center text-gray-600 hover:text-blue-600 transition transform hover:scale-110">
                            <i id="video-icon" class="fa-solid fa-video text-2xl"></i>
                            <span id="video-label" class="text-xs mt-1 font-medium">{{ __('frontend.video') }}</span>
                        </button>
                        <button id="end-call-btn"
                            class="flex flex-col items-center text-red-600 hover:text-red-800 transition transform hover:scale-110">
                                <i class="fa-solid fa-phone-slash text-2xl"></i>
                                <span class="text-xs mt-1 font-medium">{{ __('frontend.end') }}</span>
                            </button>
                            <div id="call-timer" class="text-gray-700 font-bold text-lg min-w-[80px] text-center">00:00</div>
                            <div id="call-countdown" class="text-sm text-gray-500"></div>

                        </div>
                    </div>
                </div>
            </div>

            <div id="incomingPopup" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl  w-[550px] max-w-[95vw] max-h-[90vh] overflow-y-auto  border-blue-600 animate-fadeIn">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4  bg-[#07683B] rounded-t-2xl text-white">
                        <div class="flex items-center gap-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-300" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m0-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                            </svg>
                            <h3 class="text-lg font-bold">{{ __('frontend.new_online_request') }}</h3>
                        </div>
                        <button id="popupClose" class="text-white hover:text-gray-200 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="p-6 space-y-4 text-gray-700">
                        <div class="text-center mb-2">
                            <h4 class="font-semibold text-lg text-gray-800">{{ __('frontend.user') }}: <span id="callerName"
                                    class="text-blue-600"></span></h4>
                        </div>

                        <div class="grid grid-cols-1 gap-2">
                            <div class="flex justify-between border-b py-2">
                                <span class="font-medium">{{ __('frontend.applicant_type') }}:</span>
                                <span id="applicantType" class="text-gray-900 font-semibold"></span>
                            </div>
                            <div class="flex justify-between border-b py-2">
                                <span class="font-medium">{{ __('frontend.litigation_type') }}:</span>
                                <span id="litigantType" class="text-gray-900 font-semibold"></span>
                            </div>
                            <div class="flex justify-between border-b py-2">
                                <span class="font-medium">{{ __('frontend.emirate') }}:</span>
                                <span id="emirate" class="text-gray-900 font-semibold"></span>
                            </div>
                            <div class="flex justify-between border-b py-2">
                                <span class="font-medium">{{ __('frontend.you_represent') }}:</span>
                                <span id="youRepresent" class="text-gray-900 font-semibold"></span>
                            </div>
                            <div class="flex justify-between border-b py-2">
                                <span class="font-medium">{{ __('frontend.case_type') }}:</span>
                                <span id="caseType" class="text-gray-900 font-semibold"></span>
                            </div>
                            <div class="flex justify-between border-b py-2">
                                <span class="font-medium">{{ __('frontend.case_stage') }}:</span>
                                <span id="caseStage" class="text-gray-900 font-semibold"></span>
                            </div>
                            <div class="flex justify-between border-b py-2">
                                <span class="font-medium">{{ __('frontend.language') }}:</span>
                                <span id="language" class="text-gray-900 font-semibold"></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="font-medium">{{ __('frontend.duration') }}:</span>
                                <span id="duration" class="text-gray-900 font-semibold"></span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 pb-6 flex justify-between gap-3">
                        <button id="acceptBtn"
                            class="flex-1 bg-green-600 hover:bg-green-700 transition text-white font-bold py-3 rounded-xl shadow-md flex items-center justify-center gap-2 text-lg">
                            {{ __('frontend.accept') }}
                        </button>
                        <button id="rejectBtn"
                            class="flex-1 bg-red-600 hover:bg-red-700 transition text-white font-bold py-3 rounded-xl shadow-md flex items-center justify-center gap-2 text-lg">
                            {{ __('frontend.reject') }}
                        </button>
                    </div>
                </div>
            </div>
        </main>
        
    </div>

    <!-- Banner -->
    @yield('ads')

    @include('frontend.include.footer')

    <script src="/coi-serviceworker.js"></script>
    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"/>
    <script src="{{ asset('assets/js/moment.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap/daterangepicker.min.js') }}"></script>
    <script>
        window.isOnlineLawyer = false;
        window.todayActiveSeconds = 0;
        const onlineToggle = document.getElementById('switch-online');
        window.isOnlineLawyer = onlineToggle.checked;

        function syncOnlineStatus() {
            fetch('/lawyer/online-status', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                const isOnline = data.is_online == 1;

                if (onlineToggle.checked !== isOnline) {
                    onlineToggle.checked = isOnline;

                    const label = onlineToggle.closest('label')
                        .querySelector('span');

                    label.textContent = isOnline
                        ? "{{ __('frontend.online') }}"
                        : "{{ __('frontend.offline') }}";
                }
                window.isOnlineLawyer = isOnline;
                window.todayActiveSeconds = parseInt(data.seconds, 10);
            })
            .catch(() => {});
        }
        
        // poll every 10 seconds
        setInterval(syncOnlineStatus, 10000);
    </script>


    <script>
        
        function changeOnlineStatus(status) {
            $.ajax({
                url: "{{ route('lawyer.changeOnlineStatus') }}",
                type: "POST",
                data: {
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    toastr.success(response.message);
                    window.location.reload();
                },
                error: function (xhr) {
                    toastr.error("{{ __('frontend.something_went_wrong') }}");
                }
            });
        }


        window.consultationStatusUpdateUrl = "{{ route('consultation.status.update') }}";
        window.csrfToken = "{{ csrf_token() }}";
        // const ZoomVideo = window.ZoomVideo;

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


            let currentConsultation = null;

            // Polling lawyer assignments
            async function pollLawyer() {
                try {
                    const res = await fetch("{{ route('web.lawyer.poll') }}", {
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await res.json();
                    if (data.status && data.data) {
                        currentConsultation = data.data;
                        document.getElementById('callerName').textContent = currentConsultation.user_name;
                        document.getElementById('applicantType').textContent = currentConsultation.applicant_type;
                        document.getElementById('litigantType').textContent = currentConsultation.litigant_type;
                        document.getElementById('emirate').textContent = currentConsultation.emirate;
                        document.getElementById('youRepresent').textContent = currentConsultation.you_represent;
                        document.getElementById('caseType').textContent = currentConsultation.case_type;
                        document.getElementById('caseStage').textContent = currentConsultation.case_stage;
                        document.getElementById('language').textContent = currentConsultation.language;
                        document.getElementById('duration').textContent = currentConsultation.duration;
                        document.getElementById('incomingPopup').classList.remove('hidden');
                    }else{
                        document.getElementById('incomingPopup').classList.add('hidden');
                        currentConsultation = null;
                    }
                } catch (err) {
                    console.error(err);
                }
            }
            setInterval(pollLawyer, 3000);

            // Close popup
            document.getElementById('popupClose').addEventListener('click', () => {
                document.getElementById('incomingPopup').classList.add('hidden');
                currentConsultation = null;
            });


            // Accept
            document.getElementById('acceptBtn').addEventListener('click', async () => {
                if (!currentConsultation) return;
                const res = await fetch("{{ route('web.lawyer.response') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        consultation_id: currentConsultation.consultation_id,
                        action: 'accept'
                    })
                });
                const data = await res.json();
                if (data.status) {
                    document.getElementById('incomingPopup').classList.add('hidden');

                    consultationId = currentConsultation.consultation_id;
                    
                    document.getElementById('waitingMessage').classList.add('hidden');
                    document.getElementById('video-call-container').classList.remove('hidden');
                    await startCall(data.data, "{{ addslashes(auth('frontend')->user()->name) }}");

                }else {
                    document.getElementById('incomingPopup').classList.add('hidden');
                    currentConsultation = null;
                    toastr.error(data.message);
                }
            });

            // Reject
            document.getElementById('rejectBtn').addEventListener('click', async () => {
                if (!currentConsultation) return;
                const resReject = await fetch("{{ route('web.lawyer.response') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        consultation_id: currentConsultation.consultation_id,
                        action: 'reject'
                    })
                });
                const dataRej = await resReject.json();
                if (dataRej.status) {
                    document.getElementById('incomingPopup').classList.add('hidden');
                    currentConsultation = null;
                    toastr.success(dataRej.message);
                }else {
                    document.getElementById('incomingPopup').classList.add('hidden');
                    currentConsultation = null;
                    toastr.error(dataRej.message);
                }
            });


        });
    </script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Language Dropdown
    const langBtn = document.getElementById('langDropdownBtn');
    const langDropdown = document.getElementById('langDropdown');
    
    // User Dropdown
    const userBtn = document.getElementById('userDropdownButton');
    const userDropdown = document.getElementById('userDropdown');
    
    // Toggle language dropdown
    if (langBtn) {
        langBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            langDropdown.classList.toggle('hidden');
            if (userDropdown) {
                userDropdown.classList.add('hidden');
            }
        });
    }
    
    // Toggle user dropdown
    if (userBtn) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            if (langDropdown) {
                langDropdown.classList.add('hidden');
            }
        });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        if (langDropdown) {
            langDropdown.classList.add('hidden');
        }
        if (userDropdown) {
            userDropdown.classList.add('hidden');
        }
    });
    
    // Prevent dropdown close when clicking inside
    if (langDropdown) {
        langDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    if (userDropdown) {
        userDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>

    @yield('script')
</body>

</html>
