<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once 'session_check.php';
requirePermission('student.view');

require_once 'includes/db_connection.php';
require_once 'includes/access_helper.php';

$student_id = filter_input(
    INPUT_GET,
    'student_id',
    FILTER_VALIDATE_INT
);

$university = trim($_GET['university'] ?? '');

if (!$student_id || $university === '') {
    http_response_code(400);
    exit('Invalid request.');
}

/* Verify student exists */
$stmt = $conn->prepare("
    SELECT Branch_name
    FROM studentdetails
    WHERE student_id = ?
");

$stmt->bind_param("i", $student_id);
$stmt->execute();

$student = $stmt->get_result()->fetch_assoc();

$stmt->close();

if (!$student) {
    http_response_code(404);
    exit('Student not found.');
}

/* Branch authorization */
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

$stmt = mysqli_prepare($conn, 
    "SELECT * FROM student_messages 
     WHERE student_id = ? AND University_Name = ?
     ORDER BY Message_date DESC");

mysqli_stmt_bind_param($stmt, "is", $student_id, $university);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while($row = mysqli_fetch_assoc($result))
{
    echo '<div class="msg">';

    echo '<div class="meta">';
    echo '<strong>' . htmlspecialchars($row['Mail_from']) . '</strong>';
    echo ' | ';
    echo date('d-M-Y h:i A', strtotime($row['Message_date']));
    echo '</div>';

    if(!empty($row['Mail_message']))
    {
        echo '<div style="margin-top:8px;">';
        echo nl2br(htmlspecialchars($row['Mail_message']));
        echo '</div>';
    }

    /* SOP DOCUMENT */

    if(!empty($row['your_docs']))
    {
        $file = htmlspecialchars($row['your_docs']);
        $fileName = basename($file);

        echo '<div class="doc-actions">';

        echo '<span class="doc-badge">📄 SOP</span> ';
		echo '<strong>' . $fileName . '</strong>';
		echo '<div style="margin-top:5px;">';
		echo '<a href="#" onclick="openDocument(\'' . $file . '\');return false;">👁 View</a>';
		echo ' | ';
		echo '<a href="' . $file . '" download>⬇ Download</a>';
		echo '</div>';
        echo '</div>';
    }

    /* HO DOCUMENT */

    if(!empty($row['ho_docs']))
    {
        $file = htmlspecialchars($row['ho_docs']);
        $fileName = basename($file);

        echo '<div class="doc-actions">';

        echo '<span class="doc-badge">📁 HO</span> ';
        echo '<strong>' . $fileName . '</strong>';
		echo '<div style="margin-top:5px;">';
		echo '<a href="#" onclick="openDocument(\'' . $file . '\');return false;">👁 View</a>';
		echo ' | ';
		echo '<a href="' . $file . '" download>⬇ Download</a>';
		echo '</div>';
        echo '</div>';
    }

    /* PAYMENT LINK */

    if(!empty($row['payment_link']))
    {
        $paymentLink = htmlspecialchars($row['payment_link']);

        echo '<div class="doc-actions">';

        echo '<span class="doc-badge">💳 PAYMENT</span> ';

		echo '<a href="' . $paymentLink . '" target="_blank">';
		echo '🔗 Open Payment Link';
		echo '</a>';

        echo '</div>';
    }

    echo '</div>';
}

mysqli_stmt_close($stmt);
$conn->close();
?>
