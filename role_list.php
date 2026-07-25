<?php
/******************************************************************************
 * StudyConnect
 *
 * Module   : Security / RBAC
 * Page     : role_list.php
 * Purpose  : Displays application roles.
 *
 * Version  : 1.0
 * Updated  : 21-Jul-2026
 *
 * Changes:
 *   - Initial Role Management implementation
 *   - Administrator-only access
 *   - Uses common admin UI framework
 ******************************************************************************/

require_once 'session_check.php';
require_once 'includes/db_connection.php';
require_once 'includes/permission_helper.php';


/*
|--------------------------------------------------------------------------
| Administrator Access Only
|--------------------------------------------------------------------------
*/

if (!isAdministrator())
{
    denyAccess();
}



/*
|--------------------------------------------------------------------------
| Page Information
|--------------------------------------------------------------------------
*/

$pageTitle      = "Role Management";
$pageHeading    = "Role Management";
$pageSubHeading = "Create and manage application roles.";



/*
|--------------------------------------------------------------------------
| Fetch Roles
|--------------------------------------------------------------------------
*/

$sql = "

SELECT

    r.role_id,
    r.role_name,
    r.description,
    r.is_system_generated,
    r.is_active,
    r.created_on,

    COALESCE(u.total_users,0) AS total_users


FROM roles r 


LEFT JOIN
(
    SELECT

        role_id,
        COUNT(*) AS total_users

    FROM user_roles

    WHERE is_active = 1

    GROUP BY role_id

) u

ON r.role_id = u.role_id
WHERE r.is_active = 1

ORDER BY r.role_name

";


$roleResult = $conn->query($sql);


if (!$roleResult)
{
    die($conn->error);
}

?>



<?php

require_once 'includes/admin_header.php';
include 'includes/admin_page_header.php';

?>



<div class="d-flex justify-content-between align-items-center mb-3">


<button
    class="btn btn-primary"
    onclick="location.href='role_form.php'">

    <i class="fa fa-user-shield"></i>
    New Role

</button>


</div>





<div class="card shadow-sm">


<div class="card-header sc-card-header">

<h5 class="mb-0">

<i class="fa fa-user-shield"></i>
Application Roles

</h5>

</div>





<div class="card-body">


<table id="rolesTable"
       class="display nowrap table table-hover table-sm"
       style="width:100%">



<thead>

<tr>

<th>Role Name</th>

<th>Description</th>

<th>Users</th>

<th>Type</th>

<th>Status</th>

<th>Created On</th>

<th style="width:220px;">
Actions
</th>


</tr>

</thead>




<tbody>


<?php while ($role = $roleResult->fetch_assoc()) { ?>


<?php


$roleId = (int)$role['role_id'];



$description =
    empty($role['description'])
    ? "--"
    : $role['description'];



$totalUsers =
    (int)$role['total_users'];



/*
|--------------------------------------------------------------------------
| Role Type
|--------------------------------------------------------------------------
*/

if ($role['is_system_generated'])
{
    $typeBadge =
        '<span class="badge bg-primary">
            System
         </span>';
}
else
{
    $typeBadge =
        '<span class="badge bg-secondary">
            Custom
         </span>';
}




/*
|--------------------------------------------------------------------------
| Status
|--------------------------------------------------------------------------
*/

$statusClass =
    $role['is_active']
    ? "status-active"
    : "status-inactive";


$statusText =
    $role['is_active']
    ? "Active"
    : "Inactive";




/*
|--------------------------------------------------------------------------
| Delete Rules
|--------------------------------------------------------------------------
|
| System roles cannot be deleted.
| Roles assigned to users cannot be deleted.
|
*/

$canDelete = true;


if ($role['is_system_generated'])
{
    $canDelete = false;
}


if ($totalUsers > 0)
{
    $canDelete = false;
}


?>



<tr>


<td>

<?= e($role['role_name']) ?>

</td>




<td>

<?= e($description) ?>

</td>




<td>

<?= $totalUsers ?>

</td>




<td>

<?= $typeBadge ?>

</td>




<td>


<span class="status-badge <?= $statusClass ?>">

<?= $statusText ?>

</span>


</td>




<td>

<?= date(
        "d-M-Y",
        strtotime($role['created_on'])
    )
?>

</td>





<td class="actions-column">


<div class="d-flex gap-1">


<a
href="role_form.php?role_id=<?= $roleId ?>"
class="btn btn-sm btn-primary">

<i class="fa fa-edit"></i>
Edit

</a>





<a
href="assign_role_permissions.php?role_id=<?= $roleId ?>"
class="btn btn-sm btn-info">

<i class="fa fa-key"></i>
Permissions

</a>




<?php if ($canDelete) { ?>

<form
    method="post"
    action="delete_role.php"
    class="d-inline">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= $_SESSION['csrf_token'] ?>">

    <input
        type="hidden"
        name="role_id"
        value="<?= $roleId ?>">

    <button
        type="submit"
        class="btn btn-sm btn-danger"
        onclick="return confirm('Are you sure you want to deactivate this role?');">

        <i class="fa fa-trash"></i>
        Delete

    </button>

</form>

<?php } else { ?>

<button
    class="btn btn-sm btn-secondary"
    disabled
    title="System role or role assigned to users">

    <i class="fa fa-trash"></i>
    Delete

</button>

<?php } ?>


</div>


</td>




</tr>



<?php } ?>


</tbody>


</table>


</div>


</div>






<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>





<?php

require_once 'includes/admin_footer.php';

?>





<script>


$(document).ready(function(){



if ($.fn.DataTable.isDataTable('#rolesTable'))
{
    $('#rolesTable').DataTable().destroy();
}




$('#rolesTable').DataTable({

    pageLength: 10,

    responsive:false,

    autoWidth:false,

    scrollX:true,


    dom:
    "<'row mb-3'<'col-md-6'B><'col-md-6'f>>" +
    "rt" +
    "<'row mt-3'<'col-md-6'i><'col-md-6'p>>",


    buttons:
    [
        'excel',
        'pdf',
        'print'
    ]

});


});


</script>