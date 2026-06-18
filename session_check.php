<?php

ini_set('session.cookie_httponly', 1);

if (!empty($_SERVER['HTTPS'])) {
    ini_set('session.cookie_secure', 1);
}

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$timeout = 1800; // 30 minutes
//$timeout = 180; // 3 minutes

function forceLogout()
{
    session_unset();
    session_destroy();

    echo "
    <script>
       if (window.top !== window.self) {
			window.top.location.href = 'login.php?expired=1';
		} else {
			window.location.href = 'login.php?expired=1';
		}
    </script>
    ";
    exit();
}

if (
    isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $timeout
) {
    forceLogout();
}

$_SESSION['last_activity'] = time();

if (!isset($_SESSION['email'])) {
    forceLogout();
}


