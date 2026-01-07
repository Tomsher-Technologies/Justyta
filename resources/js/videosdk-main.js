document.addEventListener("DOMContentLoaded", async () => {
    const ZoomVideo = window.ZoomVideo;
    const metaKey = document.querySelector('meta[name="zoom-videosdk-key"]');
    const metaSecret = document.querySelector('meta[name="zoom-videosdk-secret"]');
    const sdkKey = metaKey ? metaKey.content : "";
    const sdkSecret = metaSecret ? metaSecret.content : "";

    const videoContainer = document.querySelector("video-player-container");
    const topic = "TestOne";
    const role = 1;
    const username = "User-" + String(Date.now()).slice(6);
    const client = ZoomVideo.createClient();

    let callStartTime = null;
    let timerInterval = null;
    let isTimerPaused = false;
    let remainingHoldTime = 0;
    let commandChannel = null;

    const isLocalhost = ["localhost", "127.0.0.1", "::1"].includes(
        location.hostname
    );
    const isSecure = window.isSecureContext || isLocalhost;

    


    async function startCall(data, username) {
        console.log("data", data);
        window.consultation_id = data.consultation_id;
        window.userRole = data.role;
      
        window.callDurationLimit = data.duration * 60 * 1000; 
        // window.callDurationLimit = 1 * 60 * 1000;

        if (!isSecure) {
            alert(
                "This app requires a secure context for camera/mic. Open via HTTPS."
            );
            return;
        }
        await client.init("en-US", "Global", { patchJsMedia: true });
      
        client.on("peer-video-state-change", renderVideo);
        client.on("user-added", onUserJoined);
        commandChannel = client.getCommandClient();
        client.on("command-channel-message", async (payload) => {
            console.log("Received command   :", payload.text);
            try {
                const data = JSON.parse(payload.text); 
                if (data.action === "pause-timer") {
                    stopCallTimer(true); 
                } else if (data.action === "resume-timer") {
                    resumeCallTimer(data.additionalMs || 0, false);
                }else if (data.action === "end-call") {
                    console.log("Received end-call command");
                    leaveCall();
                }

                if (data.action === "pause-av") {
                    pauseLocalAV();
                }

                if (data.action === "resume-av") {
                    resumeLocalAV();
                }

            } catch (e) {
                console.warn("Invalid command data", payload.text);
            }
        });
        window.commandChannel = commandChannel;
        
        await client.join(data.meeting_number, data.signature, username);
        
        const mediaStream = client.getMediaStream();
        await mediaStream.startAudio();
        await mediaStream.startVideo();
        await renderVideo({
            action: "Start",
            userId: client.getCurrentUserInfo().userId,
            userName: username,
        });
       
        document.getElementById('video-call-container').classList.remove('hidden');
        const response = await fetch(`/consultation/start-time/${data.consultation_id}`);
        const result = await response.json();

        if (result.start_time) {
            window.zoomCallStartTime = result.start_time;
            startCallTimer(result.start_time);
        }

        // startStatusPolling();
    }

    let callStarted = false;

    async function onUserJoined(user) {
        if (!callStarted) {
            callStarted = true;

            const now = Date.now();

            // store start time only once
            await fetch("/consultation/start-time", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": window.csrfToken
                },
                body: JSON.stringify({
                    consultation_id: window.consultation_id,
                    start_time: now
                })
            });

            window.zoomCallStartTime = now;
            startCallTimer(now);
        }
    }

  

    async function renderVideo(event) {
        const mediaStream = client.getMediaStream();
        const userId = event.userId;

        // console.log("user data", client.getCurrentUserInfo());

        const targetContainer = userId === client.getCurrentUserInfo().userId
            ? document.querySelector("#local-video")
            : document.querySelector("#remote-video");

        if (event.action === "Stop") {
            const elements = await mediaStream.detachVideo(event.userId);
            (Array.isArray(elements) ? elements : [elements])
                .forEach(el => el?.remove?.());
            return;
        }

        try {
            const userVideo = await mediaStream.attachVideo(event.userId, 2); // 360p
            if (userVideo && targetContainer && !targetContainer.contains(userVideo)) {
                targetContainer.appendChild(userVideo);

                // console.log("client ----------------------", client);
                if(userId === client.getCurrentUserInfo().userId) {
                    document.querySelector("#user-name").textContent = client.getCurrentUserInfo().displayName;
                }else {
                    const participants = client.getAllUser();
                    const participant = participants.find(p => p.userId === userId);

                    if (participant && participant.displayName) {
                        document.querySelector("#guest-name").textContent = participant.displayName;
                    } else {
                        document.querySelector("#guest-name").textContent = 'Guest';
                    }
                }
                console.log("Attached video element:", userVideo.tagName || userVideo.nodeName);

                // Small delay to ensure layout calculated
                setTimeout(() => {
                    // console.log("Video rect:", userVideo.getBoundingClientRect());
                }, 150);
            } else if (!userVideo) {
                // console.warn("attachVideo returned no element");
            }
        } catch (err) {
            // console.error("Error attaching video:", err);
        }
    }


    async function leaveCall() {
        console.log('leavecall');
        if (window.statusCheckInterval) clearInterval(window.statusCheckInterval);
        const mediaStream = client.getMediaStream();

        client.off("peer-video-state-change", renderVideo);
        client.off("video-active-change", renderVideo);
        client.off("user-added", renderVideo);
        client.off("user-removed", renderVideo);

        const users = client.getAllUser() || [];

        for (const user of users) {
            try {
                const result = await mediaStream.detachVideo(user.userId);

                if (Array.isArray(result)) {
                    result.forEach(el => el?.remove?.());
                } else if (result) {
                    result.remove?.();
                }

            } catch (e) {
                console.warn("Detach failed for user", user.userId, e);
            }
        }

        document.querySelectorAll("video, canvas").forEach(el => {
            try { el.remove(); } catch {}
        });

        document.querySelector("#guest-name").textContent = '';
        document.querySelector("#user-name").textContent = '';

        try {
            await client.leave();
        } catch (e) {
            console.warn("Zoom leave failed", e);
        }

        stopCallTimer();
        console.log('complete status');
        const response = await fetch(window.consultationStatusUpdateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken
            },
            body: JSON.stringify({
                consultation_id: window.consultation_id,
                status: 'completed'
            })
        });

        const result = await response.json();
        if (result.redirect_url) {
            window.location.href = result.redirect_url;
        }
    }


    async function toggleVideo() {
        const mediaStream = client.getMediaStream();
        const icon = document.getElementById("video-icon");
        const label = document.getElementById("video-label");

        if (mediaStream.isCapturingVideo()) {
            await mediaStream.stopVideo();
            await renderVideo({
                action: "Stop",
                userId: client.getCurrentUserInfo().userId,
            });

            icon.classList.remove("fa-video");
            icon.classList.add("fa-video-slash", "text-red-500");
        } else {
            await mediaStream.startVideo();
            await renderVideo({
                action: "Start",
                userId: client.getCurrentUserInfo().userId,
            });

            icon.classList.remove("fa-video-slash", "text-red-500");
            icon.classList.add("fa-video");
        }
    }

    async function toggleAudio() {
        const mediaStream = client.getMediaStream();
        const icon = document.getElementById("audio-icon");
        const label = document.getElementById("audio-label");

        try {
            if (mediaStream.isAudioMuted()) {
                await mediaStream.unmuteAudio();
                icon.classList.remove("fa-microphone-slash", "text-red-500");
                icon.classList.add("fa-microphone");
            } else {
                await mediaStream.muteAudio();
                icon.classList.remove("fa-microphone");
                icon.classList.add("fa-microphone-slash", "text-red-500");
            }
        } catch (error) {
            // console.error("Audio toggle failed:", error);
        }
    }



    const stopBtn = document.querySelector("#end-call-btn");
    const mute = document.querySelector("#toggle-audio-btn");
    const toggleVideoBtn = document.querySelector("#toggle-video-btn");

    if (stopBtn && toggleVideoBtn && mute) {
        stopBtn.addEventListener("click", async () => {
            toggleVideoBtn.style.display = "none";
            mute.style.display = "none";
            stopCallTimer();
            console.log('stopbutton');
            if (commandChannel) {
                const commandData = JSON.stringify({ action: "end-call" });
                await commandChannel.send(commandData);
            }
            stopBtn.style.display = "none";
            leaveCall();
        });

        toggleVideoBtn.addEventListener("click", async () => {
            await toggleVideo();
        });

        mute.addEventListener("click", async () => {
            await toggleAudio();
        });
    }
    window.startCall = startCall;
    window.renderVideo = renderVideo;
    window.leaveCall = leaveCall;
    window.toggleVideo = toggleVideo;
    window.extendCall = extendCall;
    window.resumeCallTimer = resumeCallTimer;
    // window.startStatusPolling = startStatusPolling;

    function startCallTimer(baseTime = null) {
        const timerElement = document.getElementById("call-timer");
        callStartTime = baseTime ? new Date(baseTime).getTime() : Date.now();

        if (!window.callDurationLimit) window.callDurationLimit = 0;
        if (timerInterval) clearInterval(timerInterval);

        timerInterval = setInterval(async () => {
            if (isTimerPaused) return; // skip while paused

            const now = Date.now();
            const elapsedMs = now - callStartTime; // how long since start/resume
            const remainingMs = window.callDurationLimit - elapsedMs;


            const extendBtn = document.getElementById("extend-call-btn");
            // if (remainingMs <= 5 * 60 * 1000 && extendBtn) {
            if (remainingMs <= 45 * 1000 && extendBtn) {
                extendBtn.classList.remove("hidden");
            }

            if (remainingMs <= 0) {
                clearInterval(timerInterval);
                timerInterval = null;
                stopCallTimer(false);
                leaveCall().catch(()=>{});
                return;
            }

            const totalSeconds = Math.floor(remainingMs / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            timerElement.textContent =
                (hours > 0 ? `${String(hours).padStart(2,"0")}:` : "") +
                `${String(minutes).padStart(2,"0")}:${String(seconds).padStart(2,"0")}`;
        }, 1000);
    }

   
    function stopCallTimer(pause = false) {
        if (timerInterval) {
            clearInterval(timerInterval);
            timerInterval = null;
        }

        if (pause) {
            isTimerPaused = true;
            const elapsedMs = Date.now() - callStartTime;
            remainingHoldTime = window.callDurationLimit - elapsedMs;
            // clamp
            if (remainingHoldTime < 0) remainingHoldTime = 0;
            console.log("Timer paused. remainingHoldTime (ms):", remainingHoldTime);
        } else {
            // full stop: reset display
            isTimerPaused = false;
            remainingHoldTime = 0;
            document.getElementById("call-timer").textContent = "00:00";
        }
    }

    function resumeCallTimer(additionalMinutesOrMs = 0, isMinutes = true) {
        // Convert to ms
        const additionalMs = isMinutes ? (Number(additionalMinutesOrMs) * 60 * 1000) : Number(additionalMinutesOrMs) || 0;

        console.log("resumeCallTimer called. additionalMs:", additionalMs, "remainingHoldTime:", remainingHoldTime);

        // compute new remaining duration (ms) after adding extension
        const newRemainingMs = (typeof remainingHoldTime === "number" ? remainingHoldTime : 0) + additionalMs;

        // Update the duration value (callDurationLimit should be a duration in ms)
        window.callDurationLimit = Number(newRemainingMs);

        // reset start time from now (we will measure elapsed from now)
        callStartTime = Date.now();

        // clear paused flag and (re)start interval
        isTimerPaused = false;
        if (timerInterval) clearInterval(timerInterval);
        startCallTimer(); // no baseTime needed since we set callStartTime above
        document.getElementById("extend-call-btn").classList.add("hidden");

        resumeLocalAV();

        // Notify others
        if (commandChannel) {
            const resumeData = JSON.stringify({ action: "resume-av" });
            commandChannel.send(resumeData);
        }
    }


    // function startStatusPolling() {
    //     if (!window.consultation_id) return;

    //     // store interval ID to clear later
    //     window.statusCheckInterval = setInterval(async () => {
    //         try {
    //             const res = await fetch(`/consultation/status/${window.consultation_id}`);
    //             const data = await res.json();
    //             if (data.status === 'completed') {
    //                 console.log("Consultation ended via backend");
    //                 leaveCall();
    //             }
    //         } catch (err) {
    //             console.warn("Failed to check consultation status", err);
    //         }
    //     }, 5000); // every 5 seconds
    // }

    async function extendCall(consultationId, consultantType) {
        if (!commandChannel) return;

        // pause timers for all participants
        const commandData = JSON.stringify({ action: "pause-timer" });
        await commandChannel.send(commandData); // sends to all

        const commandDataPause = JSON.stringify({ action: "pause-av" });
        await commandChannel.send(commandDataPause);

        // pause local AV (important)
        pauseLocalAV();

        // pause local timer
        stopCallTimer(true);

        // show modal to select duration & payment
        showExtensionModal(consultationId, consultantType);
    }

    function showExtensionModal(consultationId, consultantType) {
        const $modal = $("#extendModal");
        $modal.removeClass("hidden");

        // Set hidden fields
        $("#modal-consultation-id").val(consultationId);
        $("#modal-consultant-type").val(consultantType);

        const $select = $("#timeslot-select");
        $select.empty().append('<option value="">Select a timeslot</option>');
        $("#extension-price").text("Amount: $0");

        // Fetch timeslots via AJAX
        $.ajax({
            url: window.extendTimeSlots,
            type: 'GET',
            data: { consultation_id: consultationId, consultant_type: consultantType },
            success: function(res) {
                if (res.timeslots && res.timeslots.length) {
                    $.each(res.timeslots, function(i, slot) {
                        $select.append('<option value="'+slot.duration+'">'+slot.value+'</option>');
                    });
                }
            }
        });
    }

    async function pauseLocalAV() {
        const mediaStream = client.getMediaStream();

        try {
            let iconVideo = document.getElementById("video-icon");
            let iconAudio = document.getElementById("audio-icon");

            if (mediaStream.isCapturingVideo()) {
                await mediaStream.stopVideo();
                iconVideo.classList.remove("fa-video");
                iconVideo.classList.add("fa-video-slash", "text-red-500");
            }

            if (!mediaStream.isAudioMuted()) {
                await mediaStream.muteAudio();
                iconAudio.classList.remove("fa-microphone");
                iconAudio.classList.add("fa-microphone-slash", "text-red-500");
            }
        } catch (e) {
            console.log("Pause AV error", e);
        }
    }

    async function resumeLocalAV() {
        const mediaStream = client.getMediaStream();

        try {
            let iconVideo = document.getElementById("video-icon");
            let iconAudio = document.getElementById("audio-icon");

            await mediaStream.startVideo();
            iconVideo.classList.remove("fa-video-slash", "text-red-500");
            iconVideo.classList.add("fa-video");

            await mediaStream.unmuteAudio();
            iconAudio.classList.remove("fa-microphone-slash", "text-red-500");
            iconAudio.classList.add("fa-microphone");
        } catch (e) {
            console.log("Resume AV error:", e);
        }
    }
    
});
