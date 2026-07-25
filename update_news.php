<?php

require_once 'session_check.php';
//require_once 'news_helper.php';
require_once 'news/news_helper.php';

$host = "localhost";
$dbname = "studyconnect";
$username = "StudyConnect";
$password = "Study@2025";

$conn = new mysqli(
    $host,
    $username,
    $password,
    $dbname
);

if (!$conn->connect_error) {
    updateEducationNews($conn);
}

$conn->close();

echo json_encode([
    'success' => true
]);