<?php
session_start();

// Database connection
$db = new mysqli("mydb", "dummy", "c3322b", "db3322");
if ($db->connect_error) {
    http_response_code(500);
    die("Connection Error: " . $db->connect_error);
}

// Session control
\$session_expired = false;
$auth_failed = false;
$notification = "";
if (isset($_SESSION['username']) && isset($_SESSION['login_time'])) {
    if (time() - $_SESSION['login_time'] > 5) {
        session_destroy();
        session_start();
        $session_expired = true;
        $notification = "Session expired!!";
    }
} else {
    unset($_SESSION['username']);
}

// Check if musid is provided
if (!isset($_GET['musid']) || empty($_GET['musid'])) {
    http_response_code(400);
    die("Missing music ID");
}

$musid = $_GET['musid'];

// Prepare and execute query to get music file info
$stmt = $db->prepare("SELECT Path, Filename FROM Music WHERE _id = ?");
$stmt->bind_param("s", $musid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    die("Music not found");
}

$music = $result->fetch_assoc();
$filePath = $music['Path'] . '/' . $music['Filename'];

// Verify file exists
if (!file_exists($filePath)) {
    http_response_code(404);
    die("File not found");
}

// Update play count
$updateStmt = $db->prepare("UPDATE Music SET Pcount = Pcount + 1 WHERE _id = ?");
$updateStmt->bind_param("s", $musid);
$updateStmt->execute();
$updateStmt->close();

// Stream the audio file
header('Content-Type: audio/mpeg');
header('Content-Length: ' . filesize($filePath));
header('Content-Disposition: inline; filename="' . $music['Filename'] . '"');
header('Cache-Control: no-cache');
header('Accept-Ranges: bytes');

// Read and output the file
readfile($filePath);

$stmt->close();
$db->close();
?>
