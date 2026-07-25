<?php
/******************************************************************************
 * StudyConnect
 * Common Admin Header
 ******************************************************************************/

if(!isset($pageTitle))
{
    $pageTitle = "StudyConnect";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="utf-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1">

<title><?= htmlspecialchars($pageTitle) ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<link rel="stylesheet"
href="css/style.css">

<link rel="stylesheet"
href="css/admin.css">

</head>

<body class="p-4">

<?php
require_once 'includes/flash_messages.php';
?>