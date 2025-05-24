<?php
session_start();
header('Content-Type: application/json');
if (isset($_SESSION['user_id'])) {
    // Session active
    echo json_encode([
        'logged' => true,
        'user_id' => $_SESSION['user_id'],
        'user_email' => $_SESSION['user_email']
    ]);
} else {
    echo json_encode(['logged' => false]);
}
