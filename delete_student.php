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
  die("Connection failed: " . $conn->connect_error);
}

$uploadDir = "uploads/";
if (!file_exists($uploadDir)) {
  mkdir($uploadDir, 0777, true);
}

if (!isset($_POST['delete_id'])) {
    echo "Invalid request";
    exit;
}

$student_id = intval($_POST['delete_id']);

if ($student_id <= 0) {
    echo "Invalid ID";
    exit;
}

$conn->begin_transaction();

try {

    // Delete from child tables first
    $stmt1 = $conn->prepare("DELETE FROM studentlanguagetests WHERE student_id = ?");
    $stmt1->bind_param("i", $student_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM coursechoice WHERE student_id = ?");
    $stmt2->bind_param("i", $student_id);
    $stmt2->execute();

    // Delete main table
    $stmt3 = $conn->prepare("DELETE FROM studentdetails WHERE student_id = ?");
    $stmt3->bind_param("i", $student_id);
    $stmt3->execute();

    // Optional: check if anything was deleted
    if ($stmt3->affected_rows === 0) {
        throw new Exception("Student not found");
    }

    $conn->commit();
    echo "success";

} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>