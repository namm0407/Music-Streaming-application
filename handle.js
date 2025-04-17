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

    // Handle audio player functionality
    const audioPlayers = document.querySelectorAll('.audio-player');
    const playButtons = document.querySelectorAll('.music-item img[alt="Play"]');

    // Add event listeners to handle when audio finishes playing
    audioPlayers.forEach(player => {
        player.addEventListener('ended', function() {
            // Reset the play button icon when audio finishes naturally
            const correspondingButton = this.closest('.music-item').querySelector('img[alt="Play"]');
            correspondingButton.src = 'resource_ASS3/play.png';
        });
    });

    playButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            const audioPlayer = audioPlayers[index];
            
            // Check if the audio is currently playing (not paused and not ended)
            if (audioPlayer.paused || audioPlayer.ended) {
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
});
