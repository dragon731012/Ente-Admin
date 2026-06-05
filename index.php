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

<?php foreach ($users as $user):?>

<div class="title">User Management</div>

<div class="user-cont">
    <p class="user-identifier txt">
        <?php if (htmlspecialchars($user["admin"])==1) echo "Admin" ?>
    </p>
    <p class="user-email txt">
        <?php echo htmlspecialchars($user["email"]); ?>
    </p>
    <img class="user-edit" src="edit.png"/>
    <p class="user-id txt">
        <?php echo htmlspecialchars($user["id"]); ?>
    </p>
</div>
<button id="logout" onclick="window.location='logout.php';">Log out</button>

<?php endforeach; ?>

<link rel="stylesheet" href="style.css">