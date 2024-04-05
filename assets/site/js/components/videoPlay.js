document.addEventListener('DOMContentLoaded', function() {
    let video = document.getElementById('banner-vid');

    if (video) {
        video.play().catch(error => {
            console.error("Video cannot be played automatically: ", error);
        });
    }
});