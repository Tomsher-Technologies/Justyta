// Videosdk main without bundler; relies on window.ZoomVideo and KJUR (jsrsasign)
console.log("heer");

(function () {
    function whenReady(cb) {
        if (document.readyState !== "loading") cb();
        else document.addEventListener("DOMContentLoaded", cb);
    }
    function waitFor(cb) {
        if (
            window.ZoomVideo &&
            window.KJUR &&
            ((window.KJUR.jws && window.KJUR.jws.JWS) ||
                (window.KJUR.KJUR &&
                    window.KJUR.KJUR.jws &&
                    window.KJUR.KJUR.jws.JWS))
        ) {
            console.log("✅ ZoomVideo & KJUR loaded");
            cb();
        } else {
            console.log("⏳ Waiting for ZoomVideo or KJUR...");
            setTimeout(() => waitFor(cb), 100);
        }
    }

    whenReady(() => waitFor(boot));
    console.log("heer2");

    function boot() {
        console.log("heer2ss");
        const ZoomVideo = window.ZoomVideo;
        const metaKey = document.querySelector(
            'meta[name="zoom-videosdk-key"]'
        );
        const metaSecret = document.querySelector(
            'meta[name="zoom-videosdk-secret"]'
        );
        const sdkKey = metaKey ? metaKey.content : "";
        const sdkSecret = metaSecret ? metaSecret.content : "";
        console.log("ssss");
        const videoContainer = document.querySelector("video-player-container");
        const topic = "TestOne";
        const role = 1;
        const username = "User-" + String(Date.now()).slice(6);
        const client = ZoomVideo.createClient();
        console.log("topic", topic);
        const isLocalhost = ["localhost", "127.0.0.1", "::1"].includes(
            location.hostname
        );
        const isSecure = window.isSecureContext || isLocalhost;

        function generateSignature(sessionName, role, key, secret) {
            const K = window.KJUR;
            if (
                !(
                    window.KJUR &&
                    ((window.KJUR.jws && window.KJUR.jws.JWS) ||
                        (window.KJUR.KJUR &&
                            window.KJUR.KJUR.jws &&
                            window.KJUR.KJUR.jws.JWS))
                )
            ) {
                console.error("KJUR (jsrsasign) not available.");
                return "";
            }
            const iat = Math.floor(Date.now() / 1000) - 30;
            const exp = iat + 60 * 60 * 2;
            const oHeader = { alg: "HS256", typ: "JWT" };
            const oPayload = {
                app_key: key,
                tpc: sessionName,
                role_type: role,
                version: 1,
                iat,
                exp,
            };
            return (
                window.KJUR.jws ? window.KJUR.jws : window.KJUR.KJUR.jws
            ).JWS.sign(
                "HS256",
                JSON.stringify(oHeader),
                JSON.stringify(oPayload),
                secret
            );
        }

        async function startCall() {
            if (!isSecure) {
                alert(
                    "This app requires a secure context for camera/mic. Open via HTTPS or run on localhost."
                );
                return;
            }
            await client.init("en-US", "Global", { patchJsMedia: true });
            if (!sdkKey || !sdkSecret) {
                alert("Missing ZOOM_SDK_KEY or ZOOM_SDK_SECRET");
                return;
            }
            const token = generateSignature(topic, role, sdkKey, sdkSecret);
            if (!token) {
                alert("Failed to generate token. Check jsrsasign load.");
                return;
            }
            client.on("peer-video-state-change", renderVideo);
            await client.join(topic, token, username);
            const mediaStream = client.getMediaStream();
            await mediaStream.startAudio();
            await mediaStream.startVideo();
            await renderVideo({
                action: "Start",
                userId: client.getCurrentUserInfo().userId,
            });
        }

        async function renderVideo(event) {
            const mediaStream = client.getMediaStream();
            if (event.action === "Stop") {
                const element = await mediaStream.detachVideo(event.userId);
                Array.isArray(element)
                    ? element.forEach((el) => el.remove())
                    : element && element.remove && element.remove();
            } else {
                const userVideo = await mediaStream.attachVideo(
                    event.userId,
                    2 /* Video_360P */
                );
                if (userVideo) {
                    console.log(
                        "Attached video element:",
                        userVideo.tagName || userVideo.nodeName,
                        userVideo
                    );
                    if (videoContainer) {
                        videoContainer.appendChild(userVideo);
                    }
                    try {
                        setTimeout(() => {
                            console.log(
                                "Video rect:",
                                userVideo.getBoundingClientRect()
                            );
                        }, 150);
                    } catch (e) {}
                } else {
                    console.warn("attachVideo returned no element");
                }
                if (videoContainer && userVideo)
                    videoContainer.appendChild(userVideo);
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
            await client.leave();
        }

        async function toggleVideo() {
            const mediaStream = client.getMediaStream();
            if (mediaStream.isCapturingVideo()) {
                await mediaStream.stopVideo();
                await renderVideo({
                    action: "Stop",
                    userId: client.getCurrentUserInfo().userId,
                });
            } else {
                await mediaStream.startVideo();
                await renderVideo({
                    action: "Start",
                    userId: client.getCurrentUserInfo().userId,
                });
            }
        }

        const startBtn = document.querySelector("#start-btn");
        const stopBtn = document.querySelector("#stop-btn");
        const toggleVideoBtn = document.querySelector("#toggle-video-btn");
        console.log("Video Js");
        if (startBtn && stopBtn && toggleVideoBtn) {
            startBtn.addEventListener("click", async () => {
                if (!sdkKey || !sdkSecret) {
                    alert(
                        "Please set ZOOM_SDK_KEY and ZOOM_SDK_SECRET in .env"
                    );
                    return;
                }
                startBtn.innerHTML = "Connecting...";
                startBtn.disabled = true;
                await startCall();
                startBtn.innerHTML = "Connected";
                startBtn.style.display = "none";
                stopBtn.style.display = "block";
                toggleVideoBtn.style.display = "block";
            });

            stopBtn.addEventListener("click", async () => {
                toggleVideoBtn.style.display = "none";
                await leaveCall();
                stopBtn.style.display = "none";
                startBtn.style.display = "block";
                startBtn.innerHTML = "Join";
                startBtn.disabled = false;
            });

            toggleVideoBtn.addEventListener("click", async () => {
                await toggleVideo();
            });
        }
    }
})();
