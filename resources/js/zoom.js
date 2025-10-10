// resources/js/zoom.js
import ZoomVideo from '@zoom/videosdk';

export async function startZoomVideo(data, username) {
    const client = ZoomVideo.createClient();
    await client.init('en-US', 'zoom-media-div'); // optional locale
    await client.join(
        data.sdk_key,
        data.meeting_number,
        username,
        data.signature,
        data.role
    );

    const stream = client.getMediaStream();
    await stream.startVideo();
    await stream.startAudio();

    const container = document.getElementById('videoContainer');
    container.innerHTML = '';
    const localDiv = document.createElement('div');
    localDiv.style.width = '100%';
    localDiv.style.height = '100%';
    container.appendChild(localDiv);

    stream.renderVideo(localDiv, { fit: 'cover' });

    document.getElementById('leaveBtn').addEventListener('click', async () => {
        await stream.stopVideo();
        await stream.stopAudio();
        await client.leave();
        document.getElementById('videoArea').classList.add('hidden');
    });

    document.getElementById('videoArea').classList.remove('hidden');
}
