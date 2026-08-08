<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once 'session_check.php';
requirePermission('student.view');

require_once 'includes/db_connection.php';
require_once 'includes/access_helper.php';
require_once 'includes/student_helper.php';

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


    //$student_id = $_POST['student_id'] ?? '';
	
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

	$message = trim($_POST['message'] ?? '');
	if ($message === '') {
    http_response_code(400);
    exit('Message is required.');
	}

	$payment_link = trim($_POST['payment_link'] ?? '');
	
    //$mail_from = "phinny@gmail.com";
	$mail_from = $_SESSION['email'] ?? '';

	if ($mail_from === '') {
		http_response_code(401);
		exit('Session expired.');
	}

	// Validate student exists
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

	// Verify branch authorization
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
	
	// Verify the university belongs to this student
	$stmt = $conn->prepare("
		SELECT 1
		FROM coursechoice
		WHERE student_id = ?
		  AND University_Name = ?
		LIMIT 1
	");

	$stmt->bind_param(
		"is",
		$student_id,
		$university
	);

	$stmt->execute();

	$result = $stmt->get_result();

	if ($result->num_rows === 0) {
		$stmt->close();
		http_response_code(400);
		exit('Invalid university selected.');
	}

	$stmt->close();

    $your_docs = '';
    $ho_docs = '';


	 $your_docs = uploadFile(
			'your_docs',
			'',
			'uploads/sop_docs'
		);

		$ho_docs = uploadFile(
			'ho_docs',
			'',
			'uploads/ho_docs'
		);

	if (
		$payment_link !== '' &&
		!filter_var($payment_link, FILTER_VALIDATE_URL)
	) {
		http_response_code(400);
		exit('Invalid payment link.');
	}
	
    $sql = "
        INSERT INTO student_messages
        (
            Mail_from,
            student_id,
            Mail_message,
            Message_date,
            University_Name,
            your_docs,
            ho_docs,
            payment_link
        )
        VALUES
        (
            ?,
            ?,
            ?,
            NOW(),
            ?,
            ?,
            ?,
            ?
        )
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sisssss",
        $mail_from,
        $student_id,
        $message,
        $university,
        $your_docs,
        $ho_docs,
        $payment_link
    );

    $stmt->execute();

    echo "success";
	
	$stmt->close();
	$conn->close();

?>

