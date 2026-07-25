<?php
/******************************************************************************
 * StudyConnect
 *
 * Module  : Security / RBAC
 * Page    : save_user.php
 * Purpose : Save New User
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';

if (!isPost())
{
    $_SESSION['error_message'] = "Invalid request.";
    redirect("users_list.php");
}

$firstName = clean($_POST['first_name'] ?? '');
$lastName = clean($_POST['last_name'] ?? '');
$displayName = clean($_POST['display_name'] ?? '');
$email = strtolower(clean($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$statusId = (int)($_POST['status_id'] ?? 0);

$forcePasswordChange = isset($_POST['force_password_change']) ? 1 : 0;

$errors = [];

if ($firstName == '')
    $errors[] = "First Name is required.";

if ($displayName == '')
    $errors[] = "Display Name is required.";

if ($email == '')
    $errors[] = "Email Address is required.";

if (!filter_var($email, FILTER_VALIDATE_EMAIL))
    $errors[] = "Invalid Email Address.";

if ($password == '')
    $errors[] = "Password is required.";

if ($statusId <= 0)
    $errors[] = "Please select a valid status.";

if (!empty($errors))
{
    $_SESSION['error_message'] = implode("<br>", $errors);

    redirect("user_form.php");
}

$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM users
    WHERE email = ?
");

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0)
{
    $_SESSION['error_message'] = "A user with this email address already exists.";

    redirect("user_form.php");
}

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$conn->begin_transaction();

try
{
	$sql = "INSERT INTO users
(
    email,
    password_hash,
    first_name,
    last_name,
    display_name,
    status_id,
    force_password_change,
    failed_login_attempts,
    created_by_user_id
)

VALUES
(
    ?, ?, ?, ?, ?, ?, ?, 0, ?
)";

$stmt = $conn->prepare($sql);

if(!$stmt)
{
    throw new Exception($conn->error);
}

$createdBy = $_SESSION['user_id'] ?? 1;

$stmt->bind_param(

    "sssssiii",

    $email,
    $passwordHash,
    $firstName,
    $lastName,
    $displayName,
    $statusId,
    $forcePasswordChange,
    $createdBy

);

if(!$stmt->execute())
{
    throw new Exception($stmt->error);
}

$newUserId = $conn->insert_id;

$conn->commit();

$_SESSION['success_message'] =
    "User created successfully.";
	
	redirect("users_list.php");
	
}
catch(Exception $ex)
{
    $conn->rollback();

    $_SESSION['error_message'] =
        $ex->getMessage();

    redirect("user_form.php");
}