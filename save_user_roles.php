<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Security / RBAC
 * Page     : save_user_roles.php
 * Purpose  : Save role assignments for a user.
 *
 * Version  : 1.0
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('users_list.php');
}

$userId = (int) ($_POST['user_id'] ?? 0);

if ($userId <= 0) {
    $_SESSION['error_message'] = "Invalid user.";
    redirect('users_list.php');
}

$selectedRoles = $_POST['roles'] ?? [];

/*
|--------------------------------------------------------------------------
| Load Existing Role Assignments
|--------------------------------------------------------------------------
*/

$existingRoles = [];

$existingRolesStmt = $conn->prepare("
    SELECT
        user_role_id,
        role_id,
        is_active
    FROM user_roles
    WHERE user_id = ?
");

$existingRolesStmt->bind_param("i", $userId);
$existingRolesStmt->execute();

$existingRolesResult = $existingRolesStmt->get_result();

while ($row = $existingRolesResult->fetch_assoc()) {
    $existingRoles[$row['role_id']] = $row;
}

/*
|--------------------------------------------------------------------------
| Process Selected Roles
|--------------------------------------------------------------------------
*/

foreach ($selectedRoles as $roleId) {

    $roleId = (int) $roleId;

    if (isset($existingRoles[$roleId])) {

        if ((int) $existingRoles[$roleId]['is_active'] === 0) {

            $updateRoleStmt = $conn->prepare("
                UPDATE user_roles
                SET is_active = 1
                WHERE user_role_id = ?
            ");

            $updateRoleStmt->bind_param(
                "i",
                $existingRoles[$roleId]['user_role_id']
            );

            $updateRoleStmt->execute();
        }

    } else {

        $createdByUserId = $_SESSION['user_id'];

        $insertRoleStmt = $conn->prepare("
            INSERT INTO user_roles
            (
                user_id,
                role_id,
                created_by_user_id,
                is_active
            )
            VALUES
            (?, ?, ?, 1)
        ");

        $insertRoleStmt->bind_param(
            "iii",
            $userId,
            $roleId,
            $createdByUserId
        );

        $insertRoleStmt->execute();
    }
}

/*
|--------------------------------------------------------------------------
| Disable Unselected Roles
|--------------------------------------------------------------------------
*/

foreach ($existingRoles as $roleId => $role) {

    if (!in_array($roleId, $selectedRoles)) {

        $disableRoleStmt = $conn->prepare("
            UPDATE user_roles
            SET is_active = 0
            WHERE user_role_id = ?
        ");

        $disableRoleStmt->bind_param(
            "i",
            $role['user_role_id']
        );

        $disableRoleStmt->execute();
    }
}

$_SESSION['success_message'] = "User roles updated successfully.";

redirect("assign_roles.php?user_id={$userId}");