<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : User Administration
 * File     : user_helper.php
 * Purpose  : Helper functions for User Administration
 *
 * Version  : 1.0
 ******************************************************************************/

/**
 * Returns complete information about a user.
 *
 * @param mysqli $conn
 * @param int    $user_id
 *
 * @return array|null
 */
 
require_once __DIR__ . '/password_helper.php';
require_once __DIR__ . '/email_helper.php';

function getUserSummary(mysqli $conn, int $user_id): ?array
{
    $sql = "
        SELECT
            u.user_id,
            u.email,
            u.first_name,
            u.last_name,
            u.display_name,

            s.status_id,
            s.status_code,
            s.status_name

        FROM users u

        INNER JOIN account_statuses s
            ON u.status_id = s.status_id

        WHERE u.user_id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die($conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        return null;
    }

    $user = $result->fetch_assoc();

    /*
    |--------------------------------------------------------------------------
    | Branches
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT branch_name
        FROM user_branches
        WHERE user_id = ?
          AND is_active = 1
        ORDER BY branch_name
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $branches = [];

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $branches[] = $row['branch_name'];
    }

    $user['branches'] = $branches;

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT r.role_name
        FROM user_roles ur

        INNER JOIN roles r
            ON ur.role_id = r.role_id

        WHERE ur.user_id = ?
          AND ur.is_active = 1
          AND r.is_active = 1

        ORDER BY r.role_name
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $roles = [];

    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['role_name'];
    }

    $user['roles'] = $roles;

    return $user;
}

/**
 * Resets a user's password.
 *
 * Updates the password hash, forces a password change on next login,
 * clears failed login attempts, unlocks the account, and records the
 * administrator who performed the reset.
 *
 * @param mysqli $conn
 * @param int    $userId
 * @param string $passwordHash
 * @param int    $updatedByUserId
 *
 * @return bool
 */
function resetUserPassword(
     mysqli $conn,
    int $userId,
    string $passwordHash,
    int $updatedByUserId
): bool
{

	$stmt = $conn->prepare("
    UPDATE users
       SET password_hash = ?,
           force_password_change = 1,
           failed_login_attempts = 0,
           locked_until = NULL,
           updated_by_user_id = ?,
           updated_on = NOW()
     WHERE user_id = ?
	");

	if (!$stmt)
	{
		die($conn->error);
	}

	$stmt->bind_param(
    "sii",
    $passwordHash,
    $updatedByUserId,
    $userId
	);

	$result = $stmt->execute();

	$stmt->close();

	return $result;

}

/******************************************************************************
 * Reset a user's password and notify them via email.
 *
 * Parameters:
 *      mysqli $conn
 *      array  $user
 *      int    $performedByUserId
 *
 * Returns:
 *      array
 ******************************************************************************/
function resetUserPasswordAndNotify(
    mysqli $conn,
    array $user,
    int $performedByUserId
): array
{
    $result = [

        'success'   => false,
        'emailSent' => false,
        'message'   => ''

    ];

    // Generate a secure temporary password.
    $temporaryPassword = generateTemporaryPassword();

    // Hash the temporary password.
    $passwordHash = hashPassword($temporaryPassword);

    // Reset the password in the database.
    $success = resetUserPassword(
        $conn,
        (int)$user['user_id'],
        $passwordHash,
        $performedByUserId
    );

    if (!$success)
    {
        $result['message'] =
            "Unable to reset the password. Please try again.";

        return $result;
    }

    $result['success'] = true;

    // Send the password reset email.
	try
	{
		$result['emailSent'] = sendPasswordResetEmail(
			$user['email'],
			$user['display_name'],
			$temporaryPassword
		);
	}
	catch (Throwable $ex)
	{
		$result['emailSent'] = false;
	}
	
	
    if ($result['emailSent'])
    {
        $result['message'] =
            "Password reset successfully. A temporary password has been sent to the user's registered email address.";
    }
    else
    {
        $result['message'] =
            "Password reset successfully, but the email notification could not be sent.";
    }

    return $result;
}

/**
 * Returns a user by ID.
 *
 * @param mysqli $conn
 * @param int    $userId
 *
 * @return array|null
 */
function getUserById(mysqli $conn, int $userId): ?array
{
    $sql = "
        SELECT *
        FROM users
        WHERE user_id = ?
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        return null;
    }

    $user = $result->fetch_assoc();

    $stmt->close();

    return $user;
}

/**
 * Updates a user's password and clears
 * force password change requirements.
 *
 * @param mysqli $conn
 * @param int    $userId
 * @param string $newPasswordHash
 *
 * @return bool
 */
function updateUserPassword(
    mysqli $conn,
    int $userId,
    string $newPasswordHash
): bool
{
    $sql = "
        UPDATE users
        SET
            password_hash = ?,
            force_password_change = 0,
            failed_login_attempts = 0,
            locked_until = NULL
        WHERE user_id = ?
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "si",
        $newPasswordHash,
        $userId
    );

    $success = $stmt->execute();

    $stmt->close();

    return $success;
}