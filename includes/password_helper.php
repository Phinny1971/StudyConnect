<?php
/******************************************************************************
 * StudyConnect
 *
 * File      : password_helper.php
 * Purpose   : Provides reusable password utility functions.
 *
 * Version   : 1.0
 * Updated   : 15-Jul-2026
 *
 * Functions :
 *   - generateTemporaryPassword()
 *   - hashPassword()
 *   - verifyPassword()
 *   - passwordNeedsRehash()
 *
 ******************************************************************************/

declare(strict_types=1);

/**
 * Generates a secure temporary password.
 *
 * Default composition:
 *   - 3 Uppercase letters
 *   - 3 Lowercase letters
 *   - 2 Digits
 *   - 2 Special characters
 *
 * Ambiguous characters such as O, 0, I, l and 1 are intentionally excluded.
 *
 * Example:
 *   QaMrtx58@#
 *
 * @return string
 */
function generateTemporaryPassword(): string
{
    $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
    $lower   = 'abcdefghijkmnopqrstuvwxyz';
    $digits  = '23456789';
    $special = '@#$%&*!?';

    $password = [];

    // Uppercase characters
    for ($i = 0; $i < 3; $i++) {
        $password[] = $upper[random_int(0, strlen($upper) - 1)];
    }

    // Lowercase characters
    for ($i = 0; $i < 3; $i++) {
        $password[] = $lower[random_int(0, strlen($lower) - 1)];
    }

    // Digits
    for ($i = 0; $i < 2; $i++) {
        $password[] = $digits[random_int(0, strlen($digits) - 1)];
    }

    // Special characters
    for ($i = 0; $i < 2; $i++) {
        $password[] = $special[random_int(0, strlen($special) - 1)];
    }

    // Cryptographically shuffle the array
    for ($i = count($password) - 1; $i > 0; $i--) {
        $j = random_int(0, $i);
        [$password[$i], $password[$j]] = [$password[$j], $password[$i]];
    }

    return implode('', $password);
}

/**
 * Creates a secure password hash.
 *
 * @param string $password
 * @return string
 */
function hashPassword(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verifies a password against its hash.
 *
 * @param string $password
 * @param string $hash
 *
 * @return bool
 */
function verifyPassword(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * Determines whether an existing password hash should be upgraded.
 *
 * @param string $hash
 *
 * @return bool
 */
function passwordNeedsRehash(string $hash): bool
{
    return password_needs_rehash($hash, PASSWORD_DEFAULT);
}

/**
 * Validates a password against the StudyConnect password policy.
 *
 * Rules:
 * - Minimum 8 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one digit
 * - At least one special character
 *
 * @param string $password
 *
 * @return array
 */
function validatePasswordPolicy(string $password): array
{
    $errors = [];

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain at least one digit.';
    }

    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = 'Password must contain at least one special character.';
    }

    return [
        'valid'  => empty($errors),
        'errors' => $errors
    ];
}