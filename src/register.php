<?php
session_start();
header('Content-Type: application/json');

// include DB
require_once __DIR__ . '/db.php';

// read JSON body
$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);

$email    = isset($data['email'])    ? trim($data['email'])    : '';
$password = isset($data['password']) ? $data['password']       : '';

// validate
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    echo json_encode(['success'=>false,'message'=>'Invalid email or password too short']);
    exit;
}

// check existing
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success'=>false,'message'=>'Email already registered']);
    $stmt->close();
    exit;
}
$stmt->close();

// insert raw password
$stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $password);

if ($stmt->execute()) {
    // auto-login
    $_SESSION['user_email'] = $email;
    echo json_encode(['success'=>true,'message'=>'Registration successful']);
} else {
    echo json_encode(['success'=>false,'message'=>'Registration failed']);
}
$stmt->close();
$conn->close();
?>