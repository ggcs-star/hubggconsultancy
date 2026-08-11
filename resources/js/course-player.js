/**
 * Drives the actual video playback for the course player defined in
 * resources/views/user/courses/_player.blade.php. That Alpine component
 * owns all UI/answer state; this module only ever talks to it through
 * window CustomEvents (course:load-item / course:before-switch /
 * course:continue) and dispatches course:checkpoint / course:lesson-completed
 * back when the video reaches a checkpoint or ends.
 */

let teardownCurrent = null;
let resumeCurrent = null;

function completeLesson(item) {
    window.axios.post(item.progressUrl, { completed: true }).finally(() => {
        window.dispatchEvent(new CustomEvent('course:lesson-completed'));
    });
}

function nextUnfiredCheckpoint(checkpoints, fired, currentTime) {
    return (checkpoints || []).find((checkpoint) => ! fired.has(checkpoint.id) && currentTime >= checkpoint.timestamp_seconds);
}

function setupNativeVideo(item) {
    const video = document.getElementById('course-native-video');
    if (! video) return;

    const fired = new Set();

    const onTimeUpdate = () => {
        const checkpoint = nextUnfiredCheckpoint(item.checkpoints, fired, video.currentTime);
        if (checkpoint) {
            fired.add(checkpoint.id);
            video.pause();
            window.dispatchEvent(new CustomEvent('course:checkpoint', { detail: checkpoint }));
        }
    };

    const onEnded = () => completeLesson(item);

    video.addEventListener('timeupdate', onTimeUpdate);
    video.addEventListener('ended', onEnded);

    teardownCurrent = () => {
        video.removeEventListener('timeupdate', onTimeUpdate);
        video.removeEventListener('ended', onEnded);
    };

    resumeCurrent = () => video.play();
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

    window.onYouTubeIframeAPIReady = () => {
        window.__ytApiCallbacks.forEach((callback) => callback());
        window.__ytApiCallbacks = [];
    };

    const tag = document.createElement('script');
    tag.src = 'https://www.youtube.com/iframe_api';
    document.head.appendChild(tag);
}

function setupYoutubeVideo(item) {
    const container = document.getElementById('course-youtube-player');
    if (! container) return;

    const fired = new Set();
    let player = null;
    let pollTimer = null;

    loadYoutubeApi(() => {
        player = new window.YT.Player('course-youtube-player', {
            videoId: item.videoSrc,
            playerVars: { rel: 0 },
            events: {
                onReady: (event) => event.target.playVideo(),
                onStateChange: (event) => {
                    if (event.data === window.YT.PlayerState.ENDED) {
                        completeLesson(item);
                    }
                },
            },
        });

        pollTimer = setInterval(() => {
            if (! player || typeof player.getCurrentTime !== 'function') return;

            const checkpoint = nextUnfiredCheckpoint(item.checkpoints, fired, player.getCurrentTime());
            if (checkpoint) {
                fired.add(checkpoint.id);
                player.pauseVideo();
                window.dispatchEvent(new CustomEvent('course:checkpoint', { detail: checkpoint }));
            }
        }, 500);
    });

    teardownCurrent = () => {
        clearInterval(pollTimer);
        if (player && typeof player.destroy === 'function') player.destroy();
        player = null;
    };

    resumeCurrent = () => player && player.playVideo();
}

window.addEventListener('course:before-switch', () => {
    if (teardownCurrent) teardownCurrent();
    teardownCurrent = null;
    resumeCurrent = null;
});

window.addEventListener('course:load-item', (event) => {
    const item = event.detail?.item;
    if (! item || item.type !== 'lesson') return;

    if (item.videoSource === 'upload') {
        setupNativeVideo(item);
    } else if (item.videoSource === 'youtube') {
        setupYoutubeVideo(item);
    }
});

window.addEventListener('course:continue', () => {
    if (resumeCurrent) resumeCurrent();
});
