<?php
/******************************************************************************
 * StudyConnect
 *
 * File    : flash_messages.php
 * Purpose : Displays Success / Error messages
 ******************************************************************************/

if (!empty($_SESSION['success_message']))
{
?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">

        <i class="fa fa-circle-check"></i>

        <?= htmlspecialchars($_SESSION['success_message']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php

    unset($_SESSION['success_message']);
}

if (!empty($_SESSION['error_message']))
{
?>

    <div class="alert alert-danger alert-dismissible fade show" role="alert">

        <i class="fa fa-circle-xmark"></i>

        <?= htmlspecialchars($_SESSION['error_message']) ?>

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert">
        </button>

    </div>

<?php

    unset($_SESSION['error_message']);
}
?>