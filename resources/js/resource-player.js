/**
 * Drives YouTube playback for the Resources video-quiz modal defined in
 * resources/views/user/resources/index.blade.php. That Alpine component owns
 * all UI/answer state; this module only ever talks to it through window
 * CustomEvents (resource:checkpoint / resource:ended) and listens for
 * resource:continue to resume playback — same decoupling as course-player.js,
 * trimmed down for a single always-YouTube video inside a modal (no lesson
 * list to switch between, no native-upload path).
 */

let currentPlayer = null;
let pollTimer = null;

function nextUnfiredCheckpoint(checkpoints, fired, currentTime) {
    return (checkpoints || []).find((checkpoint) => ! fired.has(checkpoint.id) && currentTime >= checkpoint.timestamp_seconds);
}

function loadYoutubeApi(onReady) {
    if (window.YT && window.YT.Player) {
        onReady();
        return;
    }

    window.__ytApiCallbacks = window.__ytApiCallbacks || [];
    window.__ytApiCallbacks.push(onReady);

    if (window.__ytApiLoading) return;
    window.__ytApiLoading = true;

    const previousReady = window.onYouTubeIframeAPIReady;
    window.onYouTubeIframeAPIReady = () => {
        if (typeof previousReady === 'function') previousReady();
        window.__ytApiCallbacks.forEach((callback) => callback());
        window.__ytApiCallbacks = [];
    };

    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(tag);
}

window.startResourcePlayer = function (videoId, checkpoints) {
    window.stopResourcePlayer();

    const fired = new Set();

    loadYoutubeApi(() => {
        const container = document.getElementById('resource-youtube-player');
        if (! container) return;

        currentPlayer = new window.YT.Player('resource-youtube-player', {
            videoId,
            playerVars: { rel: 0 },
            events: {
                onReady: (event) => event.target.playVideo(),
                onStateChange: (event) => {
                    if (event.data === window.YT.PlayerState.ENDED) {
                        window.dispatchEvent(new CustomEvent('resource:ended'));
                    }
                },
            },
        });

        pollTimer = setInterval(() => {
            if (! currentPlayer || typeof currentPlayer.getCurrentTime !== 'function') return;

            const checkpoint = nextUnfiredCheckpoint(checkpoints, fired, currentPlayer.getCurrentTime());
            if (checkpoint) {
                fired.add(checkpoint.id);
                currentPlayer.pauseVideo();
                window.dispatchEvent(new CustomEvent('resource:checkpoint', { detail: checkpoint }));
            }
        }, 500);
    });
};

window.stopResourcePlayer = function () {
    if (pollTimer) clearInterval(pollTimer);
    pollTimer = null;

    if (currentPlayer && typeof currentPlayer.destroy === 'function') {
        currentPlayer.destroy();
    }
    currentPlayer = null;
};

window.addEventListener('resource:continue', () => {
    if (currentPlayer) currentPlayer.playVideo();
});
