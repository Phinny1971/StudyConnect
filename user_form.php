<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Security / RBAC
 * Page     : user_form.php
 * Purpose  : Add / Edit User
 *
 * Version : 1.1
	Updated : 10-Jul-2026

	Changes :
	- Adopted common admin layout
	- Added common page header
	- Added Account Information section
	- Added Credentials section
	- Improved button layout
	- Improved display name auto-generation
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';

/*----------------------------------------------------------
    Determine Add / Edit Mode
----------------------------------------------------------*/

$userId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;

$isEditMode = ($userId > 0);

$pageTitle = $isEditMode ? "Edit User" : "Add New User";

/*----------------------------------------------------------
    Default Values
----------------------------------------------------------*/

$firstName = "";
$lastName = "";
$displayName = "";
$email = "";
$statusId = 1;                 // Active
$forcePasswordChange = 1;

/*----------------------------------------------------------
    Load User In Edit Mode
----------------------------------------------------------*/

if($isEditMode)
{
    $sql = "

    SELECT
        user_id,
        first_name,
        last_name,
        display_name,
        email,
        status_id,
        force_password_change

    FROM users

    WHERE user_id = ?";

    $stmt = $conn->prepare($sql);

    if(!$stmt)
    {
        die($conn->error);
    }

    $stmt->bind_param("i", $userId);

    $stmt->execute();

    $result = $stmt->get_result();

    if($row = $result->fetch_assoc())
    {
        $firstName = $row['first_name'];
        $lastName = $row['last_name'];
        $displayName = $row['display_name'];
        $email = $row['email'];
        $statusId = $row['status_id'];
        $forcePasswordChange = $row['force_password_change'];
    }
    else
    {
        $_SESSION['error_message'] = "User not found.";

        redirect("users_list.php");
    }

    $stmt->close();
}


/*----------------------------------------------------------
    Load Statuses
----------------------------------------------------------*/

$sql = "
SELECT
    status_id,
    status_name
FROM account_statuses
ORDER BY status_name";

$statusResult = $conn->query($sql);

if(!$statusResult)
{
    die($conn->error);
}

$page_title = $pageTitle;
$page_description = "Create or update an application user.";
$page_icon = "fa fa-user";

include 'includes/admin_header.php';
include 'includes/admin_page_header.php';

?>


<div class="card shadow-sm">

    <div class="card-body">

		<form method="post" action="<?= $isEditMode ? 'update_user.php' : 'save_user.php' ?>">
		
		<h5 class="border-bottom pb-2 mb-4">
			<i class="fa fa-user"></i>
			Account Information
		</h5>

		<?php if($isEditMode){ ?>
		
		<input
		type="hidden"
		name="user_id"
		value="<?= $userId ?>">

		<?php } ?>
		
		<div class="row">

		<div class="col-md-6 mb-3">

		<label class="form-label">

		First Name <span class="text-danger">*</span>

		</label>

		<input
		type="text"
		id="first_name"
		name="first_name"
		class="form-control"
		value="<?= htmlspecialchars($firstName) ?>"
		required>

		</div>

		<div class="col-md-6 mb-3">

		<label class="form-label">

		Last Name

		</label>

		<input
		type="text"
		id="last_name"
		name="last_name"
		class="form-control"
		value="<?= htmlspecialchars($lastName) ?>">

		</div>

		</div>
		
		<div class="row">

		<div class="col-md-6 mb-3">

		<label class="form-label">

		Display Name <span class="text-danger">*</span>

		</label>

		<input
		type="text"
		id="display_name"
		name="display_name"
		class="form-control"
		value="<?= htmlspecialchars($displayName) ?>"
		required>

		</div>

		<div class="col-md-6 mb-3">

		<label class="form-label">

		Email Address <span class="text-danger">*</span>

		</label>

		<input type="email" name="email" class="form-control" value="<?= e($email) ?>" 
		<?= $isEditMode ? 'readonly' : '' ?>
		required>
		
		<?php if($isEditMode){ ?>
		<div class="form-text">
			Email address cannot be changed after user creation.
		</div>
		<?php } ?>
		
		</div>

		</div>
		
		<div class="row">

		<div class="col-md-6 mb-3">

		<label class="form-label">

		Status

		</label>

		<select
		name="status_id"
		class="form-select">

		<?php while($status=$statusResult->fetch_assoc()){ ?>

		<option
		value="<?= $status['status_id'] ?>"
		<?= ($statusId==$status['status_id'])?'selected':'' ?>>

		<?= htmlspecialchars($status['status_name']) ?>

		</option>

		<?php } ?>

		</select>

		</div>

		</div>
		
		<?php if(!$isEditMode){ ?>

		<h5 class="border-bottom pb-2 mb-4 mt-4">
			<i class="fa fa-key"></i>
			Credentials
		</h5>
		
		<div class="row">

			<div class="col-md-6 mb-3">

				<label class="form-label">
					Temporary Password
				</label>

				<div class="input-group">

					<input
						type="text"
						id="tempPassword"
						name="password"
						class="form-control bg-light fw-bold"
						readonly>

					<button
						class="btn btn-outline-secondary"
						type="button"
						onclick="generatePassword()">

						<i class="fa fa-refresh"></i>
						Generate

					</button>

					<button
						class="btn btn-outline-primary"
						type="button"
						id="copyButton"
						onclick="copyPassword()">

						<i class="fa fa-copy"></i>
						Copy

					</button>

				</div>

			</div>

		</div>

		<div class="form-check mb-3">

		<input
		class="form-check-input"
		type="checkbox"
		name="force_password_change"
		checked>

		<label class="form-check-label">

		Force password change on first login

		</label>

		</div>

		<?php } ?>
		

		<div class="d-flex justify-content-between mt-4">
			<a href="users_list.php"
			   class="btn btn-secondary">
				<i class="fa fa-arrow-left"></i>
				Back
			</a>
			<button
				type="submit"
				class="btn btn-success">
				<i class="fa fa-save"></i>
				<?= $isEditMode ? 'Update User' : 'Save User' ?>
			</button>
		</div>
		
		</form>

		</div>

		</div>

<?php include 'includes/admin_footer.php'; ?>

<script>

let displayNameEdited = false;

document.addEventListener("DOMContentLoaded", function () {

    const firstName = document.getElementById("first_name");
    const lastName = document.getElementById("last_name");
    const displayName = document.getElementById("display_name");

    displayName.addEventListener("input", function () {
        displayNameEdited = true;
    });

    function updateDisplayName() {

        if(displayNameEdited)
            return;

        let name = firstName.value.trim();

        if(lastName.value.trim() !== "")
            name += " " + lastName.value.trim();

        displayName.value = name;
    }

	firstName.addEventListener("input", updateDisplayName);
	lastName.addEventListener("input", updateDisplayName);

    updateDisplayName();

});

function generatePassword()
{
    const upper = "ABCDEFGHJKLMNPQRSTUVWXYZ";
    const lower = "abcdefghijkmnopqrstuvwxyz";
    const numbers = "23456789";
    const symbols = "@#$%";

    const all = upper + lower + numbers + symbols;

    let password = "";

    password += upper[Math.floor(Math.random()*upper.length)];
    password += lower[Math.floor(Math.random()*lower.length)];
    password += numbers[Math.floor(Math.random()*numbers.length)];
    password += symbols[Math.floor(Math.random()*symbols.length)];

    while(password.length < 12)
    {
        password += all[Math.floor(Math.random()*all.length)];
    }

    password = password
        .split('')
        .sort(() => Math.random() - 0.5)
        .join('');

    document.getElementById("tempPassword").value = password;
}

window.onload=generatePassword;

	function copyPassword()
	{
		const passwordField = document.getElementById("tempPassword");

		if(passwordField.value === "")
			return;

		navigator.clipboard.writeText(passwordField.value);

		const btn = document.getElementById("copyButton");

		btn.innerHTML = '<i class="fa fa-check"></i> Copied';

		setTimeout(function(){

			btn.innerHTML =
				'<i class="fa fa-copy"></i> Copy';

		},2000);
	}
</script>