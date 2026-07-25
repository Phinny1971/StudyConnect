<?php


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
//$conn = new mysqli($host, $username, $password, $dbname);
$conn = mysqli_connect($host, $user, $password, $database, $port);


if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}