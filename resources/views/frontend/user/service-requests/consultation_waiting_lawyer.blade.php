@extends('layouts.web_default', ['title' => __('frontend.request_success')])

@section('content')
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

    </style>

    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white p-4 rounded-[20px] border !border-[#FFE9B1] flex flex-col h-[calc(100vh-150px)]">

            <!-- Waiting Message -->
            <div id="waitingMessage" class="flex-1 flex items-center justify-center">
                <div class="bg-[#FFFAF0] border border-[#FFE9B1] rounded-xl p-8 max-w-xl w-full text-center">
                    <h1 class="text-2xl font-medium text-[#07683B] mb-4">{{ __('frontend.waiting') }}</h1>
                    <div class="text-gray-600 mb-6">{!! $pageData ?? '' !!}</div>
                    <!-- Spinner -->
                    <button disabled type="button" class="my-8">
                        <svg aria-hidden="true" role="status" class="inline w-8 h-8 me-3 text-white animate-spin" ...></svg>
                    </button>
                    <img src="{{ asset('assets/images/logo-text.svg') }}" alt="{{ __('frontend.logo') }}"
                        class="mx-auto mb-8">
                    <a href="#"
                        class="inline-flex items-center gap-3 px-6 py-3 border border-[#4D1717] text-[#4D1717] rounded-full transition-colors duration-200">
                        <!-- back icon svg --> {{ __('frontend.back_to_home') }}
                    </a>
                </div>
            </div>

            <!-- Video Call Container -->
            <div id="video-call-container" class="hidden flex flex-col flex-1 items-center space-y-6 bg-gradient-to-b from-gray-50  rounded-2xl w-full ">
                <div class="relative w-full flex-1 max-w-5xl bg-black">
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

                        <button id="extend-call-btn" data-consultation-id="{{ $consultation->id }}" data-consultant-type="{{ $consultation->consultant_type }}" class="hidden px-4 py-2 bg-primary text-white rounded">
                            Extend Call
                        </button>
                    </div>
                </div>

                
            </div>


            <!-- Extension Modal -->
            <div id="extendModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-xl w-96 relative">
                    <h3 class="text-lg font-semibold mb-4">Extend Consultation</h3>
                    <input type="hidden" id="modal-consultation-id" value="{{ $consultation->id }}">
                    <input type="hidden" id="modal-consultant-type" value="{{ $consultation->consultant_type }}">

                    <label for="timeslot-select" class="block mb-2 font-medium">Select Duration:</label>
                    <select id="timeslot-select" class="w-full border px-3 py-2 rounded mb-4">
                        <option value="">Select a timeslot</option>
                    </select>

                    <div id="extension-price" class="mb-4">Amount: $0</div>

                    <div class="flex justify-end gap-3">
                        <button id="extension-cancel" class="px-4 py-2 bg-gray-300 rounded">Cancel</button>
                        <button id="extension-pay" class="px-4 py-2 bg-blue-600 text-white rounded">Pay & Extend</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <style>

    </style>
@endsection

@section('script')
    <script src="{{ asset('/coi-serviceworker.js') }}"></script>

    <script>
        window.consultationStatusUpdateUrl = "{{ route('consultation.status.update') }}";
        window.extendTimeSlots = "{{ route('consultation.timeslots') }}";
        window.csrfToken = "{{ csrf_token() }}";
        let consultationId = {{ $consultation->id }};
        let videoFlag = false;
        // Poll server every 4 seconds
        async function pollUser() {
            try {
                const res = await fetch(`{{ route('web.user.check') }}?consultation_id=${consultationId}`, {
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                const data = await res.json();

                if (data.status && data.data && !videoFlag) {
                    videoFlag = true;
                    document.getElementById('waitingMessage').classList.add('hidden');
                    document.getElementById('video-call-container').classList.remove('hidden');
                    
                    await startCall(data.data, '{{ addslashes(auth()->user()->name) }}');
                }
            } catch (err) {
                console.error(err);
            }
        }
        setInterval(pollUser, 4000);

        document.addEventListener("DOMContentLoaded", () => {
            document.getElementById("extend-call-btn").addEventListener("click", () => {
                const btn = document.getElementById("extend-call-btn");
                const consultationId = btn.dataset.consultationId;
                const consultantType = btn.dataset.consultantType;

                extendCall(consultationId, consultantType);
            });

            $("#timeslot-select").on("change", function() {
                const duration = $(this).val();
                const consultationId = $("#modal-consultation-id").val();
                const consultantType = $("#modal-consultant-type").val();

                if (!duration) {
                    $("#extension-price").text("Amount: $0");
                    return;
                }

                $.ajax({
                    url: "{{ route('consultation.price') }}",
                    type: 'GET',
                    data: { consultation_id: consultationId, duration: duration, consultant_type: consultantType },
                    success: function(res) {
                        if (res.status) {
                            $("#extension-price").text("Amount: $" + res.data.total);
                        } else {
                            $("#extension-price").text("Amount: $0");
                        }
                    }
                });
            });

           
            $("#extension-cancel").on("click", function() {
                $("#extendModal").addClass("hidden");
                resumeCallTimer(0, true);
                if (commandChannel) {
                    const commandData = JSON.stringify({
                        action: "resume-timer",
                        additionalMs: 0
                    });
                    try {
                        commandChannel.send(commandData);
                    } catch (e) {
                        console.warn("commandChannel.send failed", e);
                    }
                }
            });

            let checkInterval = null;

            $("#extension-pay").on("click", function() {
                const consultationId = $("#modal-consultation-id").val();
                const duration = $("#timeslot-select").val();

                if (!duration) {
                    alert("Please select a duration");
                    return;
                }

                // $("#extension-pay").prop("disabled", true).text("Processing...");

                $.ajax({
                    url: "{{ route('consultation.extend.pay') }}",  // new route
                    type: "POST",
                    data: {
                        consultation_id: consultationId,
                        duration: duration,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status && res.payment_url) {
                            const paymentTab = window.open(res.payment_url, "_blank");

                            if (checkInterval) clearInterval(checkInterval);
                            checkInterval = setInterval(() => {
                                checkPaymentStatus(consultationId);
                            }, 2000);
                        } else {
                            $("#extension-pay").prop("disabled", false).text("Pay & Extend");
                        }
                    },
                    error: function() {
                        $("#extension-pay").prop("disabled", false).text("Pay & Extend");
                        alert("Server error. Try again.");
                    }
                });
            });

           


            function checkPaymentStatus(consultationId) {
                $.ajax({
                    url: "{{ route('consultation.payment-status') }}",
                    type: "GET",
                    data: { consultation_id: consultationId },
                    success: function(res) {
                        if (res.success) {

                            if (typeof checkInterval !== 'undefined' && checkInterval) {
                                clearInterval(checkInterval);
                                checkInterval = null;
                            }
                            
                            const extendedMinutes = Number(res.extended_minutes) || 0;
                            resumeCallTimer(extendedMinutes, true);

                            if (commandChannel) {
                                const commandData = JSON.stringify({
                                    action: "resume-timer",
                                    additionalMs: extendedMinutes * 60 * 1000
                                });
                                try {
                                    commandChannel.send(commandData);
                                } catch (e) {
                                    console.warn("commandChannel.send failed", e);
                                }
                            }

                            $("#extendModal").addClass("hidden");

                            console.log("Consultation extended successfully!");
                        } else {
                             console.log("Payment not completed.");
                        }
                    }
                });
            }


        });
    </script>
@endsection
