<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Role Management
 * Page     : submit_role.php
 * Purpose  : Save a new Role
 *
 * Version  : 1.0
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';

require_once 'includes/helpers.php';
require_once 'includes/permission_helper.php';
require_once 'includes/role_helper.php';


/*----------------------------------------------------------
    Administrator Only
----------------------------------------------------------*/

if (!isAdministrator())
{
    denyAccess();
}


/*----------------------------------------------------------
    POST Request Only
----------------------------------------------------------*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    redirect('role_list.php');
}


/*----------------------------------------------------------
    Validate CSRF
----------------------------------------------------------*/

if (!validateCsrfToken($_POST['csrf_token'] ?? ''))
{
    $_SESSION['error_message'] =
        "Invalid request. Please refresh the page and try again.";

    redirect('role_list.php');
}


/*----------------------------------------------------------
    Read Form
----------------------------------------------------------*/

$roleName    = clean($_POST['role_name'] ?? '');
$description = clean($_POST['description'] ?? '');

$isActive = isset($_POST['is_active']) ? 1 : 0;


/*----------------------------------------------------------
    Validation
----------------------------------------------------------*/

if ($roleName === '')
{
    $_SESSION['error_message'] = "Role Name is required.";

    redirect('role_form.php');
}

if (strlen($roleName) > 100)
{
    $_SESSION['error_message'] =
        "Role Name cannot exceed 100 characters.";

    redirect('role_form.php');
}

if (strlen($description) > 255)
{
    $_SESSION['error_message'] =
        "Description cannot exceed 255 characters.";

    redirect('role_form.php');
}


/*----------------------------------------------------------
    Duplicate Role Check
----------------------------------------------------------*/

if (roleExists($conn, $roleName))
{
    $_SESSION['error_message'] =
        "Role already exists.";

    redirect('role_form.php');
}


/*----------------------------------------------------------
    Save Role
----------------------------------------------------------*/

$createdByUserId = (int)$_SESSION['user_id'];

try
{
    $success = createRole(
        $conn,
        $roleName,
        $description,
        $isActive,
        $createdByUserId
    );

    if ($success)
    {
        $_SESSION['success_message'] =
            "Role created successfully.";
    }
    else
    {
        $_SESSION['error_message'] =
            "Unable to create the role.";
    }
}
catch (Throwable $ex)
{
    $_SESSION['error_message'] =
        "Database Error: " . $ex->getMessage();
}

redirect('role_list.php');