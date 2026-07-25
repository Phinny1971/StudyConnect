<?php
/******************************************************************************
 * StudyConnect
 *
 * File    : helpers.php
 * Purpose : Common reusable helper functions
 *
 * Version : 1.0
 ******************************************************************************/

/**
 * Redirect to another page
 */
function redirect($url)
{
	session_write_close();
    header("Location: " . $url);
    exit;
}

/**
 * Trim whitespace
 */
function clean($value)
{
    return trim($value);
}

/**
 * Safe HTML output
 */
function e($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Check POST request
 */
function isPost()
{
	if (!validateCsrfToken($_POST['csrf_token'] ?? ''))
	{
		$errors[] = 'Invalid request. Please refresh the page and try again.';
	}
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check GET request
 */
function isGet()
{
    return $_SERVER['REQUEST_METHOD'] === 'GET';
}

/******************************************************************************
 * Validates CSRF Token
 *
 * @param string $token
 *
 * @return bool
 ******************************************************************************/
function validateCsrfToken(string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}