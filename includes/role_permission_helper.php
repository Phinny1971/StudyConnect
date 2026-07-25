<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Role Management
 * File     : role_permission_helper.php
 * Purpose  : Helper functions for Role Permission Management
 *
 * Version  : 1.0
 ******************************************************************************/

/******************************************************************************
 * Returns all active permissions grouped by Module and Feature.
 ******************************************************************************/
function getPermissionsGrouped(mysqli $conn): array
{
    $permissions = [];

    $sql = "
        SELECT
            permission_id,
            module_name,
            feature_name,
            action_name,
            permission_key,
            description
        FROM permissions
        WHERE is_active = 1
        ORDER BY
            display_order,
            module_name,
            feature_name,
            action_name
    ";

    $result = $conn->query($sql);

    if (!$result)
    {
        throw new Exception($conn->error);
    }

    while ($row = $result->fetch_assoc())
    {
        $permissions
            [$row['module_name']]
            [$row['feature_name']][] = $row;
    }

    return $permissions;
}

/******************************************************************************
 * Returns all permissions assigned to a role.
 *
 * Result:
 * [
 *      permission_id => true
 * ]
 ******************************************************************************/
function getRolePermissions(
    mysqli $conn,
    int $roleId
): array
{
    $assigned = [];

    $sql = "
        SELECT permission_id
        FROM role_permissions
        WHERE role_id = ?
          AND is_active = 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt)
    {
        throw new Exception($conn->error);
    }

    $stmt->bind_param("i", $roleId);

    $stmt->execute();

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc())
    {
        $assigned[(int)$row['permission_id']] = true;
    }

    $stmt->close();

    return $assigned;
}

/******************************************************************************
 * Saves permissions assigned to a role.
 ******************************************************************************/
function saveRolePermissions(
    mysqli $conn,
    int $roleId,
    array $permissionIds,
    int $createdByUserId
): bool
{
    $conn->begin_transaction();

    try
    {
        /*
        |--------------------------------------------------------------
        | Deactivate Existing Assignments
        |--------------------------------------------------------------
        */

        // Remove all existing permissions
		$stmt = $conn->prepare("
			DELETE
			FROM role_permissions
			WHERE role_id = ?
		");

		$stmt->bind_param("i", $roleId);
		$stmt->execute();
		$stmt->close();

        /*
        |--------------------------------------------------------------
        | Insert New Assignments
        |--------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            INSERT INTO role_permissions
            (
                role_id,
                permission_id,
                created_by_user_id,
                is_active
            )
            VALUES
            (
                ?, ?, ?, 1
            )
        ");

        foreach ($permissionIds as $permissionId)
        {
            $permissionId = (int)$permissionId;

            $stmt->bind_param(
                "iii",
                $roleId,
                $permissionId,
                $createdByUserId
            );

            $stmt->execute();
        }

        $stmt->close();

        $conn->commit();

        return true;
    }
    catch (Throwable $ex)
    {
        $conn->rollback();

        throw $ex;
    }
}