<?php
require_once "db.php";

require_once "auth.php";
checkAdminSession();

$json = json_decode(file_get_contents('php://input'), true);

$userId = $_POST["id"] ?? $json["id"] ?? null;

if (!$userId){
    header("Location: login.php");
    exit;
}

if ($json) {
    if (isset($json["storage"])){
        updateUserStorage($userId, $json["storage"]);
        header('Content-Type: application/json');
        echo json_encode(["status" => "success", "message" => "Storage updated"]);
        exit;
    }
    if (isset($json["expiry"])){
        updateUserExpiry($userId, $json["expiry"]);
        header('Content-Type: application/json');
        echo json_encode(["status" => "success", "message" => "Expiry updated"]);
        exit;
    }
}
?>
<link rel="stylesheet" href="style.css">
<div class="title">Manage - <?php echo urldecode(htmlspecialchars($_POST["email"])); ?></div>
<p class="txt">User ID - <?php echo $_POST["id"]; ?></p>
<div class="cont txt">
    Max storage - 
    <input type="text" class="manage-input" id="storage" placeholder="<?php echo htmlspecialchars(getUserStorage($userId)); ?> GB"/>
    <button class="manage-button" onclick="sendPost('manage.php', {id: '<?php echo htmlspecialchars($userId); ?>', storage: document.getElementById('storage').value})">Save</button>
</div>
<div class="cont txt">
    Expiry - 
    <input type="date" id="expiry" class="expiry-input" value="<?php echo htmlspecialchars(getUserExpiry($userId)); ?>"/>
    <button class="manage-button" onclick="sendPost('manage.php', {id: '<?php echo htmlspecialchars($userId); ?>', expiry: document.getElementById('expiry').value})">Save</button>
<script>
async function sendPost(url, data){
    try {
        const response = await fetch(url, { 
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.status === "success") {
            alert(result.message);
            if (data.storage) {
                document.getElementById('storage').placeholder = data.storage + " GB";
                document.getElementById('storage').value = "";
            }
        }
    } catch (error) {
        console.error("Error updating user data:", error);
    }
}
</script>

<div id="panel-cont">
    <button class="panel-button txt" id="users" onclick="window.location='index.php';">Users</button>
    <button class="panel-button txt" id="otps" onclick="window.location='otp.php';">OTPs</button>
    <button class="panel-button txt" id="logout" onclick="window.location='logout.php';">Log out</button>
</div>
<script>
    if (window.location.pathname.includes("otp")){
        document.getElementById("otps").className="panel-button txt selected";
    } else if (!window.location.pathname.includes("manage")) {
        document.getElementById("users").className="panel-button txt selected";
    }
</script>