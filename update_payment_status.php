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

if($conn->connect_error)
{
    die("Connection failed");
}

$student_id =
    $_POST['student_id'] ?? '';

$university =
    $_POST['university'] ?? '';

$payment_status =
    $_POST['payment_status'] ?? 0;

$stmt = $conn->prepare(

    "UPDATE coursechoice

     SET Payment_Status = ?

     WHERE student_id = ?
     AND University_Name = ?"

);

$stmt->bind_param(

    "iis",

    $payment_status,

    $student_id,

    $university

);

$stmt->execute();

echo "success";

$stmt->close();

$conn->close();

?>