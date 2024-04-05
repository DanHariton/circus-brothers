document.body.addEventListener('click', function () {
    let video = document.getElementById('myVideo');

    if (video) {
        if (video.paused) {
            video.play();
        }
    }
}, {once: true});