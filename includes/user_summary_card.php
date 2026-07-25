<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : User Administration
 * File     : user_summary_card.php
 * Purpose  : Displays a summary of the selected user
 *
 * Version  : 1.0
 ******************************************************************************/

if (!isset($user))
{
    return;
}
?>

<style>
.sc-card-header
{
    background: #f8f9fa;
    border-bottom: 1px solid #dcdcdc;
}

.sc-card-header h5
{
    margin: 0;
    font-weight: 600;
}
</style>

<div class="card shadow-sm mb-3">

    <div class="card-header sc-card-header">

        <h5 class="mb-0">
            <i class="fa fa-user"></i>
            User Information
        </h5>

    </div>

    <div class="card-body">

        <div class="row mb-2">

            <div class="col-md-2 fw-bold">
                Name
            </div>

            <div class="col-md-4">
                <?= htmlspecialchars($user['display_name']) ?>
            </div>

            <div class="col-md-2 fw-bold">
                Email
            </div>

            <div class="col-md-4">
                <?= htmlspecialchars($user['email']) ?>
            </div>

        </div>

        <div class="row mb-2">

            <div class="col-md-2 fw-bold">
                Status
            </div>

            <div class="col-md-4">

                <?php

                $badge = "secondary";

                switch ($user['status_code'])
                {
                    case "ACTIVE":
                        $badge = "success";
                        break;

                    case "INACTIVE":
                        $badge = "secondary";
                        break;

                    case "LOCKED":
                        $badge = "danger";
                        break;

                    case "EXPIRED":
                        $badge = "warning";
                        break;
                }

                ?>

                <span class="badge bg-<?= $badge ?>">

                    <?= htmlspecialchars($user['status_name']) ?>

                </span>

            </div>

            <div class="col-md-2 fw-bold">
                Roles
            </div>

            <div class="col-md-4">

                <?php
                if (!empty($user['roles']))
                {
                    foreach ($user['roles'] as $role)
                    {
                        ?>
                        <span class="badge bg-primary me-1 mb-1">
                            <?= htmlspecialchars($role) ?>
                        </span>
                        <?php
                    }
                }
                else
                {
                    echo "<span class='text-muted'>None Assigned</span>";
                }
                ?>

            </div>

        </div>

        <div class="row">

            <div class="col-md-2 fw-bold">
                Branches
            </div>

            <div class="col-md-10">

                <?php

                if (!empty($user['branches']))
                {
                    foreach ($user['branches'] as $branch)
                    {
                        ?>

                        <span class="badge bg-info text-dark me-1 mb-1">

                            <?= htmlspecialchars($branch) ?>

                        </span>

                        <?php
                    }
                }
                else
                {
                    echo "<span class='text-muted'>No Branch Assigned</span>";
                }

                ?>

            </div>

        </div>

    </div>

</div>
