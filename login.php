<?php
ob_start();
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];

    // TEMP LOGIN
    if ($email == 'admin@studyconnect.com' && $password == 'admin123') {

        $_SESSION['user_name'] = 'Administrator';
        $_SESSION['email'] = $email;

        header('Location: main.php');
        exit;
    }

    $error = "Invalid Login";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudyConnect Login</title>
    <link rel="stylesheet" href="css/style.css">
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

            <div class="demo-login">
                Demo Login:<br>
                admin@studyconnect.com / admin123
            </div>

        </form>

    </div>

</div>

</body>
</html>
