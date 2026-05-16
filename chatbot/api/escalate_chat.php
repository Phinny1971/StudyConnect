<?php

header('Content-Type: application/json');

require_once '../includes/db.php';
require_once '../includes/auth.php';

$session_id = $_POST['session_id'] ?? 1;

$stmt = $conn->prepare("
    UPDATE magi_chat_sessions
    SET status='escalated'
    WHERE session_id=?
");

$stmt->bind_param("i", $session_id);

$stmt->execute();

echo json_encode([
    'success' => true,
    'message' => 'A counselor will contact you shortly.'
]);

?>