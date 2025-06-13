<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$email    = isset($data['email'])    ? trim($data['email'])    : '';
$password = isset($data['password']) ? $data['password']       : '';

// basic check
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    echo json_encode(['success'=>false,'message'=>'Missing credentials']);
    exit;
}

// fetch stored password
$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if ($password === $row['password']) {
        $_SESSION['user_email'] = $email;
        echo json_encode(['success'=>true,'user_email'=>$email]);
    } else {
        echo json_encode(['success'=>false,'message'=>'Incorrect password']);
    }
} else {
    echo json_encode(['success'=>false,'message'=>'Email not found']);
}

$stmt->close();
$conn->close();
?>