<?php

session_start();

$dt = new mysqli("mydb", "dummy", "c3322b", "db3322");

if ($dt->connect_error) {
    die("Connection Error: " . $db->connect_error);
}

// Session control
$session_expired = false;
$auth_failed = false;
$notification = ""; 
if (isset($_SESSION['username']) && isset($_SESSION['login_time'])) {
    if (time() - $_SESSION['login_time'] > 300) {
        session_destroy();
        session_start();
        $session_expired = true;
        $notification = "Session expired!!";
    }
} else {
    unset($_SESSION['username']);
}

// Handle POST login request
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (!empty($username) && !empty($password)) {
        // Check if username exists
        $stmt = $dt->prepare("SELECT username, password FROM account WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            // Username exists, check password
            $user = $result->fetch_assoc();
            if ($user['password'] === $password) {
                // Successful login
                $_SESSION['username'] = $username;
                $_SESSION['login_time'] = time();
                header("Location: index.php");
                exit();
            } else {
                // Incorrect password
                $auth_failed = true;
                $notification = "Incorrect password!";
            }
        } else {
            // Username does not exist
            $auth_failed = true;
            $notification = "No such user!";
        }
        $stmt->close();
    }
}

// Handle search request
$search_term = $_GET['search'] ?? '';
$music_list = [];
$list_title = "Top 8 Popular Music";
$empty = "";
if ($session_expired || !isset($_SESSION['username'])) {
    // Show login page
} else {
    //$list_title = "All music under " . htmlspecialchars($search_term);
    if (!empty($search_term)) {
        $list_title = "All music under " . htmlspecialchars($search_term);
        $stmt = $dt->prepare("SELECT _id, Title, Artist, Length, License, Path, Filename, Pcount, Tags FROM music WHERE Tags LIKE ? ORDER BY Pcount DESC LIMIT 8");
        $like_term = "%" . $search_term . "%";
        $stmt->bind_param("s", $like_term);
    } else {
        $stmt = $dt->prepare("SELECT _id, Title, Artist, Length, License, Path, Filename, Pcount, Tags FROM music ORDER BY Pcount DESC LIMIT 8");
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $music_list[] = $row;
    }
    $stmt->close();
    if (empty($music_list) && !empty($search_term)) {
        //$list_title = "All music under " . htmlspecialchars($search_term);
        $empty = "No music found under this genre (" . htmlspecialchars($search_term) . ")";
    }
}

$musicZipPath = 'resource_ASS3/Music.zip';
$extractPath = 'resource_ASS3/Music/';

// If Music.zip exists but Music folder doesn't, extract it
if (file_exists($musicZipPath) && !is_dir($extractPath)) {
    // Create the directory if it doesn't exist
    if (!file_exists($extractPath)) {
        mkdir($extractPath, 0777, true);
    }
    
    // Use shell command to unzip (requires unzip command to be available)
    $command = "unzip -q ".escapeshellarg($musicZipPath)." -d ".escapeshellarg($extractPath);
    exec($command, $output, $return_var);
    
    if ($return_var === 0) {
        echo "Music folder extracted successfully!";
    } else {
        die("Failed to extract Music.zip");
    }
}
$dt->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>2024-25 3322B Assignment Three</title>
    <link rel="stylesheet" type="text/css" href="look.css"/>
    <script src="handle.js"></script>

</head>
<body>
    <header>
        <h1>3322 Royalty Free Music</h1>
        <p>(Source: <a href="https://www.chosic.com/free-music/all/" target="_blank">https://www.chosic.com/free-music/all/</a>)</p>
    </header>

    <?php if (!isset($_SESSION['username']) || $session_expired): ?>
        <div class="login-form"> 
            <form id="login-form" method="POST" action="index.php">
            <fieldset name="logininfo"> 
                <legend>LOG IN</legend> 
                <div class="input-container">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required oninvalid="this.setCustomValidity('Missing username!!')" oninput="this.setCustomValidity('')">
                </div>
                <div class="input-container">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required oninvalid="this.setCustomValidity('Password is missing!!')" oninput="this.setCustomValidity('')">
                </div>
                <div class="button-container">
                    <button type="submit">Log in</button>
                </div>   
            </fieldset> 
            <?php if ($auth_failed || $session_expired): ?>
                <p class="error"><?php echo htmlspecialchars($notification); ?></p>
            <?php endif; ?>   
            </form>
        </div>  
    
    <?php else: ?>
        <!-- Music Interface -->
        <div class="music-container">
            <div class="search-bar">
                <form method="GET" action="index.php">
                    Search <input type="text" name="search" id="searchInput" placeholder="Search for genre" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                </form>
            </div>
            <div class="genre-buttons">
                <button data-genre="Cinematic">Cinematic</button>
                <button data-genre="Games">Games</button>
                <button data-genre="Romantic">Romantic</button>
                <button data-genre="Study">Study</button>
                <button data-genre="Popular">Popular</button>
            </div>
            <h2><?php echo htmlspecialchars($list_title); ?></h2>
            <p><?php echo htmlspecialchars($empty); ?></p>
            <div class="music-list"> 
                <?php foreach ($music_list as $music): ?>
                    <div class="music-item">
                        <div class="music-info">
                            <div class="one">
                                <audio class="myAudio">
                                    <source src="resource_ASS3/<?php echo htmlspecialchars($music['Path']); ?>/<?php echo htmlspecialchars($music['Path']); ?>/<?php echo htmlspecialchars($music['Filename']); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>

                                <img class="playImage" src="resource_ASS3/play.png" alt="Play" style="width: 50px; cursor: pointer;">
                                <img class="pauseImage" src="resource_ASS3/pause.png" alt="Pause" style="width: 50px; cursor:pointer;" hidden>

                                <p><strong><?php echo htmlspecialchars($music['Title']); ?></strong> <br> <?php echo htmlspecialchars($music['Artist']); ?></p>
                            </div>
                            <div class="two">
                                <p><?php echo htmlspecialchars($music['Length']); ?></p>
                            </div>
                            <div class="three">
                                <p>
                                    <img src="resource_ASS3/CC4.png" alt="License" class="icon"> 
                                    <img src="resource_ASS3/count.png" alt="Play count" class="icon"> <?php echo htmlspecialchars($music['Pcount']); ?>
                                </p>
                            </div>
                            <div class="four">
                                <p><?php echo htmlspecialchars($music['Tags']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <script src="handle.js"></script>
</body>
</html>
