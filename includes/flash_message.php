<?php
/******************************************************************************
 * Flash Message Helper
 * Purpose : Store one-time messages across redirects
 ******************************************************************************/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Set a flash message.
 *
 * @param string $type    success|error|warning|info
 * @param string $title
 * @param string $message
 */
function setFlashMessage(string $type, string $title, string $message): void
{
    $_SESSION['flash_message'] = [
        'type'    => $type,
        'title'   => $title,
        'message' => $message
    ];
}

/**
 * Retrieve and remove the flash message.
 *
 * @return array|null
 */
function getFlashMessage(): ?array
{
    if (empty($_SESSION['flash_message'])) {
        return null;
    }

    $flash = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);

    return $flash;
}