<?php
session_start();
header('Content-Type: application/json');
include 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    echo json_encode(['success'=>false, 'message'=>'Missing credentials']);
    exit;
}

// Lookup user by email
$stmt = $conn->prepare("SELECT id, password_hash FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    // Verify password
    if (password_verify($password, $row['password_hash'])) {
        // Login success: set session
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['user_email'] = $email;
        echo json_encode(['success'=>true, 'user_email'=>$email]);
    } else {
        echo json_encode(['success'=>false, 'message'=>'Incorrect password']);
    }
} else {
    echo json_encode(['success'=>false, 'message'=>'Email not found']);
}
