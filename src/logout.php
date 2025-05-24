<?php
session_start();
session_unset();
session_destroy();
header('Content-Type: application/json');
// Clear session cookie (optional, PHP default does this on session_destroy)
echo json_encode(['success'=>true]);
