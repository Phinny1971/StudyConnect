<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once 'session_check.php';
requirePermission('student.edit');

require_once 'includes/db_connection.php';
require_once 'includes/access_helper.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Invalid request.');
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
    exit('Invalid request.');
}

try{
		$student_id = isset($_POST['student_id'])
			? (int) $_POST['student_id']
			: 0;

		if ($student_id <= 0) {
			http_response_code(400);
			exit('Invalid student.');
		}

		$university = trim($_POST['university'] ?? '');

		if ($university === '') {
			http_response_code(400);
			exit('University is required.');
		}

		$payment_status = isset($_POST['payment_status'])
			? (int) $_POST['payment_status']
			: 0;
			
		if (!in_array($payment_status, [0, 1], true)) {
			http_response_code(400);
			exit('Invalid payment status.');
		}


			$stmt = $conn->prepare("
				SELECT Branch_name
				FROM studentdetails
				WHERE student_id = ?
			");

			$stmt->bind_param("i", $student_id);
			$stmt->execute();

			$result = $stmt->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				exit('Student not found.');
			}

			$student = $result->fetch_assoc();
			$stmt->close();

			$accessibleBranches = getAccessibleBranches($conn);

			$allowedBranches = array_column(
				$accessibleBranches,
				'Branch_name'
			);

			if (
				!in_array(
					$student['Branch_name'],
					$allowedBranches,
					true
				)
			) {
				http_response_code(403);
				exit('Access denied.');
			}

			//Verify application exists
			$check = $conn->prepare("
				SELECT 1
				FROM coursechoice
				WHERE student_id = ?
				  AND University_Name = ?
			");

			$check->bind_param(
				"is",
				$student_id,
				$university
			);

			$check->execute();

			$result = $check->get_result();

			if ($result->num_rows === 0) {
				http_response_code(404);
				exit('Application not found.');
			}

			$check->close();

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
		
		$result->free();

		$stmt->close();

		$conn->close();

} catch (Exception $e) {

    if (isset($conn)) {
        $conn->close();
    }

    error_log($e->getMessage());

    http_response_code(500);
    exit('Unexpected server error.');
}


?>