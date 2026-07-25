<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Security / RBAC
 * Page     : users_list.php
 * Purpose  : Displays application users.
 *
 * Version  : 1.1
 * Updated  : 10-Jul-2026
 * Changes  :
 *   - Adopted common admin UI framework
 *   - Uses admin_header.php/admin_footer.php
 *   - Uses admin_page_header.php
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';

$pageTitle = "User Management";
$pageHeading = "User Management";
$pageSubHeading = "Manage application users, roles and branch assignments.";

$sql = "
SELECT
u.user_id,
u.display_name,
u.email,
s.status_name,
u.last_login_at,
GROUP_CONCAT(
DISTINCT r.role_name
ORDER BY r.role_name
SEPARATOR ', '
) AS roles,

GROUP_CONCAT(
DISTINCT ub.branch_name
ORDER BY ub.branch_name
SEPARATOR ', '
) AS branches

FROM users u

LEFT JOIN account_statuses s
ON s.status_id=u.status_id

LEFT JOIN user_roles ur
ON ur.user_id=u.user_id
AND ur.is_active=1

LEFT JOIN roles r
ON r.role_id=ur.role_id

LEFT JOIN user_branches ub
ON ub.user_id=u.user_id
AND ub.is_active=1

GROUP BY

u.user_id,
u.display_name,
u.email,
s.status_name,
u.last_login_at

ORDER BY
u.display_name";

$userResult = $conn->query($sql);

if (!$userResult) {
    die($conn->error);
}
?>

<?php
require_once 'includes/admin_header.php';
include 'includes/admin_page_header.php';
?>


<div class="d-flex justify-content-between align-items-center mb-3">
    <button
        class="btn btn-primary"
        onclick="location.href='user_form.php'">
        <i class="fa fa-user-plus"></i>
        New User
    </button>
</div>

<div class="card shadow-sm">

    <div class="card-header sc-card-header">
        <h5 class="mb-0">
            <i class="fa fa-users"></i>
            Application Users
        </h5>
    </div>

<div class="card-body">

<table id="usersTable" class="display nowrap table table-hover table-sm" style="width:100%">

    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Branches</th>
            <th>Status</th>
            <th>Last Login</th>
            <th style="width:170px;">Actions</th>
        </tr>
    </thead>

    <tbody>

    <?php while ($user = $userResult->fetch_assoc()) { ?>

        <?php

			$userId = (int) $user['user_id'];

			$displayName = $user['display_name'];
			$email = $user['email'];

			$statusName = $user['status_name'];

			$roles = empty($user['roles']) ? "--" : $user['roles'];
			$branches = empty($user['branches']) ? "--" : $user['branches'];

			if (empty($user['last_login_at']))
			{
				$lastLogin = '<span class="text-muted">Never Logged In</span>';
			}
			else
			{
				$lastLogin = date("d-M-Y h:i A", strtotime($user['last_login_at']));
			}

        ?>

        <tr>

            <td><?= htmlspecialchars($displayName) ?></td>

            <td><?= htmlspecialchars($email) ?></td>

            <td><?= htmlspecialchars($roles) ?></td>

            <td><?= htmlspecialchars($branches) ?></td>

            <td>

           <?php
		   
		   $statusClass = "status-default";

			switch (strtolower($statusName))
			{
				case "active":
					$statusClass = "status-active";
					break;

				case "inactive":
					$statusClass = "status-inactive";
					break;

				case "locked":
					$statusClass = "status-locked";
					break;

				case "expired":
					$statusClass = "status-expired";
					break;

				case "pending":
					$statusClass = "status-pending";
					break;

				case "disabled":
					$statusClass = "status-disabled";
					break;

				default:
					$statusClass = "status-default";
			}
			

			?>

			<span class="status-badge <?= $statusClass ?>">
				<?= htmlspecialchars($statusName) ?>
			</span>

            

            </td>

            <td><?= $lastLogin ?></td>


			<td class="actions-column">

				<div class="d-flex gap-1">

					<a href="user_form.php?user_id=<?= $userId ?>"
					   class="btn btn-sm btn-primary">
						<i class="fa fa-edit"></i> Edit
					</a>

					<a href="assign_roles.php?user_id=<?= $userId ?>"
					   class="btn btn-sm btn-info">
						<i class="fa fa-users"></i> Roles
					</a>

					<a href="assign_branches.php?user_id=<?= $userId ?>"
					   class="btn btn-sm btn-secondary"
					   title="Branches">
						<i class="fa fa-building"></i> Branches
					</a>

				</div>

			</td>

        </tr>

    <?php } ?>

    </tbody>

</table>

</div>

</div>

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<?php
require_once 'includes/admin_footer.php';
?>

<script>
$(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#usersTable')) {
        $('#usersTable').DataTable().destroy();
    }

	$('#usersTable').DataTable({
		pageLength: 10,
		responsive: false,
		autoWidth: false,
		scrollX: true,
		dom: 
		"<'row mb-3'<'col-md-6'B><'col-md-6'f>>" +
		"rt" +
		"<'row mt-3'<'col-md-6'i><'col-md-6'p>>",
		buttons: [
			'excel',
			'pdf',
			'print'
		]
	});

});

</script>
