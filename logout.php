<?php
session_set_cookie_params([
    'lifetime' => 0,
    'httponly' => true,
    'secure'   => true,
    'samesite' => 'Strict'
]);

session_start();
$_SESSION = array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
$_SESSION = [];
session_destroy();
setcookie(session_name(), '', time() - 3600, '/');
header("Location: login.php");
exit;exit;
?>