<?php

function cleanInput($data)
{
    return htmlspecialchars(
        trim($data),
        ENT_QUOTES,
        'UTF-8'
    );
}

function allowedFile($fileName)
{
    $allowed = ['pdf', 'docx', 'jpg', 'jpeg', 'png'];

    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    return in_array($ext, $allowed);
}

function formatTimestamp($datetime)
{
    return date("d M Y h:i A", strtotime($datetime));
}

function containsBlockedPrompt($message)
{
    $blocked = [
        'password',
        'database',
        'db credentials',
        'api key',
        'server access',
        'show source code',
        'system prompt',
        'environment variables',
        'admin password'
    ];

    foreach ($blocked as $word) {

        if (stripos($message, $word) !== false) {
            return true;
        }
    }

    return false;
}

?>