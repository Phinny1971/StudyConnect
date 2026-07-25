<?php
ob_start();
require_once 'session_check.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
/*
if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}
*/


?>

<!DOCTYPE html>
<html>

<head>
    <title>StudyConnect Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<?php include 'header.php'; ?>

<div class="main-layout">

    <?php include 'sidebar.php'; ?>

    <div class="content-area">

        <iframe
			name="contentFrame"
			id="contentFrame"
			src="dashboard.php"
			frameborder="0"
			loading="lazy">
		</iframe>

    </div>

</div>

</body>
</html>