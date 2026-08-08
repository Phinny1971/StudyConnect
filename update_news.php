<?php

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'news/news_helper.php';

header('Content-Type: application/json');

try {

    updateEducationNews($conn);

    echo json_encode([
        'success' => true
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        'success' => false
    ]);

}