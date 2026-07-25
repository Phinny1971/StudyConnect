<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Security Foundation
 * File     : permission_helper.php
 * Purpose  : Helper functions for Role Based Access Control (RBAC)
 *
 * Version  : 1.0
 ******************************************************************************/

require_once __DIR__ . '/helpers.php';

/******************************************************************************
 * Returns all active permissions assigned to a user.
 *
 * @param mysqli $conn
 * @param int    $userId
 *
 * @return array
 ******************************************************************************/
function getUserPermissions(mysqli $conn, int $userId): array
{
    if ($userId <= 0)
    {
        return [];
    }

    $permissions = [];

    $sql = "
        $sql = "SELECT DISTINCT
			   p.permission_key,
			   p.display_order
		FROM user_roles ur
		INNER JOIN role_permissions rp
				ON rp.role_id = ur.role_id
		INNER JOIN permissions p
				ON p.permission_id = rp.permission_id
		WHERE ur.user_id = ?
		  AND ur.is_active = 1
		  AND rp.is_active = 1
		  AND p.is_active = 1
		ORDER BY
			  p.display_order,
			  p.permission_key
";
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt)
    {
        throw new Exception(
            'Unable to prepare permission query. ' . mysqli_error($conn)
        );
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result))
    {
        $permissions[$row['permission_key']] = true;
    }

    mysqli_stmt_close($stmt);

    return $permissions;
}

/******************************************************************************
 * Returns all active roles assigned to a user.
 ******************************************************************************/
function getUserRoles(mysqli $conn, int $userId): array
{
    if ($userId <= 0)
    {
        return [];
    }

    $roles = [];

    $sql = "
        SELECT DISTINCT
               r.role_name
        FROM user_roles ur
        INNER JOIN roles r
                ON r.role_id = ur.role_id
        WHERE ur.user_id = ?
          AND ur.is_active = 1
          AND r.is_active = 1
        ORDER BY r.role_name
    ";

    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt)
    {
        throw new Exception(
            'Unable to prepare role query. ' .
            mysqli_error($conn)
        );
    }

    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result))
    {
        $roles[] = $row['role_name'];
    }

    mysqli_stmt_close($stmt);

    return $roles;
}

/******************************************************************************
 * Returns the permissions loaded into the current session.
 ******************************************************************************/
function getSessionPermissions(): array
{
    return $_SESSION['permissions'] ?? [];
}

/******************************************************************************
 * Checks whether the current user has the specified permission.
 ******************************************************************************/
function hasPermission(string $permissionKey): bool
{
    if (empty($_SESSION['permissions']))
    {
        return false;
    }

    return isset($_SESSION['permissions'][$permissionKey]);
}

/******************************************************************************
 * Checks whether the current user has any of the supplied permissions.
 *
 * @param array $permissionKeys
 *
 * @return bool
 ******************************************************************************/
function hasAnyPermission(array $permissionKeys): bool
{
    foreach ($permissionKeys as $permission)
    {
        if (hasPermission($permission))
        {
            return true;
        }
    }

    return false;
}
/******************************************************************************
 * Alias for hasPermission().
 ******************************************************************************/
function userCan(string $permissionKey): bool
{
    return hasPermission($permissionKey);
}

/******************************************************************************
 * Redirects the user when access is denied.
 ******************************************************************************/
function denyAccess(): void
{
    $_SESSION['error_message'] =
        'You do not have permission to access the requested page.';

	redirect('dashboard.php');
}

/******************************************************************************
 * Requires a specific permission.
 ******************************************************************************/
function requirePermission(string $permissionKey): void
{
    if (!hasPermission($permissionKey))
    {
        denyAccess();
    }
}

/******************************************************************************
 * Requires any one of the supplied permissions.
 ******************************************************************************/
function requireAnyPermission(array $permissionKeys): void
{
    foreach ($permissionKeys as $permission)
    {
        if (hasPermission($permission))
        {
            return;
        }
    }

    denyAccess();
}

/******************************************************************************
 * Requires all supplied permissions.
 ******************************************************************************/
function requireAllPermissions(array $permissionKeys): void
{
    foreach ($permissionKeys as $permission)
    {
        if (!hasPermission($permission))
        {
            denyAccess();
        }
    }
}

/******************************************************************************
 * Reloads the logged-in user's permissions from the database.
 ******************************************************************************/
function refreshUserPermissions(mysqli $conn): void
{
    if (!isset($_SESSION['user_id']))
    {
        return;
    }

    $_SESSION['permissions'] =
        getUserPermissions($conn, (int)$_SESSION['user_id']);
}

function hasAllPermissions(array $permissionKeys): bool
{
    foreach ($permissionKeys as $permission)
    {
        if (!hasPermission($permission))
        {
            return false;
        }
    }

    return true;
}

/******************************************************************************
 * Returns TRUE if the logged-in user has Administrator role.
 ******************************************************************************/
function isAdministrator(): bool
{
    return in_array(
        'Administrator',
        $_SESSION['roles'] ?? [],
        true
    );
}