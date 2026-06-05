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

<form method="POST" action="login.php">
    <div class="form-group">
        <label>email</label>
        <input type="text" name="user" required autocomplete="off">
    </div>
    <div class="form-group">
        <label>pw</label>
        <input type="password" name="password" required autocomplete="off">
    </div>
    <button type="submit">login</button>
</form>