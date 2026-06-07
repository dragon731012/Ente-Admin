<?php
require_once "headers.php";

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'httponly' => true,
        'secure'   => false,
        'samesite' => 'Strict'
    ]);

    session_start();
}

function checkAdminSession() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] != true) {
        header("Location: login.php");
        exit;
    }

    $timeout = 1800;

    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $timeout) {
            $_SESSION = [];
            session_destroy();
            session_write_close();
            header("Location: login.php");
            exit;
        }
    }
    $_SESSION['last_activity'] = time();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>