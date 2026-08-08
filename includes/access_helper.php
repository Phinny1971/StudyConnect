<?php
/******************************************************************************
 * StudyConnect
 *
 * File    : includes/access_helper.php
 * Purpose : Centralized Branch Access Helper
 ******************************************************************************/
 
 declare(strict_types=1);

/**
 * Returns the logged-in user's ID.
 *
 * @return int
 */
function getLoggedInUserId(): int
{
    return (int)($_SESSION['user_id'] ?? 0);
}

/**
 * Returns all active branches assigned to the logged-in user.
 *
 * @param mysqli $conn
 * @return array
 */
function getAccessibleBranches(mysqli $conn): array
{
    $userId = getLoggedInUserId();

    if ($userId <= 0) {
        return [];
    }

    $sql = "
        SELECT
            b.branch_id,
            b.Branch_name,
            b.Branch_location
        FROM user_branches ub
        INNER JOIN branches b
            ON b.Branch_name = ub.branch_name
        WHERE
            ub.user_id = ?
            AND ub.is_active = 1
        ORDER BY
            b.Branch_location,
            b.Branch_name
    ";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();

    $result = $stmt->get_result();

    $branches = [];

    while ($row = $result->fetch_assoc()) {
        $branches[] = $row;
    }

    $stmt->close();

    return $branches;
}

/**
 * Checks whether the logged-in user can access a branch.
 *
 * @param mysqli $conn
 * @param string $branchName
 * @return bool
 */
function canAccessBranch(mysqli $conn, string $branchName): bool
{
    $branches = getAccessibleBranches($conn);

    foreach ($branches as $branch) {

        if ($branch['Branch_name'] === $branchName) {
            return true;
        }

    }

    return false;
}

/**
 * Returns TRUE if the user has access to all branches.
 *
 * Version 1:
 * A user with access to all branches will simply be assigned
 * every branch in user_branches.
 *
 * @param mysqli $conn
 * @return bool
 */
function canAccessAllBranches(mysqli $conn): bool
{
    $sql = "SELECT COUNT(*) AS total FROM branches";
    $result = $conn->query($sql);
    if (!$result) {
        return false;
    }
    $totalBranches = (int)$result->fetch_assoc()['total'];
    $assignedBranches = count(getAccessibleBranches($conn));
    return ($assignedBranches >= $totalBranches);
}