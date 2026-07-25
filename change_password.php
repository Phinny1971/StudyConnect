<?php
require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/password_helper.php';
require_once 'includes/user_helper.php';
require_once 'includes/helpers.php';

$userId = (int) ($_SESSION['user_id'] ?? 0);

if ($userId <= 0) 
{
    forceLogout();
}

$user = getUserById($conn, $userId);

if (!$user) 
{
    forceLogout();
}

$errors = [];

if (isPost())
{
	if (!validateCsrfToken($_POST['csrf_token'] ?? ''))
	{
		$errors[] =
			'Invalid request. Please refresh the page.';
	}
	
	$currentPassword = clean($_POST['current_password'] ?? '');
	$newPassword     = clean($_POST['new_password'] ?? '');
	$confirmPassword = clean($_POST['confirm_password'] ?? '');

	if ($currentPassword === '') {
		$errors[] = 'Current password is required.';
	}

	if ($newPassword === '') {
		$errors[] = 'New password is required.';
	}

	if ($confirmPassword === '') {
		$errors[] = 'Confirm password is required.';
	}
	
	if ($newPassword !== $confirmPassword) 
	{
    $errors[] = 'New password and confirm password do not match.';
	}
	
	if (
    empty($errors) &&
    !verifyPassword(
        $currentPassword,
        $user['password_hash']
    )
	) {
		$errors[] = 'Current password is incorrect.';
	}

	if (
		empty($errors) &&
		verifyPassword(
			$newPassword,
			$user['password_hash']
		)
	) {
		$errors[] =
			'The new password must be different from your current password.';
	}
	
	if (empty($errors))
	{
		$validation = validatePasswordPolicy($newPassword);

		if (!$validation['valid'])
		{
			$errors = array_merge(
				$errors,
				$validation['errors']
			);
		}
	}

	
	if (empty($errors))
	{
		$newPasswordHash = hashPassword($newPassword);

		if (updateUserPassword(
			$conn,
			$userId,
			$newPasswordHash
		))
		{
			session_regenerate_id(true);

			$_SESSION['force_password_change'] = 0;

			$_SESSION['success_message'] =
				'Password changed successfully.';

			redirect('main.php');
		}

		$errors[] =
			'Unable to update password. Please try again.';
	}
}

?>

<?php if (!empty($errors)): ?>

<div class="alert alert-danger">
    <ul class="mb-0">

        <?php foreach ($errors as $error): ?>

            <li><?= e($error) ?></li>

        <?php endforeach; ?>

    </ul>
</div>

<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>StudyConnect - Change Password</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <style>

        body
        {
            background:#f5f7fb;
        }

        .password-card
        {
            max-width:550px;
            margin:60px auto;
            border:none;
            border-radius:10px;
            box-shadow:0 4px 20px rgba(0,0,0,.10);
        }

        .logo
        {
            height:70px;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="card password-card">

        <div class="card-body p-4">

            <div class="text-center mb-4">

                <img
                    src="images/SC-Logo.png.webp"
                    class="logo"
                    alt="StudyConnect">

                <h3 class="mt-3">

                    Change Password

                </h3>

            </div>

            <div class="alert alert-warning">

                <strong>

                    Password Change Required

                </strong>

                <hr>

                For security reasons you must change your password before
                accessing StudyConnect.

            </div>
			
			<div class="alert alert-info">

				Your password has been reset by an administrator
				or this is your first login.

				<br><br>

				Please choose a new password before continuing.

			</div>

            <?php require_once 'includes/flash_messages.php'; ?>

            <?php if (!empty($errors)): ?>

                <div class="alert alert-danger">

                    <ul class="mb-0">

                        <?php foreach ($errors as $error): ?>

                            <li><?= e($error) ?></li>

                        <?php endforeach; ?>

                    </ul>

                </div>

            <?php endif; ?>

            <form
                method="post"
                autocomplete="off">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e($_SESSION['csrf_token']) ?>">

			<div class="mb-3">

				<label class="form-label">
					Current Password
				</label>

				<div class="input-group">

					<input
						type="password"
						id="current_password"
						name="current_password"
						class="form-control"
						required
						autofocus>

					<button
						class="btn btn-outline-secondary"
						type="button"
						onclick="togglePassword('current_password', this)">

						<i class="fa fa-eye"></i>

					</button>

				</div>

			</div>

                <div class="mb-3">

                    <label class="form-label">

                        New Password

                    </label>

                    <input type="password" id="new_password" name="new_password" class="form-control" required>

					<div class="form-text mt-3">
						<strong>Password Requirements</strong>
						<ul class="list-unstyled mt-2">
							<li id="ruleLength">
								<i class="fa fa-circle-xmark text-danger"></i>
								Minimum 8 characters
							</li>
							<li id="ruleUpper">
								<i class="fa fa-circle-xmark text-danger"></i>
								One uppercase letter
							</li>
							<li id="ruleLower">
								<i class="fa fa-circle-xmark text-danger"></i>
								One lowercase letter
							</li>
							<li id="ruleNumber">
								<i class="fa fa-circle-xmark text-danger"></i>
								One number
							</li>
							<li id="ruleSpecial">
								<i class="fa fa-circle-xmark text-danger"></i>
								One special character
							</li>
						</ul>
					</div>
                </div>
				
				<div class="mt-2">

					<div class="progress">

						<div
							id="passwordStrengthBar"
							class="progress-bar"
							style="width:0%">

						</div>

					</div>

					<small
						id="passwordStrengthText"
						class="text-muted">

						Strength: -

					</small>

				</div>

                <div class="mb-4">

                    <label class="form-label">

                        Confirm Password

                    </label>

					<input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    
                </div>
				
				<div
					id="passwordMatchMessage"
					class="form-text mt-2">

				</div>

                <div class="d-grid">

                    <button
                        type="submit"  id="btnChangePassword"
                        class="btn btn-primary">

                        <i class="fa fa-key"></i>

                        Change Password

                    </button>

                </div>

            </form>

            <hr>

            <div class="text-center">

                <a
                    href="logout.php"
                    class="btn btn-outline-secondary">

                    Logout

                </a>

            </div>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

function togglePassword(id, button)
{
    const input = document.getElementById(id);

    const icon = button.querySelector("i");

    if (input.type === "password")
    {
        input.type = "text";

        icon.classList.remove("fa-eye");

        icon.classList.add("fa-eye-slash");
    }
    else
    {
        input.type = "password";

        icon.classList.remove("fa-eye-slash");

        icon.classList.add("fa-eye");
    }
}

document.querySelector("form").addEventListener(
    "submit",
    function ()
    {
        document
            .getElementById("btnChangePassword")
            .disabled = true;
    }
);

const password = document.getElementById("new_password");
const confirmPassword = document.getElementById("confirm_password");

const passwordMatchMessage = document.getElementById("passwordMatchMessage");
const strengthBar = document.getElementById("passwordStrengthBar");

const strengthText = document.getElementById("passwordStrengthText");

password.addEventListener(
    "input",
    function ()
    {
        const value = this.value;

        checkStrength(value);

        updateRule(
            "ruleLength",
            value.length >= 8
        );

        updateRule(
            "ruleUpper",
            /[A-Z]/.test(value)
        );

        updateRule(
            "ruleLower",
            /[a-z]/.test(value)
        );

        updateRule(
            "ruleNumber",
            /[0-9]/.test(value)
        );

        updateRule(
            "ruleSpecial",
            /[^A-Za-z0-9]/.test(value)
        );

        checkPasswordMatch();
    }
);

confirmPassword.addEventListener(
    "input",
    checkPasswordMatch
);

function checkStrength(password)
{
    let score = 0;

    if(password.length >= 8) score++;

    if(/[A-Z]/.test(password)) score++;

    if(/[a-z]/.test(password)) score++;

    if(/[0-9]/.test(password)) score++;

    if(/[^A-Za-z0-9]/.test(password)) score++;

    const bar =
        document.getElementById(
            "passwordStrengthBar"
        );

    const text =
        document.getElementById(
            "passwordStrengthText"
        );

    switch(score)
    {
        case 0:
        case 1:
            bar.style.width="20%";
            bar.className="progress-bar bg-danger";
            text.innerHTML="Strength : Weak";
            break;

        case 2:
        case 3:
            bar.style.width="60%";
            bar.className="progress-bar bg-warning";
            text.innerHTML="Strength : Medium";
            break;

        case 4:
            bar.style.width="80%";
            bar.className="progress-bar bg-info";
            text.innerHTML="Strength : Good";
            break;

        case 5:
            bar.style.width="100%";
            bar.className="progress-bar bg-success";
            text.innerHTML="Strength : Strong";
            break;
    }
}

function updateRule(id, passed)
{
    const item = document.getElementById(id);

    const icon = item.querySelector("i");

    if (passed)
    {
        icon.classList.remove(
            "fa-circle-xmark",
            "text-danger"
        );

        icon.classList.add(
            "fa-circle-check",
            "text-success"
        );
    }
    else
    {
        icon.classList.remove(
            "fa-circle-check",
            "text-success"
        );

        icon.classList.add(
            "fa-circle-xmark",
            "text-danger"
        );
    }
}

function checkPasswordMatch()
{
    const message =
        document.getElementById("passwordMatchMessage");

    if (confirmPassword.value.length === 0)
    {
        message.innerHTML = "";
        return;
    }

    if (password.value === confirmPassword.value)
    {
        message.innerHTML =
            '<span class="text-success">' +
            '<i class="fa fa-circle-check"></i> ' +
            'Passwords match.' +
            '</span>';
    }
    else
    {
        message.innerHTML =
            '<span class="text-danger">' +
            '<i class="fa fa-circle-xmark"></i> ' +
            'Passwords do not match.' +
            '</span>';
    }
}

confirmPassword.addEventListener(
    "paste",
    function(e)
    {
        e.preventDefault();
    }
);


</script>

</body>

</html>