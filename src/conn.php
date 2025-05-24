<?php
// db.php – include in other scripts
$host = 'localhost';
$db   = 'your_database';
$user = 'db_user';
$pass = 'db_pass';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(['success'=>false, 'message'=>'DB Connection error']));
}
?>
