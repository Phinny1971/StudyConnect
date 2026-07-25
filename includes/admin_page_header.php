<?php
/******************************************************************************
 * StudyConnect
 *
 * File    : admin_page_header.php
 * Purpose : Standard page heading for User Administration pages
 ******************************************************************************/

if (!isset($pageHeading))
{
    $pageHeading = "";
}

if (!isset($pageSubHeading))
{
    $pageSubHeading = "";
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h2 class="admin-page-title mb-1">

            <?= htmlspecialchars($pageHeading) ?>

        </h2>

        <?php if(!empty($pageSubHeading)){ ?>

            <small class="text-muted">

                <?= htmlspecialchars($pageSubHeading) ?>

            </small>

        <?php } ?>

    </div>

</div>

<hr>