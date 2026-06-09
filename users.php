<?php
require_once "db.php";

require_once "auth.php";
checkAdminSession();

$users = [];

try {
    $db = getConnection();
    $key = getenv("ENTE_ENCRYPTION_KEY");
    $admins = array_map("trim", explode(",", getenv("ADMINS")));

    if (!$key){
        echo "Key not found!";
    }

    $keye = base64_decode($key);

    $d = $db->query("SELECT user_id, encrypted_email, email_decryption_nonce FROM users WHERE encrypted_email IS NOT NULL");
    
    while ($r = $d->fetch(PDO::FETCH_ASSOC)) {
        $e = stream_get_contents($r["encrypted_email"]);
        $n = stream_get_contents($r["email_decryption_nonce"]);

        $decrypted = @sodium_crypto_secretbox_open($e, $n, $keye);

        $users[] = [
            "id" => $r["user_id"],
            "email" => $decrypted,
            "admin" => (in_array($r["user_id"],$admins))
        ];
    }



} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>

<div class="title">User Management</div>

<?php foreach ($users as $user):?>

<div class="user-cont">
    <p class="user-identifier txt">
        <?php if (htmlspecialchars($user["admin"])==1) echo "Admin" ?>
    </p>
    <p class="user-email txt">
        <?php echo htmlspecialchars($user["email"]); ?> - <?php echo htmlspecialchars(getUserUsage($user['id'])); ?> GB / <?php echo htmlspecialchars(getUserStorage($user['id'])); ?> GB
    </p>
    <img class="user-edit" data-id="<?php echo urlencode($user['id']); ?>" data-email="<?php echo urlencode($user['email']); ?>" src="edit.png"/>
</div>

<?php endforeach; ?>

<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

<link rel="stylesheet" href="style.css">

<div id="panel-cont">
    <button class="panel-button txt" id="dash">Dashboard</button>
    <button class="panel-button txt" id="users">Users</button>
    <button class="panel-button txt" id="otps">OTPs</button>
    <button class="panel-button txt" id="logout">Log out</button>
</div>
<script src="app.js"></script>