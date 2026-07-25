<?php
require_once 'session_check.php';
requirePermission('student.delete');
require_once 'includes/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {
    http_response_code(403);
    exit('Invalid request');
}


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



function deleteStudentFiles($conn, $student_id)
{
    $filePaths = [];

    // studentdetails
    $stmt = $conn->prepare("
        SELECT
            Passport_Upload,
            cert_10th,
            cert_intermediate,
            cert_degree,
            cert_pg,
            cert_diploma,
            cert_other,
            Exp1_Cert,
            Exp2_Cert,
            Exp3_Cert
        FROM studentdetails
        WHERE student_id = ?
    ");

    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    if ($row = $stmt->get_result()->fetch_assoc()) {
        $filePaths = array_merge(
            $filePaths,
            array_filter($row)
        );
    }

    $stmt->close();

    // studentlanguagetests
    $stmt = $conn->prepare("
        SELECT
            ENGOTHER_UPLOAD,
            APTOTHER_UPLOAD,
            GMAT_UPLOAD,
            SAT_UPLOAD,
            GRE_UPLOAD,
            DULINGO_UPLOAD,
            LANGCERT_UPLOAD,
            IELTS_UPLOAD,
            PTE_UPLOAD,
            TOEFL_UPLOAD
        FROM studentlanguagetests
        WHERE student_id = ?
    ");

    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    if ($row = $stmt->get_result()->fetch_assoc()) {
        $filePaths = array_merge(
            $filePaths,
            array_filter($row)
        );
    }

    $stmt->close();

    // studentotherdetails
    $stmt = $conn->prepare("
        SELECT
            lor1,
            lor2,
            lor3,
            moi,
            resume,
            otherdoc,
            immi_country_file,
            medical_cond_file,
            visa_refusal_file,
            convicted_offence_file,
            explor1,
            explor2,
            explor3
        FROM studentotherdetails
        WHERE student_id = ?
    ");

    $stmt->bind_param("i", $student_id);
    $stmt->execute();

    if ($row = $stmt->get_result()->fetch_assoc()) {
        $filePaths = array_merge(
            $filePaths,
            array_filter($row)
        );
    }

    $stmt->close();

    return array_unique($filePaths);
}

$filePaths = deleteStudentFiles($conn, $student_id);

$conn->begin_transaction();

try {

    // Delete from child tables first
    $stmt1 = $conn->prepare("DELETE FROM studentlanguagetests WHERE student_id = ?");
    $stmt1->bind_param("i", $student_id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM coursechoice WHERE student_id = ?");
    $stmt2->bind_param("i", $student_id);
    $stmt2->execute();

	$stmtOther = $conn->prepare("DELETE FROM studentotherdetails WHERE student_id = ?");
	$stmtOther->bind_param("i", $student_id);
	$stmtOther->execute();

    // Delete main table
    $stmt3 = $conn->prepare("DELETE FROM studentdetails WHERE student_id = ?");
    $stmt3->bind_param("i", $student_id);
    $stmt3->execute();

    // Optional: check if anything was deleted
    if ($stmt3->affected_rows === 0) {
        throw new Exception("Student not found");
    }

	$stmt1->close();
	$stmt2->close();
	$stmtOther->close();
	$stmt3->close();
	
    $conn->commit();
	
		foreach ($filePaths as $file) {

		if (empty($file)) {
			continue;
		}

		$realFile = realpath($file);
		$realUploads = realpath('uploads');

		if (
			$realFile &&
			$realUploads &&
			strpos($realFile, $realUploads) === 0 &&
			file_exists($realFile)
			) {
			
			if (!@unlink($realFile)) {
				error_log("Could not delete file: " . $realFile);
			}
			}
		}

    echo "success";

} catch (Exception $e) {
    $conn->rollback();
    echo "Error: " . $e->getMessage();
}

$conn->close();
?>
