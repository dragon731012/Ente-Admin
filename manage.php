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
<p class="txt"><?php echo $_POST["id"]; ?></p>
<div class="cont">
    <input type="text" id="storage" placeholder="<?php echo htmlspecialchars(getUserStorage($userId)); ?> GB"/>
    <button onclick="sendPost('manage.php', {id: '<?php echo htmlspecialchars($userId); ?>', storage: document.getElementById('storage').value})">Save</button>
</div>
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
            document.getElementById('storage').placeholder = data.storage + " GB";
            document.getElementById('storage').value = "";
        }
    } catch (error) {
        console.error("Error updating user data:", error);
    }
}
</script>
