<?php

session_start();

if (!isset($_SESSION['student_id'])) {

    http_response_code(401);

    echo json_encode([
        'error' => 'Unauthorized access'
    ]);

    exit;
}

$student_id = $_SESSION['student_id'];

?>