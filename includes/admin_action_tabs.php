<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : User Administration
 * File     : admin_action_tabs.php
 * Purpose  : User Administration Navigation Tabs
 *
 * Version  : 1.0
 ******************************************************************************/

if (!isset($activeTab))
{
    $activeTab = "";
}
?>

<ul class="nav nav-tabs mb-3">

    <li class="nav-item">

        <a
        class="nav-link <?= ($activeTab=="profile") ? "active" : "" ?>"
        href="user_form.php?user_id=<?= $userId ?>">

            <i class="fa fa-user"></i>

            Profile

        </a>

    </li>

    <li class="nav-item">

        <a
        class="nav-link <?= ($activeTab=="roles") ? "active" : "" ?>"
        href="assign_roles.php?user_id=<?= $userId ?>">

            <i class="fa fa-user-shield"></i>

            Roles

        </a>

    </li>

    <li class="nav-item">

        <a
        class="nav-link <?= ($activeTab=="branches") ? "active" : "" ?>"
        href="assign_branches.php?user_id=<?= $userId ?>">

            <i class="fa fa-building"></i>

            Branches

        </a>

    </li>

    <li class="nav-item">

        <a
        class="nav-link <?= ($activeTab=="password") ? "active" : "" ?>"
        href="reset_user_password.php?user_id=<?= $userId ?>">

            <i class="fa fa-key"></i>

            Password

        </a>

    </li>

    <li class="nav-item">

        <a
        class="nav-link disabled">

            <i class="fa fa-history"></i>

            Audit Log

        </a>

    </li>

</ul>