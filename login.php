<?php
ob_start();
session_start();

require_once 'includes/helpers.php';
require_once 'includes/db_connection.php';
require_once 'includes/password_helper.php';
require_once 'includes/permission_helper.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	$email = strtolower(trim($_POST['email'] ?? ''));
	$password = $_POST['password'] ?? '';

	$stmt = $conn->prepare("
		SELECT
		u.user_id,
		u.display_name,
		u.email,
		u.password_hash,
		u.status_id,
		u.force_password_change,
		s.status_name
		FROM users u
		INNER JOIN account_statuses s
			ON s.status_id = u.status_id
		WHERE u.email = ?
	");
	
if (!$stmt) {
    die("Database error: " . $conn->error);
}

$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {

    if (strtolower($user['status_name']) !== 'active') {

        $error = "Your account is not active.";

    }
    elseif (verifyPassword($password, $user['password_hash'])) {

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['display_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['status_id'] = $user['status_id'];
		$_SESSION['permissions'] = getUserPermissions($conn, (int)$user['user_id']);
		$_SESSION['roles'] = getUserRoles($conn, (int)$user['user_id'] );
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
		$_SESSION['force_password_change'] = (int)$user['force_password_change'];

		$updateStmt = $conn->prepare("
			UPDATE users
			SET last_login_at = NOW()
			WHERE user_id = ?
		");

		if (!$updateStmt) {
			die("Database error: " . $conn->error);
		}

        $updateStmt->bind_param("i", $user['user_id']);
		$updateStmt->execute();
		$updateStmt->close();

		$stmt->close();
		$conn->close();

        if ($_SESSION['force_password_change'] === 1)
		{
			header("Location: change_password.php");
			exit;
		}

		redirect('main.php');
		exit;

    }
    else {

        $error = "Invalid email or password.";

    }

}
else {

    $error = "Invalid email or password.";

}

$stmt->close();
$conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudyConnect Login</title>
    <link rel="stylesheet" href="css/style.css">
	<style>
	.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 4px;
}
	</style>
	
</head>
<body class="login-body">

<div class="login-container">

    <div class="login-left">
        <img src="images/SC-Logo.png.webp" class="login-logo">

        <h1>Student Management System</h1>
        <p>
            Manage students, applications, universities and communication
            efficiently.
        </p>

        <img
            src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=800"
            class="login-image">
    </div>

	<?php include 'modal.php'; ?>

	<?php if (isset($_GET['expired'])) { ?>
	<script>
	document.addEventListener("DOMContentLoaded", function() {

		showModal({
			title: "Session Expired",
			message: "Your session expired due to inactivity. Please login again.",
			showOk: true
		});

	});
	</script>
	<?php } ?>




    <div class="login-right">

        <form method="POST" class="login-form">

            <h2>Login</h2>

            <?php if(isset($error)) { ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php } ?>

            <input type="email"
                   name="email"
                   placeholder="Email"
                   required>

            <input type="password"
                   name="password"
                   placeholder="Password"
                   required>

            <button type="submit">Login</button>

			<div class="login-help">
				Please sign in using your StudyConnect account.
			</div>

        </form>

    </div>

</div>

<?php if (isset($_GET['expired'])) : ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    showModal({
        title: 'Session Expired',
        message: 'Your session expired due to inactivity. Please login again.',
        showOk: true
    });
});
</script>
<?php endif; ?>

</body>
</html>