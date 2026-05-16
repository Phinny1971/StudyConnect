<?php

header('Content-Type: application/json');

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

$data = json_decode(file_get_contents("php://input"), true);

$session_id = $data['session_id'] ?? 1;

$sender = cleanInput($data['sender']);

$message = cleanInput($data['message']);

if(empty($message)){

    echo json_encode([
        'success' => false,
        'message' => 'Empty message'
    ]);

    exit;
}

$stmt = $conn->prepare("
    INSERT INTO magi_chat_messages
    (session_id, sender, message)
    VALUES (?, ?, ?)
");

$stmt->bind_param(
    "iss",
    $session_id,
    $sender,
    $message
);

$stmt->execute();

echo json_encode([
    'success' => true
]);

?>