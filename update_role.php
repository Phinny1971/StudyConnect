<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Role Management
 * Page     : update_role.php
 * Purpose  : Update an existing role
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

$roleId      = (int)($_POST['role_id'] ?? 0);
$roleName    = clean($_POST['role_name'] ?? '');
$description = clean($_POST['description'] ?? '');
$isActive    = isset($_POST['is_active']) ? 1 : 0;

if ($roleId <= 0)
{
    $_SESSION['error_message'] = "Invalid Role.";

    redirect('role_list.php');
}

/*----------------------------------------------------------
    Load Existing Role
----------------------------------------------------------*/

$role = getRoleById($conn, $roleId);

if (!$role)
{
    $_SESSION['error_message'] = "Role not found.";

    redirect('role_list.php');
}

/*----------------------------------------------------------
    Validation
----------------------------------------------------------*/

if ($roleName === '')
{
    $_SESSION['error_message'] = "Role Name is required.";

    redirect("role_form.php?role_id={$roleId}");
}

if (strlen($roleName) > 100)
{
    $_SESSION['error_message'] =
        "Role Name cannot exceed 100 characters.";

    redirect("role_form.php?role_id={$roleId}");
}

if (strlen($description) > 255)
{
    $_SESSION['error_message'] =
        "Description cannot exceed 255 characters.";

    redirect("role_form.php?role_id={$roleId}");
}

/*----------------------------------------------------------
    Protect System Role Name
----------------------------------------------------------*/

if ((int)$role['is_system_generated'] === 1)
{
    $roleName = $role['role_name'];
}

/*----------------------------------------------------------
    Duplicate Check
----------------------------------------------------------*/

if (roleExists($conn, $roleName, $roleId))
{
    $_SESSION['error_message'] =
        "Another role with this name already exists.";

    redirect("role_form.php?role_id={$roleId}");
}

/*----------------------------------------------------------
    Update Role
----------------------------------------------------------*/

try
{
    $success = updateRole(
        $conn,
        $roleId,
        $roleName,
        $description,
        $isActive,
        (int)$_SESSION['user_id']
    );

    if ($success)
    {
        $_SESSION['success_message'] =
            "Role updated successfully.";
    }
    else
    {
        $_SESSION['error_message'] =
            "Unable to update the role.";
    }
}
catch (Throwable $ex)
{
    $_SESSION['error_message'] =
        "Database Error: " . $ex->getMessage();
}

redirect('role_list.php');