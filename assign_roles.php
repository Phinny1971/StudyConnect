<?php

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


$sql="
SELECT
	r.role_id,
	r.role_name,
	CASE
	WHEN ur.user_role_id IS NULL
	THEN 0
	ELSE 1
	END AS assigned
FROM roles r
	LEFT JOIN user_roles ur
	ON ur.role_id=r.role_id
	AND ur.user_id=?
	AND ur.is_active=1
ORDER BY r.role_name";

$stmt=$conn->prepare($sql);

$stmt->bind_param("i",$userId);

$stmt->execute();

$roles=$stmt->get_result();
$stmt->close();

$page_title = "Assign Roles";
$page_description = "Manage security roles for application users.";
$page_icon = "fa fa-user-shield";

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

<?php endif; ?>


    <div class="card shadow">

        <div class="card-body">

			<form method="post" action="save_user_roles.php">

			<input
			type="hidden"
			name="user_id"
			value="<?= $userId ?>">

			<?php

			$activeTab = "roles";

			include 'includes/user_summary_card.php';

			include 'includes/admin_action_tabs.php';

			?>

			<h5 class="border-bottom pb-2 mb-4 mt-4">
				<i class="fa fa-users"></i>
				Role Assignment
			</h5>
			
			<div class="row">
					
				<?php while($role = $roles->fetch_assoc()){ ?>

					<div class="col-md-4 mb-3">

						<div class="border rounded p-3 h-100">

							<div class="form-check">

								<input
									id="role<?= $role['role_id'] ?>"
									class="form-check-input"
									type="checkbox"
									name="roles[]"
									value="<?= $role['role_id'] ?>"
									<?= $role['assigned'] ? 'checked' : '' ?>>

								<label
									for="role<?= $role['role_id'] ?>"
									class="form-check-label fw-semibold">

									<?= htmlspecialchars($role['role_name']) ?>

								</label>

							</div>

						</div>

					</div>

					<?php } ?>

			</div>

				<hr>

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

						Save Roles

					</button>

				</div>

			</form>

        </div>

    </div>

<?php include 'includes/admin_footer.php'; ?>