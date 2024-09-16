document.addEventListener('DOMContentLoaded', function() {
    let video = document.getElementById('banner-vid');

    if (video) {
        video.play().catch(error => {
            console.error("Video cannot be played automatically: ", error);
        });
    }
});

document.addEventListener("DOMContentLoaded", function() {
    var video = document.getElementById('banner-vid');
    var canvas = document.getElementById('video-canvas');
    var ctx = canvas.getContext('2d');
    var videoContainer = document.querySelector('.video-container');

    video.addEventListener('loadeddata', function() {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    });

    video.oncanplay = function() {
        canvas.style.display = 'none';
        videoContainer.style.display = 'block';
    };
});