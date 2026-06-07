<?php
require_once "headers.php";

session_set_cookie_params([
    'lifetime' => 0,
    'httponly' => true,
    'secure'   => false,
    'samesite' => 'Strict'
]);

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (($_SESSION["failed_attempts"] ?? 0) >= 5) {
        $wait = 300;
        if (time() - $_SESSION["last_failed"] < $wait) {
            die("Too many attempts.");
        } else {
            $_SESSION["failed_attempts"] = 0;
        }
    }
    
    $user = strtolower(trim($_POST['user'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($user == getenv("ADMIN_USER") && $password == getenv("ADMIN_PASSWORD")){
        session_regenerate_id(true);

        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;
        $_SESSION["failed_attempts"] = 0;
        
        header("Location: index.php");
        exit;
    } else {
        $_SESSION["failed_attempts"] = ($_SESSION["failed_attempts"] ?? 0) + 1;
        $_SESSION["last_failed"] = time();
    }
}
?>

<!DOCTYPE html>

<div class="title">Ente Admin Panel</div>

<form method="POST" action="login.php" class="login-cont">
    <div class="login-inner-cont">
        <label class="login-label">email</label>
        <input type="text" name="user" class="login-input" placeholder="Username" required autocomplete="off">
    </div>
    <div class="login-inner-cont">
        <label class="login-label">pw</label>
        <input type="password" name="password" class="login-input" placeholder="Pasword" required autocomplete="off">
    </div>
    <button type="submit" class="login-button">Login</button>
</form>

<link rel="stylesheet" href="style.css">