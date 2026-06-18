<?php

require_once 'session_check.php';
//require_once 'news_helper.php';
require_once 'news/news_helper.php';

$host = getenv('MYSQLHOST');
$user = getenv('MYSQLUSER');
$password = getenv('MYSQLPASSWORD');
$database = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

/*
$host = "localhost";
$dbname = "studyconnect";
$username = "StudyConnect";
$password = "Study@2025";
*/

$conn = mysqli_connect($host, $user, $password, $database, $port);
//$conn = new mysqli($host, $username, $password, $dbname);

/*
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
*/

if (!$conn->connect_error) {
    updateEducationNews($conn);
}

$conn->close();

echo json_encode([
    'success' => true
]);