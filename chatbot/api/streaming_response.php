<?php

header('Content-Type: text/event-stream');

header('Cache-Control: no-cache');

require_once '../config/openai_config.php';

$message = $_GET['message'] ?? '';

$postData = [

    "model" => OPENAI_MODEL,

    "stream" => true,

    "messages" => [

        [
            "role" => "system",
            "content" => "You are MAGI AI."
        ],

        [
            "role" => "user",
            "content" => $message
        ]
    ]
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,
'https://api.openai.com/v1/chat/completions');

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [

    'Content-Type: application/json',
    'Authorization: Bearer ' . OPENAI_API_KEY

]);

curl_setopt($ch, CURLOPT_POSTFIELDS,
json_encode($postData));

curl_setopt($ch, CURLOPT_WRITEFUNCTION,

function($ch, $data){

    echo $data;

    ob_flush();
    flush();

    return strlen($data);
});

curl_exec($ch);

curl_close($ch);

?>