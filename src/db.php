<?php
// Database connection script
$host = ''; // database host
$db   = '';
$user = '';
$pass = '';

// connect
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    // return JSON on failure
    header('Content-Type: application/json');
    die(json_encode(['success'=>false, 'message'=>'DB Connection error: '.$conn->connect_error]));
}
?>
