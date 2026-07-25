<?php

require_once 'includes/db_connection.php';
require_once 'includes/permission_helper.php';

echo '<pre>';

print_r(getUserPermissions($conn, 16));

echo '</pre>';