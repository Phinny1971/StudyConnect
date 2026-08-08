<?php

function cleanString($value)
{
    return trim($value ?? '');
}

function requireField($value, $fieldName)
{
    if (trim($value) === '') {
        throw new Exception($fieldName . " is required.");
    }

    return trim($value);
}

function validateEmail($email)
{
    $email = trim($email);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception("Invalid email address.");
    }

    return $email;
}

function validatePhone($phone)
{
    $phone = trim($phone);

    if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
        throw new Exception("Invalid phone number.");
    }

    return $phone;
}

function nullIfEmpty($value)
{
    return ($value === '' || $value === null)
        ? null
        : trim($value);
}

function decimalOrNull($value)
{
    return ($value === '' || $value === null)
        ? null
        : (float)$value;
}

function intOrNull($value)
{
    return ($value === '' || $value === null)
        ? null
        : (int)$value;
}

function dateOrNull($value)
{
    return ($value === '' || $value === null)
        ? null
        : $value;
}

function uploadFile($fieldName, $existingFile = "", $uploadDir = "uploads")
{
    $uploadDir = rtrim($uploadDir, "/\\");

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true) && !is_dir($uploadDir)) {
            throw new Exception("Unable to create upload directory.");
        }
    }

    if (
        !isset($_FILES[$fieldName]) ||
        $_FILES[$fieldName]['error'] == UPLOAD_ERR_NO_FILE
    ) {
        return $existingFile;
    }

    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("Error uploading {$fieldName}");
    }

    if (!is_uploaded_file($_FILES[$fieldName]['tmp_name'])) {
        throw new Exception("Invalid uploaded file.");
    }

    $maxSize = 5 * 1024 * 1024;

    if ($_FILES[$fieldName]['size'] > $maxSize) {
        throw new Exception("File exceeds 5 MB.");
    }

    $allowedMimeTypes = [
        'application/pdf' => 'pdf',
        'image/jpeg'      => 'jpg',
        'image/png'       => 'png',
        'application/msword' => 'doc',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
        'application/vnd.ms-excel' => 'xls',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    $mime = finfo_file($finfo, $_FILES[$fieldName]['tmp_name']);

    finfo_close($finfo);

    if (!isset($allowedMimeTypes[$mime])) {
        throw new Exception("Invalid file type.");
    }

    $extension = $allowedMimeTypes[$mime];

    $filename = bin2hex(random_bytes(16)) . "." . $extension;

    $target = $uploadDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target)) {
        throw new Exception("Unable to save uploaded file.");
    }

    return $target;
}
