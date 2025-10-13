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
    

    <link type="text/css" rel="stylesheet" href="https://source.zoom.us/2.14.0/css/bootstrap.css"/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="{{ asset('assets/css/select2.min.css') }}">
    <script src="{{ asset('assets/js/jquery-3.5.1.min.js') }}"></script>

    
    


    <style>
        /* Target the Select2 control box */
        .select2-container--default .select2-selection--single {
            background-color: #F9F9F9 !important;
            border: 1px solid #D1D5DB !important; /* border-gray-300 */
            border-radius: 10px !important;
            padding: 0.875rem 1rem !important;     /* matches p-3.5 */
            height: auto !important;
            min-height: 48px !important;           /* consistent with Tailwind input height */
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
            color: #1F2937 !important; /* text-gray-900 */
            font-size: 0.875rem !important; /* text-sm */
            line-height: 1.5 !important;
            padding: 0 !important;
        }       
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #000;
        }
    </style>
    @yield('style')
    <!-- Tailwind Animation -->
    <style>
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-10px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out; }
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

            <div class="max-w-3xl mx-auto p-6">
                <h2 class="text-2xl font-semibold mb-4">Incoming Consultations</h2>

                <div id="incomingPopup" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
                    <div class="bg-white rounded-2xl shadow-2xl w-[550px] max-w-[95vw] max-h-[90vh] overflow-y-auto border-l-4 border-blue-600 animate-fadeIn">
                        
                        <!-- Header -->
                        <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-500 rounded-t-2xl text-white">
                            <div class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m0-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                                <h3 class="text-lg font-bold">New Consultation Request</h3>
                            </div>
                            <button id="popupClose" class="text-white hover:text-gray-200 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-4 text-gray-700">
                            <div class="text-center mb-2">
                                <h4 class="font-semibold text-lg text-gray-800">User: <span id="callerName" class="text-blue-600"></span></h4>
                            </div>

                            <div class="grid grid-cols-1 gap-2">
                                <div class="flex justify-between border-b py-2">
                                    <span class="font-medium">Applicant Type:</span>
                                    <span id="applicantType" class="text-gray-900 font-semibold"></span>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <span class="font-medium">Litigation Type:</span>
                                    <span id="litigantType" class="text-gray-900 font-semibold"></span>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <span class="font-medium">Emirate:</span>
                                    <span id="emirate" class="text-gray-900 font-semibold"></span>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <span class="font-medium">You Represent:</span>
                                    <span id="youRepresent" class="text-gray-900 font-semibold"></span>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <span class="font-medium">Case Type:</span>
                                    <span id="caseType" class="text-gray-900 font-semibold"></span>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <span class="font-medium">Case Stage:</span>
                                    <span id="caseStage" class="text-gray-900 font-semibold"></span>
                                </div>
                                <div class="flex justify-between border-b py-2">
                                    <span class="font-medium">Language:</span>
                                    <span id="language" class="text-gray-900 font-semibold"></span>
                                </div>
                                <div class="flex justify-between py-2">
                                    <span class="font-medium">Duration:</span>
                                    <span id="duration" class="text-gray-900 font-semibold"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="px-6 pb-6 flex justify-between gap-3">
                            <button id="acceptBtn" class="flex-1 bg-green-600 hover:bg-green-700 transition text-white font-bold py-3 rounded-xl shadow-md flex items-center justify-center gap-2 text-lg">
                                Accept
                            </button>
                            <button id="rejectBtn" class="flex-1 bg-red-600 hover:bg-red-700 transition text-white font-bold py-3 rounded-xl shadow-md flex items-center justify-center gap-2 text-lg">
                                Reject
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Video -->
                <div id="videoArea" class="hidden">
                    <div id="videoContainer"></div>
                    <button id="leaveBtn">Leave Meeting</button>
                </div>
            </div>

        </main>
    </div>

    <!-- Banner -->
    @yield('ads')

    @include('frontend.include.footer')

    <script src="{{ asset('assets/js/select2.full.min.js') }}"></script>

    <script>
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
                        headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}' }
                    });
                    const data = await res.json();
                    if(data.status && data.data){
                        currentConsultation = data.data;
                        document.getElementById('callerName').textContent      = currentConsultation.user_name;
                        document.getElementById('applicantType').textContent   = currentConsultation.applicant_type;
                        document.getElementById('litigantType').textContent    = currentConsultation.litigant_type;
                        document.getElementById('emirate').textContent         = currentConsultation.emirate;
                        document.getElementById('youRepresent').textContent    = currentConsultation.you_represent;
                        document.getElementById('caseType').textContent        = currentConsultation.case_type;
                        document.getElementById('caseStage').textContent       = currentConsultation.case_stage;
                        document.getElementById('language').textContent        = currentConsultation.language;
                        document.getElementById('duration').textContent        = currentConsultation.duration;
                        document.getElementById('incomingPopup').classList.remove('hidden');
                    }
                } catch(err) {
                    console.error(err);
                }
            }
            setInterval(pollLawyer, 3000);

            // Close popup
            document.getElementById('popupClose').addEventListener('click', () => {
                document.getElementById('incomingPopup').classList.add('hidden');
                currentConsultation = null;
            });

            
            // // Accept
            // document.getElementById('acceptBtn').addEventListener('click', async () => {
            //     if(!currentConsultation) return;
            //     const res = await fetch("{{ route('web.lawyer.response') }}", {
            //         method:'POST',
            //         headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            //         body: JSON.stringify({consultation_id: currentConsultation.consultation_id, action:'accept'})
            //     });
            //     const data = await res.json();
            //     if(data.status){
            //         document.getElementById('incomingPopup').classList.add('hidden');

            //         consultationId = currentConsultation.consultation_id;
            //         setTimeout(() => {
            //             startZoomVideo(data.data, '{{ addslashes(auth()->user()->name) }}');
            //         }, 3000);
                    
            //     }
            // });

            // Reject
            document.getElementById('rejectBtn').addEventListener('click', async () => {
                if(!currentConsultation) return;
                await fetch("{{ route('web.lawyer.response') }}", {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    body: JSON.stringify({consultation_id: currentConsultation.consultation_id, action:'reject'})
                });
                document.getElementById('incomingPopup').classList.add('hidden');
                currentConsultation = null;
            });

            // async function checkCameraAccess() {
            //     try {
            //         const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            //         stream.getTracks().forEach(track => track.stop());
            //         console.log("Camera access granted");
            //         return true;
            //     } catch (err) {
            //         console.error("Camera access denied or error:", err);
            //         alert("Please grant camera access to start the video.");
            //         return false;
            //     }
            // }

            // window.startZoomVideo = async function (data, username) {
            //     // Verify camera access
            //     if (!(await checkCameraAccess())) {
            //         return;
            //     }

            //     let client;
            //     try {
            //         // Check SharedArrayBuffer support
            //         const isSharedArrayBufferSupported = typeof SharedArrayBuffer === 'function';
            //         console.log("SharedArrayBuffer supported:", isSharedArrayBufferSupported);
                    
            //         console.log("Meeting details:", data);

            //         // Initialize Zoom Video SDK
            //         client = ZoomVideo.createClient();
            //         await client.init("en-US", "Global", { debug: true });
            //         console.log("Zoom client initialized");

            //         // Join the meeting
            //         await client.join(data.meeting_number, data.signature, username, '');
            //         console.log("Joined meeting, userId:", client.getCurrentUserInfo().userId);

            //         const stream = client.getMediaStream();

            //         // --- Start of Corrected Section ---

            //         // Clear and prepare video container
            //         const container = document.getElementById('videoContainer');
            //         if (!container) {
            //             throw new Error("Video container not found");
            //         }
            //         container.innerHTML = '';

            //         // Create and configure self video element FIRST
            //         const selfVideoElement = document.createElement("video");
            //         selfVideoElement.id = "self-video";
            //         selfVideoElement.autoplay = true;
            //         selfVideoElement.muted = true;
            //         selfVideoElement.playsInline = true;
            //         selfVideoElement.style.width = "400px";
            //         selfVideoElement.style.height = "300px";
            //         selfVideoElement.style.border = "2px solid blue"; // Debugging border
            //         container.appendChild(selfVideoElement);

            //         // Verify video element
            //         console.log("Self video element created:", selfVideoElement);

            //         // 💡 *THE FIX:* Start video and render the self-view in one step
            //         await stream.startVideo({ videoElement: selfVideoElement });
            //         console.log("Video stream started and attached to self-view element");

            //         const existingUsers = client.getAllUser();
            //         console.log("Existing participants:", existingUsers);

            //         // If lawyer already joined before user
            //         if (existingUsers.length > 1) {
            //             const lawyerUser = existingUsers.find(u => u.userId !== client.getCurrentUserInfo().userId);
            //             if (lawyerUser) {
            //                 console.log("Lawyer already in meeting:", lawyerUser.userId);

            //                 const remoteVideoElement = document.createElement("video");
            //                 remoteVideoElement.id = `video-${lawyerUser.userId}`;
            //                 remoteVideoElement.autoplay = true;
            //                 remoteVideoElement.playsInline = true;
            //                 remoteVideoElement.style.width = "400px";
            //                 remoteVideoElement.style.height = "300px";
            //                 remoteVideoElement.style.border = "2px solid green";
            //                 container.appendChild(remoteVideoElement);

            //                 const userInfo = client.getUser(lawyerUser.userId);
            //                 if (userInfo?.bVideoOn) {
            //                     await stream.attachVideo(lawyerUser.userId, remoteVideoElement);

            //                     // ✅ Update consultation status
            //                     await fetch(`{{ route('consultation.status.update') }}`, {
            //                         method: 'POST',
            //                         headers: {
            //                             'Content-Type': 'application/json',
            //                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //                         },
            //                         body: JSON.stringify({
            //                             consultation_id: consultationId,
            //                             status: 'in_progress'
            //                         })
            //                     });
            //                 }
            //             }
            //         }

            //         // Handle remote participants
            //         client.on('user-added', async (payload) => {

            //             console.log("User addeddddddddddddddddddddd:" +payload );
            //             console.log(payload);
            //             const remoteUser = payload.user || payload;
            //             const remoteUserId = remoteUser.userId;
            //             console.log("Remote user joined:", remoteUserId);

            //             if (!remoteUserId) return;

            //             const remoteVideoElement = document.createElement("video");
            //             remoteVideoElement.id = `video-${remoteUserId}`;
            //             remoteVideoElement.autoplay = true;
            //             remoteVideoElement.playsInline = true;
            //             remoteVideoElement.style.width = "400px";
            //             remoteVideoElement.style.height = "300px";
            //             remoteVideoElement.style.border = "2px solid green"; // Debugging border
            //             container.appendChild(remoteVideoElement);

            //             try {
            //                 // This is correct for remote users
            //                 await stream.attachVideo(remoteUserId, remoteVideoElement);
            //                 console.log(`Remote video attached for user ${remoteUserId}`);

            //                 await fetch(`{{ route('consultation.status.update') }}`, {
            //                     method: 'POST',
            //                     headers: {
            //                         'Content-Type': 'application/json',
            //                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //                     },
            //                     body: JSON.stringify({
            //                         consultation_id: consultationId,
            //                         status: 'in_progress'
            //                     })
            //                 });
            //             } catch (err) {
            //                 console.error(`Failed to attach remote video for ${remoteUserId}:`, err);
            //             }
            //         });

            //         // Handle remote user leaving
            //         client.on('user-removed', async (payload) => {
            //             const remoteUser = payload.user || payload;
            //             const remoteUserId = remoteUser.userId;
            //             console.log("Remote user left:", remoteUserId);

            //             if (!remoteUserId) return;
            //             const remoteVideoElement = document.getElementById(`video-${remoteUserId}`);
            //             if (remoteVideoElement) {
            //                 remoteVideoElement.remove();
            //             }
            //             await fetch(`{{ route('consultation.status.update') }}`, {
            //                 method: 'POST',
            //                 headers: {
            //                     'Content-Type': 'application/json',
            //                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //                 },
            //                 body: JSON.stringify({
            //                     consultation_id: consultationId,
            //                     status: 'completed'
            //                 })
            //             });
            //         });

            //         // Show video area
            //         const videoArea = document.getElementById('videoArea');
            //         if (videoArea) {
            //             videoArea.classList.remove('hidden');
            //         } else {
            //             console.error("Video area element not found");
            //         }

            //         // Handle leave meeting
            //         const leaveBtn = document.getElementById('leaveBtn');
            //         if (leaveBtn) {
            //             leaveBtn.addEventListener('click', async () => {
            //                 try {
            //                     await stream.stopVideo();
            //                     await stream.stopAudio();
            //                     await client.leave();
            //                     console.log("Left meeting");
            //                     if (videoArea) {
            //                         videoArea.classList.add('hidden');
            //                     }
            //                     container.innerHTML = ''; // Clear videos

            //                     await fetch(`{{ route('consultation.status.update') }}`, {
            //                         method: 'POST',
            //                         headers: {
            //                             'Content-Type': 'application/json',
            //                             'X-CSRF-TOKEN': '{{ csrf_token() }}'
            //                         },
            //                         body: JSON.stringify({
            //                             consultation_id: consultationId,
            //                             status: 'completed'
            //                         })
            //                     });
            //                 } catch (err) {
            //                     console.error("Error leaving meeting:", err);
            //                 }
            //             });
            //         } else {
            //             console.error("Leave button not found");
            //         }
            //     } catch (error) {
            //         console.error("Zoom SDK Error:", error);
            //         alert(`Failed to start Zoom meeting: ${error.message || 'Unknown error'}`);
            //     }
            // };


            // Global variable to store consultation ID
            let consultationId = null; 

            // Define a constant for Zoom Video SDK's VIEW_MODE.Contain. 
            // This ensures the video fills the container while maintaining aspect ratio.
            const VIEW_MODE_CONTAIN = 2; 

            /**
             * Utility function to check for camera access permissions.
             * @returns {Promise<boolean>}
             */
            async function checkCameraAccess() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                    // Stop the tracks immediately after checking access
                    stream.getTracks().forEach(track => track.stop());
                    return true;
                } catch (e) {
                    console.error("Camera access denied or failed:", e);
                    displayMessage("Please allow camera and microphone access to start the consultation.");
                    return false;
                }
            }

            /**
             * Displays a non-alert message to the user.
             */
            function displayMessage(message) {
                const container = document.getElementById('videoContainer');
                let messageDiv = document.getElementById('statusMessage');
                
                if (!container) {
                    console.error("Cannot display message: videoContainer not found.");
                    return;
                }

                if (!messageDiv) {
                    messageDiv = document.createElement('div');
                    messageDiv.id = 'statusMessage';
                    // Basic styling for visibility (adjust these classes for your CSS framework)
                    messageDiv.className = 'text-red-600 font-semibold p-4 border border-red-300 rounded-lg bg-red-50 absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 shadow-lg z-50';
                    container.appendChild(messageDiv);
                }
                
                messageDiv.textContent = message;
                
                // Hide after 5 seconds
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 5000);
            }


            // Event listener for accepting the consultation
            document.getElementById('acceptBtn').addEventListener('click', async () => {
                // Assuming 'currentConsultation' is a globally available object set before this call.
                if(!currentConsultation) return; 
                
                const res = await fetch("{{ route('web.lawyer.response') }}", {
                    method:'POST',
                    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
                    body: JSON.stringify({consultation_id: currentConsultation.consultation_id, action:'accept'})
                });
                const data = await res.json();
                
                if(data.status){
                    document.getElementById('incomingPopup').classList.add('hidden');
                    
                    // Set the global consultationId for status updates later
                    consultationId = currentConsultation.consultation_id; 

                    // Wait a few seconds before starting the video to allow time for the client to join
                    setTimeout(() => {
                        // Note: Ensure the ZoomVideo SDK is loaded via a <script> tag earlier in your HTML.
                        window.startZoomVideo(data.data, '{{ addslashes(auth()->user()->name) }}');
                    }, 3000); 
                }
            });


            /**
             * Initiates the Zoom video consultation.
             * @param {object} data Meeting details (meeting_number, signature).
             * @param {string} username Current user's display name.
             */
            window.startZoomVideo = async function (data, username) {
                let client;
                const container = document.getElementById('videoContainer');
                const videoArea = document.getElementById('videoArea');

                // 1. Verify camera access
                if (!(await checkCameraAccess())) {
                    return;
                }

                try {
                    console.log("Meeting details:", data);

                    // 2. Initialize Zoom Video SDK
                    // IMPORTANT: Ensure 'ZoomVideo' is globally available from the Zoom SDK script tag.
                    client = ZoomVideo.createClient();
                    await client.init("en-US", "Global", { debug: true });
                    console.log("Zoom client initialized");

                    // 3. Join the meeting
                    await client.join(data.meeting_number, data.signature, username, '');
                    const currentUserId = client.getCurrentUserInfo().userId;
                    console.log("Joined meeting, userId:", currentUserId);

                    const stream = client.getMediaStream();

                    // 4. Clear and prepare video container
                    if (!container) {
                        throw new Error("Video container not found");
                    }
                    container.innerHTML = '';
                    
                    // **IMPORTANT:** Ensure the video container has a layout (e.g., flex) and dimensions
                    // so the video wrappers can render side-by-side correctly.
                    container.className = 'flex justify-center items-center h-[500px] w-full p-4'; 

                    
                    /**
                     * Function to create the video element wrapper (DIV) for rendering.
                     * @param {number} userId The user ID
                     * @param {boolean} isSelf Whether this is the local user's view
                     * @returns {HTMLElement} The created video wrapper DIV
                     */
                    const createVideoWrapper = (userId, isSelf = false) => {
                        const idPrefix = isSelf ? 'self' : 'remote';
                        let videoWrapper = document.getElementById(`${idPrefix}-video-wrapper-${userId}`);
                        
                        if (videoWrapper) return videoWrapper; // Already exists
                        
                        videoWrapper = document.createElement("div");
                        videoWrapper.id = `${idPrefix}-video-wrapper-${userId}`;
                        
                        // Apply different borders for self (blue) vs remote (green) view
                        const borderColor = isSelf ? 'border-blue-500' : 'border-green-500';
                        // These classes define the visual space (adjust as needed)
                        videoWrapper.className = `w-1/2 h-full rounded-lg shadow-xl mx-2 border-4 ${borderColor} overflow-hidden relative`;
                        
                        container.appendChild(videoWrapper);
                        return videoWrapper;
                    };

                    // 5. Create and render self video element (Lawyer's video) using renderVideo
                    const selfVideoWrapper = createVideoWrapper(currentUserId, true);

                    // Start local video and audio
                    await stream.startVideo(); 
                    await stream.startAudio(); 
                    console.log("Video and audio stream started");

                    // Render the lawyer's stream into the wrapper element
                    // Using currentUserId as the userId parameter for self-view
                    await stream.renderVideo(selfVideoWrapper, currentUserId, VIEW_MODE_CONTAIN); 
                    console.log("Local video stream rendered.");


                    // 6. Listener for remote user's video status changes (The FIX!)
                    client.on('user-video-status-change', async (payload) => {
                        const remoteUser = payload.user || payload;
                        const remoteUserId = remoteUser.userId;
                        const videoStatus = payload.action; // 'Active' or 'Inactive'
                        
                        // Only care about other users
                        if (remoteUserId === currentUserId) return; 

                        console.log(`User ${remoteUserId} video status changed to: ${videoStatus}`);

                        if (videoStatus === 'Active') {
                            const remoteVideoWrapper = createVideoWrapper(remoteUserId, false);
                            try {
                                // Use renderVideo for attachment and rendering
                                await stream.renderVideo(remoteVideoWrapper, remoteUserId, VIEW_MODE_CONTAIN); 
                                console.log(`Remote video rendered for user ${remoteUserId}`);
                                
                                // Update status when the second participant's video is confirmed active
                                await updateConsultationStatus('in_progress'); 

                            } catch (err) {
                                console.error(`Failed to render remote video for ${remoteUserId}:`, err);
                            }
                        } else if (videoStatus === 'Inactive') {
                            // Video stopped, detach the stream
                            await stream.stopRenderVideo(remoteUserId);
                            console.log(`Remote video render stopped for user ${remoteUserId}`);
                        }
                    });

                    // 7. Handle initial existing users (if client joined before lawyer)
                    const existingUsers = client.getAllUser();
                    console.log("Existing participants:", existingUsers);

                    if (existingUsers.length > 1) {
                        const remoteUser = existingUsers.find(u => u.userId !== currentUserId);
                        if (remoteUser) {
                            console.log("Remote user (Client) already in meeting:", remoteUser.userId);
                            
                            const userInfo = client.getUser(remoteUser.userId);
                            const remoteVideoWrapper = createVideoWrapper(remoteUser.userId, false);

                            // If video is already on, render immediately
                            // The logs indicated bVideoOn: true, so this path should execute.
                            if (userInfo?.bVideoOn) { 
                                await stream.renderVideo(remoteVideoWrapper, remoteUser.userId, VIEW_MODE_CONTAIN);
                                console.log("Immediately rendered video of existing user.");
                                await updateConsultationStatus('in_progress'); 
                            }
                        }
                    }
                    
                    // 8. Handle user joining and removing (creating/destroying elements)
                    client.on('user-added', async (payload) => {
                        const remoteUser = payload.user || payload;
                        if (remoteUser.userId === currentUserId) return; // Ignore self
                        
                        console.log("Remote user joined:", remoteUser.userId);
                        // Element created here. Video rendering handled by 'user-video-status-change'
                        createVideoWrapper(remoteUser.userId, false); 
                    });

                    client.on('user-removed', async (payload) => {
                        const remoteUser = payload.user || payload;
                        const remoteUserId = remoteUser.userId;
                        console.log("Remote user left:", remoteUserId);

                        const remoteVideoWrapper = document.getElementById(`remote-video-wrapper-${remoteUserId}`);
                        if (remoteVideoWrapper) {
                            // IMPORTANT: Stop rendering before removing the element
                            await stream.stopRenderVideo(remoteUserId); 
                            remoteVideoWrapper.remove();
                        }
                        // Update status to 'completed' when the other user leaves
                        await updateConsultationStatus('completed');
                    });
                    
                    // 9. Show video area
                    if (videoArea) {
                        videoArea.classList.remove('hidden');
                    } else {
                        console.error("Video area element not found");
                    }

                    // 10. Handle leave meeting button
                    const leaveBtn = document.getElementById('leaveBtn');
                    if (leaveBtn) {
                        leaveBtn.addEventListener('click', async () => {
                            try {
                                // Stop rendering local stream and leave the meeting
                                await stream.stopRenderVideo(currentUserId); 
                                await stream.stopVideo();
                                await stream.stopAudio();
                                await client.leave();
                                console.log("Left meeting");
                                
                                if (videoArea) {
                                    videoArea.classList.add('hidden');
                                }
                                container.innerHTML = ''; // Clear videos

                                // Final status update upon lawyer leaving
                                await updateConsultationStatus('completed');
                            } catch (err) {
                                console.error("Error leaving meeting:", err);
                                displayMessage("Error leaving the meeting. Please refresh the page manually.");
                            }
                        });
                    } else {
                        console.error("Leave button not found");
                    }
                } catch (error) {
                    console.error("Zoom SDK Error:", error);
                    displayMessage(`Failed to start Zoom meeting: ${error.message || 'Unknown error'}. Check console for details.`);
                }
            };

            /**
             * Helper function to update consultation status on the backend.
             * @param {string} status The new status ('in_progress' or 'completed').
             */
            async function updateConsultationStatus(status) {
                if (!consultationId) {
                    console.warn("Cannot update status: consultationId is missing.");
                    return;
                }
                try {
                    await fetch(`{{ route('consultation.status.update') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            consultation_id: consultationId,
                            status: status
                        })
                    });
                    console.log(`Consultation status updated to: ${status}`);
                } catch (e) {
                    console.error(`Failed to update consultation status to ${status}:`, e);
                }
            }
        });

       
    </script>


    @yield('script')
</body>

</html>