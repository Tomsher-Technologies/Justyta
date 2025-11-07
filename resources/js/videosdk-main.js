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
                "This app requires a secure context for camera/mic. Open via HTTPS or run on localhost."
            );
            return;
        }
        await client.init("en-US", "Global", { patchJsMedia: true });
      
        client.on("peer-video-state-change", renderVideo);
        client.on("user-added", onUserJoined);
        client.on("user-removed", onUserLeft);
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

    function onUserLeft() {
        stopCallTimer();
        leaveCall();
    }

    async function renderVideo(event) {
        const mediaStream = client.getMediaStream();
        const userId = event.userId;

        console.log("user data", client.getCurrentUserInfo());

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

                console.log("client ----------------------", client);
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
                console.log(
                    "Attached video element:",
                    userVideo.tagName || userVideo.nodeName
                );

                // Small delay to ensure layout calculated
                setTimeout(() => {
                    console.log("Video rect:", userVideo.getBoundingClientRect());
                }, 150);
            } else if (!userVideo) {
                console.warn("attachVideo returned no element");
            }
        } catch (err) {
            console.error("Error attaching video:", err);
        }
    }


    async function leaveCall() {
        const mediaStream = client.getMediaStream();
        for (const user of client.getAllUser()) {
            const element = await mediaStream.detachVideo(user.userId);
            Array.isArray(element)
                ? element.forEach((el) => el.remove())
                : element && element.remove && element.remove();
        }
        client.off("peer-video-state-change", renderVideo);
        document.querySelector("#guest-name").textContent = '';
        document.querySelector("#user-name").innerHTML = '';
        await client.leave();
        stopCallTimer();
        // await fetch(window.consultationStatusUpdateUrl, {
        //     method: 'POST',
        //     headers: {
        //         'Content-Type': 'application/json',
        //         'X-CSRF-TOKEN': window.csrfToken
        //     },
        //     body: JSON.stringify({
        //         consultation_id: window.consultation_id,
        //         status: 'completed'
        //     })
        // });
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
            console.error("Audio toggle failed:", error);
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
            await leaveCall();
            stopBtn.style.display = "none";
           
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


    let callStartTime = null;
    let timerInterval = null;

    function startCallTimer(baseTime = null) {
        const timerElement = document.getElementById("call-timer");
        const countdownElement = document.getElementById("call-countdown");

        callStartTime = baseTime ? new Date(baseTime).getTime() : Date.now();

        if (timerInterval) clearInterval(timerInterval);

        timerInterval = setInterval(async () => {
            const nowTime = Date.now();
            const elapsedMs = nowTime - callStartTime;

            console.log('********************************************************************************');
            console.log("Start Time:", callStartTime);
            console.log("Elapsed Time:", elapsedMs);
            console.log("Duration:", window.callDurationLimit);
            console.log('===============================================================================');
            // If duration defined
            // if (window.callDurationLimit) {
            //     const remainingMs = window.callDurationLimit - elapsedMs;

            //     if (remainingMs <= 0) {
            //         countdownElement.textContent = "Time Left: 00:00";
            //         console.log("Meeting duration reached. Ending call...");
            //         stopCallTimer();
            //         await leaveCall();
            //         alert("Your consultation time has ended.");
            //         return;
            //     }

            //     const remainingSeconds = Math.floor(remainingMs / 1000);
            //     const rMin = Math.floor(remainingSeconds / 60);
            //     const rSec = remainingSeconds % 60;
            //     console.log(`Remaining Time: ${rMin}:${rSec}`);
            //     countdownElement.textContent =
            //         `Time Left: ${String(rMin).padStart(2, "0")}:${String(rSec).padStart(2, "0")}`;
            // }

            const totalSeconds = Math.floor(elapsedMs / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            timerElement.textContent =
                (hours > 0 ? `${String(hours).padStart(2, "0")}:` : "") +
                `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;
        }, 1000);
    }


    function stopCallTimer() {
        if (timerInterval) clearInterval(timerInterval);
        timerInterval = null;
        const timerElement = document.getElementById("call-timer");
        if (timerElement) timerElement.textContent = "00:00";
    }

    
});
