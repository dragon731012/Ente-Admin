<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = strtolower(trim($_POST['user'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if ($user == getenv("ADMIN_USER") && $password == getenv("ADMIN_PASSWORD")){
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $user;
        
        header("Location: index.php");
        exit;
    }
}
?>

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