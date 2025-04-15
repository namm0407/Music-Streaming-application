<?php
session_start();

// Database connection
$dt = new mysqli("mydb", "dummy", "c3322b", "db3322");

if ($dt->connect_error) {
    die("Connection Error: " . $db->connect_error);
}

// Session control
if (!isset($_SESSION['username']) || !isset($_SESSION['login_time']) || (time() - $_SESSION['login_time'] > 300)) {
    http_response_code(401);
    exit();
}

// Handle music streaming
$musid = $_GET['musid'] ?? '';
if (!empty($musid)) {
    $stmt = $dt->prepare("SELECT Path, Filename, Pcount FROM Music WHERE Id = ?");
    $stmt->bind_param("s", $musid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_path = $row['Path'] . "/" . $row['Filename'];
        if (file_exists($file_path)) {
            // Update play count
            $new_pcount = $row['Pcount'] + 1;
            $update_stmt = $dt->prepare("UPDATE Music SET Pcount = ? WHERE Id = ?");
            $update_stmt->bind_param("is", $new_pcount, $musid);
            $update_stmt->execute();
            $update_stmt->close();

            // Stream the file
            header('Content-Type: audio/mpeg');
            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit();
        }
    }
    $stmt->close();
}

// Invalid music ID or file not found
http_response_code(404);
$dt->close();
?>