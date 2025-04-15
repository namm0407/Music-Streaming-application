document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEvdocument.addEventListener('DOMContentLoaded', () => {
            // Form validation
            const loginForm = document.getElementById('login-form');
            if (loginForm) {
                loginForm.addEventListener('submit', (e) => {
                    const username = document.getElementById('username').value.trim();
                    const password = document.getElementById('password').value.trim();
                    if (!username) {
                        e.preventDefault();
                        alert('Please enter a username');
                        return;
                    }
                    if (!password) {
                        e.preventDefault();
                        alert('Please enter a password');
                        return;
                    }
                });
            }
        
            // Session timeout redirect
            if (document.querySelector('.music-container')) {
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 300000); // 300 seconds = 300,000 milliseconds
            }
        
            // Search functionality
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        const genre = searchInput.value.trim();
                        if (genre) {
                            searchGenre(genre);
                            searchInput.value = '';
                        }
                    }
                });
            }
        
            // Music playback
            const playButtons = document.querySelectorAll('.play-btn');
            const audioElements = document.querySelectorAll('.audio-player');
            let currentAudio = null;
        
            playButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const musid = button.dataset.musid;
                    const audio = document.querySelector(`audio[data-musid="${musid}"]`);
                    const isPaused = audio.paused && audio.currentTime > 0 && !audio.ended;
        
                    if (currentAudio && currentAudio !== audio) {
                        currentAudio.pause();
                        const prevButton = document.querySelector(`button[data-musid="${currentAudio.dataset.musid}"]`);
                        prevButton.innerHTML = '<img src="play.png" alt="Play">';
                    }
        
                    if (isPaused || audio.ended || !audio.src) {
                        if (!audio.src) {
                            audio.src = `file.php?musid=${musid}`;
                            audio.load();
                        }
                        audio.play().catch(error => {
                            if (error.message.includes('401')) {
                                alert('Session expired!!');
                                window.location.href = 'index.php';
                            }
                        });
                        button.innerHTML = '<img src="pause.png" alt="Pause">';
                        currentAudio = audio;
                        alert('Playing music');
                    } else if (!audio.paused) {
                        audio.pause();
                        button.innerHTML = '<img src="play.png" alt="Play">';
                        alert('Music paused');
                    } else {
                        audio.play();
                        button.innerHTML = '<img src="pause.png" alt="Pause">';
                        alert('Resuming music');
                    }
                });
            });
        
            audioElements.forEach(audio => {
                audio.addEventListener('ended', () => {
                    const button = document.querySelector(`button[data-musid="${audio.dataset.musid}"]`);
                    button.innerHTML = '<img src="play.png" alt="Play">';
                    audio.src = '';
                    currentAudio = null;
                });
            });
        });
        
        function searchGenre(genre) {
            window.location.href = genre ? `index.php?search=${encodeURIComponent(genre)}` : 'index.php';
        }entListener('submit', function(e) {
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            
            if (!username.value.trim()) {
                e.preventDefault();
                alert('Missing username!');
                username.focus();
                return;
            }
            
            if (!password.value.trim()) {
                e.preventDefault();
                alert('Missing password!');
                password.focus();
                return;
            }
        });
    }
});
