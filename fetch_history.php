<?php

header('Content-Type: application/json');

require_once '../includes/db.php';
require_once '../includes/auth.php';

$session_id = $_GET['session_id'] ?? 1;

$stmt = $conn->prepare("
    SELECT sender, message, created_at
    FROM magi_chat_messages
    WHERE session_id = ?
    ORDER BY created_at ASC
");

$stmt->bind_param("i", $session_id);

$stmt->execute();

$result = $stmt->get_result();

$messages = [];

while ($row = $result->fetch_assoc()) {

    $messages[] = [
        'sender' => $row['sender'],
        'message' => nl2br(htmlspecialchars($row['message'])),
        'time' => $row['created_at']
    ];
}

echo json_encode($messages);

?>