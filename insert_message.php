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

// Create connection
$conn = mysqli_connect($host, $user, $password, $database, $port);
//$conn = new mysqli($host, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST['student_id'];
    $university = $_POST['university'];
    $message = $_POST['message'];

    //$mail_from = $_SESSION['email']; // logged-in user
	//TEMPORARY
	$mail_from = "phinny@gmail.com";

    $stmt = mysqli_prepare($conn, 
        "INSERT INTO student_messages 
        (Mail_from, student_id, Mail_message, Message_date, University_Name)
        VALUES (?, ?, ?, NOW(), ?)");

    mysqli_stmt_bind_param($stmt, "siss", $mail_from, $student_id, $message, $university);
    mysqli_stmt_execute($stmt);

    echo "success";
}
?>
