<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Role Management
 * File     : role_helper.php
 * Purpose  : Helper functions for Role Administration
 *
 * Version  : 1.0
 ******************************************************************************/

/******************************************************************************
 * Returns a role by ID.
 *
 * @param mysqli $conn
 * @param int    $roleId
 *
 * @return array|null
 ******************************************************************************/
function getRoleById(mysqli $conn, int $roleId): ?array
{
    $sql = "
        SELECT
            role_id,
            role_name,
            description,
            is_system_generated,
            is_active,
            created_by_user_id,
            created_on,
            updated_by_user_id,
            updated_on
        FROM roles
        WHERE role_id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt)
    {
        return null;
    }

    $stmt->bind_param("i", $roleId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0)
    {
        $stmt->close();
        return null;
    }

    $role = $result->fetch_assoc();

    $stmt->close();

    return $role;
}

/******************************************************************************
 * Checks whether a role name already exists.
 *
 * @param mysqli $conn
 * @param string $roleName
 * @param int    $excludeRoleId
 *
 * @return bool
 ******************************************************************************/
function roleExists(
    mysqli $conn,
    string $roleName,
    int $excludeRoleId = 0
): bool
{
    $sql = "
        SELECT role_id
        FROM roles
        WHERE LOWER(role_name) = LOWER(?)
          AND role_id <> ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt)
    {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        "si",
        $roleName,
        $excludeRoleId
    );

    $stmt->execute();
    $stmt->store_result();

    $exists = ($stmt->num_rows > 0);

    $stmt->close();

    return $exists;
}

/******************************************************************************
 * Creates a new role.
 *
 * @param mysqli $conn
 * @param string $roleName
 * @param string $description
 * @param int    $isActive
 * @param int    $createdByUserId
 *
 * @return bool
 ******************************************************************************/
function createRole(
    mysqli $conn,
    string $roleName,
    string $description,
    int $isActive,
    int $createdByUserId
): bool
{
    $sql = "
        INSERT INTO roles
        (
            role_name,
            description,
            is_active,
            created_by_user_id,
            created_on
        )
        VALUES
        (
            ?,
            ?,
            ?,
            ?,
            NOW()
        )
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt)
    {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        "ssii",
        $roleName,
        $description,
        $isActive,
        $createdByUserId
    );

    $success = $stmt->execute();

    $stmt->close();

    return $success;
}

/******************************************************************************
 * Updates an existing role.
 *
 * @param mysqli $conn
 * @param int    $roleId
 * @param string $roleName
 * @param string $description
 * @param int    $isActive
 * @param int    $updatedByUserId
 *
 * @return bool
 ******************************************************************************/
function updateRole(
    mysqli $conn,
    int $roleId,
    string $roleName,
    string $description,
    int $isActive,
    int $updatedByUserId
): bool
{
    $sql = "
        UPDATE roles
        SET
            role_name = ?,
            description = ?,
            is_active = ?,
            updated_by_user_id = ?,
            updated_on = NOW()
        WHERE role_id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt)
    {
        throw new Exception($conn->error);
    }

    $stmt->bind_param(
        "ssiii",
        $roleName,
        $description,
        $isActive,
        $updatedByUserId,
        $roleId
    );

    $success = $stmt->execute();

    $stmt->close();

    return $success;
}

