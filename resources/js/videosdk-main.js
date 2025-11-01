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

    // function generateSignature(sessionName, role, key, secret){
    //   const K = window.KJUR;
    //   if (!(window.KJUR && ((window.KJUR.jws && window.KJUR.jws.JWS) || (window.KJUR.KJUR && window.KJUR.KJUR.jws && window.KJUR.KJUR.jws.JWS)))) { console.error('KJUR (jsrsasign) not available.'); return ''; }
    //   const iat = Math.floor(Date.now()/1000) - 30;
    //   const exp = iat + 60*60*2;
    //   const oHeader = { alg: 'HS256', typ: 'JWT' };
    //   const oPayload = { app_key: key, tpc: sessionName, role_type: role, version: 1, iat, exp };
    //   return (window.KJUR.jws ? window.KJUR.jws : window.KJUR.KJUR.jws).JWS.sign('HS256', JSON.stringify(oHeader), JSON.stringify(oPayload), secret);
    // }

    async function startCall(data, username) {
        console.log("data", data);

        if (!isSecure) {
            alert(
                "This app requires a secure context for camera/mic. Open via HTTPS or run on localhost."
            );
            return;
        }
        await client.init("en-US", "Global", { patchJsMedia: true });
        // if (!sdkKey || !sdkSecret) { alert('Missing ZOOM_SDK_KEY or ZOOM_SDK_SECRET'); return; }
        // const token = generateSignature(topic, role, sdkKey, sdkSecret);
        // if (!token) { alert('Failed to generate token. Check jsrsasign load.'); return; }
        client.on("peer-video-state-change", renderVideo);
        await client.join(data.meeting_number, data.signature, username);
        const mediaStream = client.getMediaStream();
        await mediaStream.startAudio();
        await mediaStream.startVideo();
        await renderVideo({
            action: "Start",
            userId: client.getCurrentUserInfo().userId,
            userName: username,
        });
        const sessionInfo = client.getSessionInfo();
        console.log("sessionInfoTimer", sessionInfo);
        const baseTime = sessionInfo.startTime || Date.now();

        document.getElementById('video-call-container').classList.remove('hidden');
        startCallTimer(baseTime);
    }

    // async function renderVideo(event) {
    //     const mediaStream = client.getMediaStream();
    //     if (event.action === "Stop") {
    //         const element = await mediaStream.detachVideo(event.userId);
    //         Array.isArray(element)
    //             ? element.forEach((el) => el.remove())
    //             : element && element.remove && element.remove();
    //     } else {
    //         const userVideo = await mediaStream.attachVideo(
    //             event.userId,
    //             2 /* Video_360P */
    //         );
    //         if (userVideo) {
    //             console.log(
    //                 "Attached video element:",
    //                 userVideo.tagName || userVideo.nodeName,
    //                 userVideo
    //             );
    //             if (videoContainer) {
    //                 videoContainer.appendChild(userVideo);
    //             }
    //             try {
    //                 setTimeout(() => {
    //                     console.log(
    //                         "Video rect:",
    //                         userVideo.getBoundingClientRect()
    //                     );
    //                 }, 150);
    //             } catch (e) {}
    //         } else {
    //             console.warn("attachVideo returned no element");
    //         }
    //         if (videoContainer && userVideo)
    //             videoContainer.appendChild(userVideo);
    //     }
    // }

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

        // Use provided base time (same for everyone) or local time as fallback
        callStartTime = baseTime ? new Date(baseTime).getTime() : Date.now();

        if (timerInterval) clearInterval(timerInterval);

        timerInterval = setInterval(() => {
            const elapsedMs = Date.now() - callStartTime;
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
