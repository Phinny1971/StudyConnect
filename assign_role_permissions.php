<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Role Management
 * Page     : assign_role_permissions.php
 * Purpose  : Assign Permissions to a Role
 *
 * Version  : 1.0
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';
require_once 'includes/permission_helper.php';
require_once 'includes/role_helper.php';
require_once 'includes/role_permission_helper.php';

/*---------------------------------------------------------------------------
    Administrator Only
---------------------------------------------------------------------------*/

if (!isAdministrator())
{
    denyAccess();
}

/*---------------------------------------------------------------------------
    Read Role
---------------------------------------------------------------------------*/

$roleId = (int)($_GET['role_id'] ?? 0);

if ($roleId <= 0)
{
    $_SESSION['error_message'] = "Invalid Role.";
    redirect("role_list.php");
}

$role = getRoleById($conn, $roleId);

if (!$role)
{
    $_SESSION['error_message'] = "Role not found.";
    redirect("role_list.php");
}

/*---------------------------------------------------------------------------
    Load Permissions
---------------------------------------------------------------------------*/

$permissions = getPermissionsGrouped($conn);

$assignedPermissions = getRolePermissions($conn, $roleId);

/*---------------------------------------------------------------------------
    Page Information
---------------------------------------------------------------------------*/

$pageTitle      = "Assign Permissions";
$pageHeading    = "Assign Permissions";
$pageSubHeading = "Manage permissions for the selected role.";

require_once 'includes/admin_header.php';
include 'includes/admin_page_header.php';
?>

<div class="card shadow-sm">

<div class="card-body">

<h5 class="mb-3">

<i class="fa fa-user-shield"></i>

<?= e($role['role_name']) ?>

</h5>

<p class="text-muted">

<?= e($role['description']) ?>

</p>

<form
method="post"
action="save_role_permissions.php">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>">

<input
type="hidden"
name="role_id"
value="<?= $roleId ?>">

<?php foreach ($permissions as $module => $features) { ?>

<div class="card mb-4">

<div class="card-header sc-card-header">

<strong><?= e($module) ?></strong>

</div>

<div class="card-body">

<?php foreach ($features as $feature => $actions) { ?>

<h6 class="mt-3">

<?= e($feature) ?>

</h6>

<div class="row">

<?php foreach ($actions as $permission) { ?>

<div class="col-md-3 mb-2">

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="permissions[]"
value="<?= $permission['permission_id'] ?>"
<?= isset($assignedPermissions[$permission['permission_id']]) ? 'checked' : '' ?>>

<label class="form-check-label">

<?= e($permission['action_name']) ?>

</label>

</div>

</div>

<?php } ?>

</div>

<?php } ?>

</div>

</div>

<?php } ?>

<div class="d-flex justify-content-between">

<a
href="role_list.php"
class="btn btn-secondary">

<i class="fa fa-arrow-left"></i>

Back

</a>

<button
type="submit"
class="btn btn-success">

<i class="fa fa-save"></i>

Save Permissions

</button>

</div>

</form>

</div>

</div>

<?php include 'includes/admin_footer.php'; ?>