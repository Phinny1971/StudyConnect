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

$student_id = $_GET['student_id'];
$university = $_GET['university'];

$stmt = mysqli_prepare($conn, 
    "SELECT * FROM student_messages 
     WHERE student_id = ? AND University_Name = ?
     ORDER BY Message_date DESC");

mysqli_stmt_bind_param($stmt, "is", $student_id, $university);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_assoc($result)) {

    echo '<div class="msg">';
    echo '<div class="meta">';
    echo 'From: ' . htmlspecialchars($row['Mail_from']) . '<br>';
    echo 'Date: ' . $row['Message_date'];
    echo '</div>';
    echo '<p>' . nl2br(htmlspecialchars($row['Mail_message'])) . '</p>';
    echo '</div>';
}
?>
