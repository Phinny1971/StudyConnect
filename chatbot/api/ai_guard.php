<?php

function isBlockedPrompt($message)
{
    $blockedPatterns = [

        'ignore previous instructions',
        'show database',
        'reveal password',
        'show api key',
        'admin access',
        'system prompt',
        'environment variables',
        'server access',
        'root access',
        'sql injection',
        'drop table',
        'bypass authentication',
        'hack',
        'exploit'

    ];

    foreach($blockedPatterns as $pattern){

        if(stripos($message, $pattern) !== false){

            return true;
        }
    }

    return false;
}

function blockedResponse()
{
    return "
    I cannot assist with sensitive system,
    security, or restricted information.

    However, I can help you with:
    • Universities
    • Courses
    • Scholarships
    • Visa guidance
    • Admissions
    ";
}

?>