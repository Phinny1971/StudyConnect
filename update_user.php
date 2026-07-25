<?php

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';

if (!isPost())
{
    $_SESSION['error_message'] = "Invalid request.";

    redirect("users_list.php");
}

$userId = (int)($_POST['user_id'] ?? 0);

$firstName = clean($_POST['first_name'] ?? '');
$lastName = clean($_POST['last_name'] ?? '');
$displayName = clean($_POST['display_name'] ?? '');
$statusId = (int)($_POST['status_id'] ?? 0);

$errors = [];

if($userId <= 0)
{
    $errors[] = "Invalid user.";
}

if($firstName == '')
{
    $errors[] = "First Name is required.";
}

if($displayName == '')
{
    $errors[] = "Display Name is required.";
}

if($statusId <= 0)
{
    $errors[] = "Status is required.";
}

if(!empty($errors))
{
    $_SESSION['error_message'] = implode("<br>", $errors);

    redirect("users_list.php");
}

$conn->begin_transaction();

try
{
    $updatedBy = $_SESSION['user_id'] ?? 1;

    $sql = "

    UPDATE users

    SET

        first_name = ?,
        last_name = ?,
        display_name = ?,
        status_id = ?,
        updated_by_user_id = ?,
        updated_on = NOW()

    WHERE user_id = ?";

    $stmt = $conn->prepare($sql);

    if(!$stmt)
    {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(

        "sssiii",

        $firstName,
        $lastName,
        $displayName,
        $statusId,
        $updatedBy,
        $userId
    );

    if(!$stmt->execute())
    {
        throw new Exception($stmt->error);
    }

    $conn->commit();

    $_SESSION['success_message'] =
        "User updated successfully.";

    redirect("users_list.php");
}
catch(Exception $ex)
{
    $conn->rollback();

    $_SESSION['error_message'] =
        $ex->getMessage();

    redirect("users_list.php");
}

