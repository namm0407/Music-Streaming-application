<?php
session_start();

// Check if session is active
if (!isset($_SESSION['username'])) {
    http_response_code(401); // Unauthorized
    exit();
}

// Check if session has expired (300 seconds = 5 minutes)
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > 300)) {
    session_destroy();
    http_response_code(401); // Unauthorized
    exit("Session expired. Please log in again.");
}

// Check if music ID is provided
if (!isset($_GET['_id'])) {
    http_response_code(401); // Bad request
    exit();
}

$musid = $_GET['_id'];
$musicZipPath = 'resource_ASS3/Music.zip';
$extractPath = 'resource_ASS3/Music/';

// If Music.zip exists but Music folder doesn't, extract it
if (file_exists($musicZipPath) && !is_dir($extractPath)) {
    $zip = new ZipArchive;
    if ($zip->open($musicZipPath) === TRUE) {
        $zip->extractTo($extractPath); // Extract to "resource_ASS3/Music/"
        $zip->close();
        echo "Music folder extracted successfully!";
    } else {
        die("Failed to open Music.zip");
    }
}

// Connect to database
$db = new mysqli("mydb", "dummy", "c3322b", "db3322");
if ($db->connect_error) {
    die("Connection Error: " . $db->connect_error);
}

// Get music file info
$stmt = $db->prepare("SELECT Path, Filename FROM music WHERE _id = ?");
$stmt->bind_param("s", $musid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401); // Not found
    exit();
}

$music = $result->fetch_assoc();
$stmt->close();

// Update play count
$update_stmt = $db->prepare("UPDATE music SET Pcount = Pcount + 1 WHERE _id = ?");
$update_stmt->bind_param("s", $musid);
$update_stmt->execute();
$update_stmt->close();

$db->close();

// Stream the music file
$file_path = 'resource_ASS3/' . $music['Path'] . '/' . $music['Filename'];
if (!file_exists($file_path)) {
    http_response_code(401); // Not found
    exit();
}

// Set appropriate headers for audio streaming
header('Content-Type: audio/mpeg');
header('Content-Length: ' . filesize($file_path));
header('Content-Disposition: inline; filename="' . $music['Filename'] . '"');
header('X-Content-Type-Options: nosniff');

// Clear output buffer and stream the file
ob_clean();
flush();
readfile($file_path);
exit();
?>
