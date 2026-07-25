<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Role Management
 * Page     : save_role_permissions.php
 * Purpose  : Saves permissions assigned to a role.
 *
 * Version  : 1.0
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';
require_once 'includes/permission_helper.php';
require_once 'includes/role_permission_helper.php';
require_once 'includes/role_helper.php';

/*----------------------------------------------------------
    Administrator Only
----------------------------------------------------------*/

if (!isAdministrator())
{
    denyAccess();
}

/*----------------------------------------------------------
    POST Only
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
        'Invalid request.';

    redirect('role_list.php');
}

/*----------------------------------------------------------
    Read Form
----------------------------------------------------------*/

$roleId = (int)($_POST['role_id'] ?? 0);

$permissions = $_POST['permissions'] ?? [];

if ($roleId <= 0)
{
    $_SESSION['error_message'] =
        'Invalid role.';

    redirect('role_list.php');
}

/*----------------------------------------------------------
    Verify Role Exists
----------------------------------------------------------*/

$role = getRoleById($conn, $roleId);

if (!$role)
{
    $_SESSION['error_message'] =
        'Role not found.';

    redirect('role_list.php');
}

/*----------------------------------------------------------
    Save
----------------------------------------------------------*/

try
{
    saveRolePermissions(
        $conn,
        $roleId,
        $permissions,
        (int)$_SESSION['user_id']
    );

    $_SESSION['success_message'] =
        'Permissions updated successfully.';
}
catch(Throwable $ex)
{
    $_SESSION['error_message'] =
        'Database Error : ' . $ex->getMessage();
}

redirect("assign_role_permissions.php?role_id={$roleId}");