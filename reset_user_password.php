<?php

/******************************************************************************
 * StudyConnect
 *
 * Module   : Security / RBAC
 * Page     : reset_user_password.php
 * Purpose  : Reset user password.
 *
 * Version  : 1.0
 * Updated  : 15-Jul-2026
 ******************************************************************************/


require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';

require_once 'includes/user_helper.php';

$userId = (int)($_GET['user_id'] ?? 0);

if ($userId <= 0)
{
    $_SESSION['error_message'] = "Invalid user.";
    redirect("users_list.php");
}

$user = getUserSummary($conn, $userId);

if (!$user)
{
    $_SESSION['error_message'] = "User not found.";
    redirect("users_list.php");
}

$page_title = "Password Management";
$page_description = "Reset user passwords and manage password security.";
$page_icon = "fa fa-key";

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $result = resetUserPasswordAndNotify(
        $conn,
        $user,
        $_SESSION['user_id']
    );

    if ($result['success'])
    {
        if ($result['emailSent'])
        {
            $_SESSION['success_message'] = $result['message'];
        }
        else
        {
            $_SESSION['warning_message'] = $result['message'];
        }
    }
    else
    {
        $_SESSION['error_message'] = $result['message'];
    }

    redirect("users_list.php");
}


include 'includes/admin_header.php';
include 'includes/admin_page_header.php';
?>


<?php if (!empty($_SESSION['success_message'])): ?>

<div class="alert alert-success alert-dismissible fade show" role="alert">

    <?= htmlspecialchars($_SESSION['success_message']) ?>

    <?php unset($_SESSION['success_message']); ?>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php if (!empty($_SESSION['warning_message'])): ?>

<div class="alert alert-warning alert-dismissible fade show"
     role="alert">

    <i class="fa fa-exclamation-triangle"></i>

    <?= htmlspecialchars($_SESSION['warning_message']) ?>

    <?php unset($_SESSION['warning_message']); ?>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php endif; ?>

<?php endif; ?>

<?php if (!empty($_SESSION['error_message'])): ?>

<div class="alert alert-danger alert-dismissible fade show" role="alert">

    <?= htmlspecialchars($_SESSION['error_message']) ?>

    <?php unset($_SESSION['error_message']); ?>

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php endif; ?>

<div class="card shadow">

    <div class="card-body">

<?php

$activeTab = "password";

include 'includes/user_summary_card.php';

include 'includes/admin_action_tabs.php';

?>

<h5 class="border-bottom pb-2 mb-4 mt-4">

    <i class="fa fa-key"></i>

    Password Management

</h5>

<div class="alert alert-warning">

    <h6>

        <i class="fa fa-exclamation-triangle"></i>

        Password Reset

    </h6>

    <p class="mb-2">

        Resetting this user's password will:

    </p>

    <ul class="mb-0">

        <li>Generate a secure temporary password.</li>
        <li>Require the user to change the password at next login.</li>
        <li>Clear failed login attempts.</li>
        <li>Unlock the account if currently locked.</li>
		<li>Send the temporary password to the user's registered email address.</li>

    </ul>

</div>

<form id="resetPasswordForm" method="post">

    <div class="d-flex justify-content-between mt-4">

        <a
            href="users_list.php"
            class="btn btn-secondary">

            <i class="fa fa-arrow-left"></i>

            Back

        </a>

		<button
			type="button"
			class="btn btn-warning"
			onclick="showConfirmationModal({
				formId: 'resetPasswordForm',
				icon: 'fa fa-key',
				title: 'Reset Password',
				message: 'You are about to reset this user\'s password.',
				details: [
					'A secure temporary password will be generated.',
					'The current password will stop working immediately.',
					'The user will be required to change the password at the next login.',
					'An email containing the temporary password will be sent to the user.'
				],
				confirmText: 'Reset Password',
				confirmClass: 'btn-warning',
				headerClass: 'bg-warning text-dark'
			});">

			<i class="fa fa-key"></i>

			Reset Password

		</button>

    </div>

</form>

    </div>

</div>

<?php require_once 'includes/confirm_modal.php'; ?>

<?php include 'includes/admin_footer.php'; ?>