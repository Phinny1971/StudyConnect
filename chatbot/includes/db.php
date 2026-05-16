<?php

$host = "sql101.infinityfree.com"; 
$dbname = "if0_41864403_studyconnect";
$username = "if0_41864403"; 
$password = "Study2025";

/*
$host = "localhost";
$dbname = "studyconnect";
$username = "StudyConnect";
$password = "Study@2025";
*/

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

?>