document.addEventListener('DOMContentLoaded', function() {
    // Handle genre button clicks
    const genreButtons = document.querySelectorAll('.genre-buttons button');
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.querySelector('.search-bar form');

    genreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const genre = this.getAttribute('data-genre');
            searchInput.value = genre;
            searchForm.submit();
        });
    });

    // Get all music items
    const musicItems = document.querySelectorAll('.music-item');
    
    musicItems.forEach(item => {
        const audio = item.querySelector('.myAudio');
        const playImage = item.querySelector('.playImage');
        const pauseImage = item.querySelector('.pauseImage');
        
        // Pause all other audio when one plays
        audio.addEventListener('play', () => {
            document.querySelectorAll('.myAudio').forEach(otherAudio => {
                if (otherAudio !== audio) {
                    otherAudio.pause();
                    // Reset play/pause images for other items
                    const otherItem = otherAudio.closest('.music-item');
                    otherItem.querySelector('.playImage').hidden = false;
                    otherItem.querySelector('.pauseImage').hidden = true;
                }
            });
        });
        
        playImage.addEventListener('click', () => {
            audio.play();
            playImage.hidden = true;
            pauseImage.hidden = false;
        });

        pauseImage.addEventListener('click', () => {
            audio.pause();
            playImage.hidden = false;
            pauseImage.hidden = true;
        });
        
        // When audio ends, reset to play image
        audio.addEventListener('ended', () => {
            playImage.hidden = false;
            pauseImage.hidden = true;
        });
    });
});
