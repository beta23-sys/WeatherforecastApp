<?php
session_start();
header('Content-Type: application/json');
include 'db.php'; // our DB connection

// Get JSON or form input
$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

// Simple validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    echo json_encode(['success'=>false, 'message'=>'Invalid email or password too short']);
    exit;
}

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success'=>false, 'message'=>'Email already registered']);
    exit;
}
$stmt->close();

// Hash the password
$hash = password_hash($password, PASSWORD_DEFAULT); 

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (email, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $email, $hash);
if ($stmt->execute()) {
    // Optionally log the user in immediately:
    $_SESSION['user_id'] = $stmt->insert_id;
    $_SESSION['user_email'] = $email;
    echo json_encode(['success'=>true, 'message'=>'Registration successful']);
} else {
    echo json_encode(['success'=>false, 'message'=>'Registration failed']);
}
