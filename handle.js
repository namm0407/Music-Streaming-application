document.addEventListener('DOMContentLoaded', function() {
    // Handle genre button clicks
    const genreButtons = document.querySelectorAll('.genre-buttons button');
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.querySelector('.search-bar form');

    genreButtons.forEach(button => {
        button.addEventListener('click', function() {
            const genre = this.getAttribute('data-genre');
            // Set the search input value to the selected genre
            searchInput.value = genre;
            // Submit the form
            searchForm.submit();
        });
    });

    // Handle audio player functionality
    const audioPlayers = document.querySelectorAll('.audio-player');
    const playButtons = document.querySelectorAll('.music-item img[alt="Play"]');

    playButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            const audioPlayer = audioPlayers[index];
            const musicId = audioPlayer.getAttribute('data-musid');
            
            if (audioPlayer.paused) {
                // Pause all other audio players
                audioPlayers.forEach(player => {
                    if (player !== audioPlayer) {
                        player.pause();
                        player.currentTime = 0;
                        // Reset the play button icon
                        const correspondingButton = player.closest('.music-item').querySelector('img[alt="Play"]');
                        correspondingButton.src = 'resource_ASS3/play.png';
                    }
                });
                
                // Play the selected audio
                audioPlayer.src = `getmusic.php?id=${musicId}`;
                audioPlayer.play();
                button.src = 'resource_ASS3/pause.png';
            } else {
                // Pause the audio
                audioPlayer.pause();
                audioPlayer.currentTime = 0;
                button.src = 'resource_ASS3/play.png';
            }
        });
    });

    // Update play button icons when audio ends
    audioPlayers.forEach(audio => {
        audio.addEventListener('ended', function() {
            const button = this.closest('.music-item').querySelector('img[alt="Play"]');
            button.src = 'resource_ASS3/play.png';
        });
    });
});
