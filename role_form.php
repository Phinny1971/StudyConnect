<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Security / RBAC
 * Page     : role_form.php
 * Purpose  : Add / Edit Role
 *
 * Version  : 1.0
 * Updated  : 21-Jul-2026
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';
require_once 'includes/permission_helper.php';

/*----------------------------------------------------------
    Administrator Access Only
----------------------------------------------------------*/

if (!isAdministrator())
{
    denyAccess();
}

/*----------------------------------------------------------
    Determine Add / Edit Mode
----------------------------------------------------------*/

$roleId = isset($_GET['role_id']) ? (int)$_GET['role_id'] : 0;

$isEditMode = ($roleId > 0);

/*----------------------------------------------------------
    Default Values
----------------------------------------------------------*/

$roleName = "";
$description = "";
$isActive = 1;
$isSystemGenerated = 0;

require_once 'includes/role_helper.php';

/*----------------------------------------------------------
    Load Existing Role
----------------------------------------------------------*/

if ($isEditMode)
{
    $role = getRoleById($conn, $roleId);

    if (!$role)
    {
        $_SESSION['error_message'] = "Role not found.";
        redirect("role_list.php");
    }

    $roleName          = $role['role_name'];
    $description       = $role['description'];
    $isSystemGenerated = (int)$role['is_system_generated'];
    $isActive          = (int)$role['is_active'];
}

/*----------------------------------------------------------
    Page Information
----------------------------------------------------------*/

$pageTitle = $isEditMode ? "Edit Role" : "Add New Role";

$pageHeading = $pageTitle;

$pageSubHeading = "Create or update application roles.";

require_once 'includes/admin_header.php';
include 'includes/admin_page_header.php';
?>

<div class="card shadow-sm">

<div class="card-body">

<form
method="post"
action="<?= $isEditMode ? 'update_role.php' : 'submit_role.php' ?>">

<input
type="hidden"
name="csrf_token"
value="<?= $_SESSION['csrf_token'] ?>">

<?php if ($isEditMode) { ?>

<input
type="hidden"
name="role_id"
value="<?= $roleId ?>">

<?php } ?>

<h5 class="border-bottom pb-2 mb-4">

<i class="fa fa-user-shield"></i>

Role Information

</h5>

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">

Role Name <span class="text-danger">*</span>

</label>

<?php if ($isSystemGenerated) { ?>

<input
type="text"
class="form-control"
value="<?= e($roleName) ?>"
disabled>

<input
type="hidden"
name="role_name"
value="<?= e($roleName) ?>">

<div class="form-text">

System role names cannot be changed.

</div>

<?php } else { ?>

<input
type="text"
name="role_name"
class="form-control"
maxlength="100"
value="<?= e($roleName) ?>"
required>

<?php } ?>

</div>

</div>

<div class="row">

<div class="col-md-12 mb-3">

<label class="form-label">

Description

</label>

<textarea
name="description"
class="form-control"
rows="4"
maxlength="255"><?= e($description) ?></textarea>

</div>

</div>

<div class="row">

<div class="col-md-6 mb-3">

<div class="form-check">

<input
class="form-check-input"
type="checkbox"
name="is_active"
value="1"
<?= $isActive ? 'checked' : '' ?>>

<label class="form-check-label">

Active

</label>

</div>

</div>

</div>

<div class="d-flex justify-content-between mt-4">

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

<?= $isEditMode ? 'Update Role' : 'Save Role' ?>

</button>

</div>

</form>

</div>

</div>