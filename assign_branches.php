<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Security / RBAC
 * Page     : assign_branches.php
 * Purpose  : Assign branch access to users.
 *
 * Version  : 1.0
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';
require_once 'includes/user_helper.php';

$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

if ($userId <= 0) {
redirect('users_list.php');
}

$userSummary = getUserSummary($conn, $userId);

if (!$userSummary) {
    die("User not found.");
}

$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $selectedBranches = $_POST['branches'] ?? [];

    /*
     * Load all existing assignments
     */
	$existingAssignments = [];

    $existingAssignmentsStmt = $conn->prepare("
        SELECT user_branch_id,
               branch_name,
               is_active
        FROM user_branches
        WHERE user_id = ?
    ");

    $existingAssignmentsStmt->bind_param("i", $userId);
    $existingAssignmentsStmt->execute();

    $existingAssignmentsResult = $existingAssignmentsStmt->get_result();

    while ($row = $existingAssignmentsResult->fetch_assoc()) {
        $existingAssignments[$row['branch_name']] = $row;
    }

    /*
     * Process selected branches
     */

    foreach ($selectedBranches as $branchName) {

        if (isset($existingAssignments[$branchName])) {

            if ($existingAssignments[$branchName]['is_active'] == 0) {

                $updateAssignmentStmt = $conn->prepare("
                    UPDATE user_branches
                    SET is_active = 1
                    WHERE user_branch_id = ?
                ");

                $updateAssignmentStmt->bind_param(
                    "i",
                    $existingAssignments[$branchName]['user_branch_id']
                );

                $updateAssignmentStmt->execute();
            }

        } else {

            $insertAssignmentStmt = $conn->prepare("
                INSERT INTO user_branches
                (
                    user_id,
                    branch_name,
                    created_by_user_id,
                    is_active
                )
                VALUES
                (?, ?, ?, 1)
            ");

            $createdBy = $_SESSION['user_id'];

            $insertAssignmentStmt->bind_param(
                "isi",
                $userId,
                $branchName,
                $createdBy
            );

            $insertAssignmentStmt->execute();
        }
    }

    /*
     * Disable unselected branches
     */

    foreach ($existingAssignments as $branchName => $branch) {

        if (!in_array($branchName, $selectedBranches)) {

            $disableAssignmentStmt = $conn->prepare("
                UPDATE user_branches
                SET is_active = 0
                WHERE user_branch_id = ?
            ");

            $disableAssignmentStmt->bind_param(
                "i",
                $branch['user_branch_id']
            );

            $disableAssignmentStmt->execute();
        }
    }

    $successMessage = "Branch access updated successfully.";
}

/*
 * Load all branches
 */

$branches = [];

$branchSql = "
SELECT
    Branch_name,
    Branch_location
FROM branches
ORDER BY Branch_name
";

$branchResult = $conn->query($branchSql);

while ($row = $branchResult->fetch_assoc()) {
    $branches[] = $row;
}

/*
 * Active assignments
 */

$assignedBranches = [];

$assignedBranchesStmt = $conn->prepare("
SELECT branch_name
FROM user_branches
WHERE user_id = ?
AND is_active = 1
");

$assignedBranchesStmt->bind_param("i", $userId);
$assignedBranchesStmt->execute();

$branchResult = $assignedBranchesStmt->get_result();

while ($row = $branchResult->fetch_assoc()) {
    $assignedBranches[] = $row['branch_name'];
}

$totalBranches = count($branches);
$assignedCount = count($assignedBranches);

require_once 'includes/admin_header.php';

$pageHeading = "Assign Branch Access";
$pageSubHeading = "Manage branch-level access for this user.";

include 'includes/admin_page_header.php';

$user = $userSummary;
echo '<pre>';
var_dump($user);
echo '</pre>';
exit;
include 'includes/user_summary_card.php';

include 'includes/admin_action_tabs.php';

?>

<?php if ($successMessage): ?>

<div class="alert alert-success">
    <?= htmlspecialchars($successMessage) ?>
</div>

<?php endif; ?>

<div class="card shadow-sm">

    <div class="card-header">

		<h5 class="mb-1">
			<i class="fa fa-building"></i>
			Branch Access
		</h5>

		<small class="text-muted">
			<?= $assignedCount ?> of <?= $totalBranches ?> branches currently assigned.
		</small>

    </div>

    <div class="card-body">

        <form method="post">
		
		<?php foreach ($branches as $branch): ?>

			<div class="border rounded p-3 mb-3">

				<input
					class="form-check-input"
					type="checkbox"
					name="branches[]"
					value="<?= htmlspecialchars($branch['Branch_name']) ?>"
					id="<?= md5($branch['Branch_name']) ?>"

					<?= in_array(
						$branch['Branch_name'],
						$assignedBranches
					) ? 'checked' : '' ?>

				>

				<label
					class="form-check-label"
					for="<?= md5($branch['Branch_name']) ?>"
				>

					<strong>

						<?= htmlspecialchars($branch['Branch_name']) ?>

					</strong>

					<br>

					<small class="text-muted">

						<?= htmlspecialchars($branch['Branch_location']) ?>

					</small>

				</label>

			</div>

		
			<?php endforeach; ?>
			
		<div class="d-flex justify-content-between">

    <a
        href="users_list.php"
        class="btn btn-secondary"
    >

        Back

    </a>

    <button
        class="btn btn-primary"
        type="submit"
    >

        Save Branch Access

    </button>

</div>

</form>

</div>

</div>

<?php

require_once 'includes/admin_footer.php';

