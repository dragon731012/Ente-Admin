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

<div class="title">User Management</div>

<?php foreach ($users as $user):?>

<div class="user-cont">
    <p class="user-identifier txt">
        <?php if (htmlspecialchars($user["admin"])==1) echo "Admin" ?>
    </p>
    <p class="user-email txt">
        <?php echo htmlspecialchars($user["email"]); ?>
    </p>
    <img class="user-edit" src="edit.png" onclick="submitPost('manage.php',{id: '<?php echo urlencode($user['id']); ?>', email: '<?php echo urlencode($user['email']); ?>'});"/>
</div>
<script>
    function submitPost(url, data) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = url;
        for (const key in data) {
            if (data.hasOwnProperty(key)) {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = key;
                inp.value = data[key];
                f.appendChild(inp);
            }
        }
        document.body.appendChild(f);
        f.submit();
    }
</script>

<?php endforeach; ?>

<link rel="stylesheet" href="style.css">

<div id="panel-cont">
    <button class="panel-button txt" id="users" onclick="window.location='index.php';">Users</button>
    <button class="panel-button txt" id="otps" onclick="window.location='otp.php';">OTPs</button>
    <button class="panel-button txt" id="logout" onclick="window.location='logout.php';">Log out</button>
</div>
<script>
    if (window.location.href.includes("otp")){
        document.getElementById("otps").className="panel-button txt selected";
    } else {
        document.getElementById("users").className="panel-button txt selected";
    }
</script>