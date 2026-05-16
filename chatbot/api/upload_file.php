<?php

header('Content-Type: application/json');

require_once '../includes/db.php';
require_once '../includes/auth.php';
require_once '../includes/helpers.php';

if (!isset($_FILES['file'])) {

    echo json_encode([
        'success' => false,
        'message' => 'No file uploaded'
    ]);

    exit;
}

$file = $_FILES['file'];

if ($file['error'] !== 0) {

    echo json_encode([
        'success' => false,
        'message' => 'Upload failed'
    ]);

    exit;
}

if (!allowedFile($file['name'])) {

    echo json_encode([
        'success' => false,
        'message' => 'Invalid file type'
    ]);

    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {

    echo json_encode([
        'success' => false,
        'message' => 'File too large'
    ]);

    exit;
}

$uploadDir = "../uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$newFileName =
    time() . "_" .
    preg_replace("/[^a-zA-Z0-9._-]/", "", $file['name']);

$targetPath = $uploadDir . $newFileName;

move_uploaded_file($file['tmp_name'], $targetPath);

$stmt = $conn->prepare("
    INSERT INTO magi_uploaded_files
    (session_id, uploaded_by, file_name, file_path)
    VALUES (?, ?, ?, ?)
");

$session_id = 1;
$uploaded_by = 'user';

$stmt->bind_param(
    "isss",
    $session_id,
    $uploaded_by,
    $newFileName,
    $targetPath
);

$stmt->execute();

echo json_encode([
    'success' => true,
    'file' => $newFileName
]);

?>