<?php
require_once "db.php";

require_once "auth.php";
checkAdminSession();

$headers = array_change_key_case(getallheaders(), CASE_LOWER);

$json = json_decode(file_get_contents('php://input'), true);

$csrfHeader = $headers['x-csrf-token'] ?? '';
$csrfPost = $_POST['csrf_token'] ?? $json['csrf_token'] ?? '';
$csrfValid = hash_equals($_SESSION['csrf_token'] ?? '', $csrfHeader ?: $csrfPost);

if (!$csrfValid) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit;
}

$userId = $_POST["id"] ?? $json["id"] ?? null;

if (!$userId){
    if (isset($headers['x-requested-with']) || str_contains($headers['accept'] ?? '', 'application/json')) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Missing User ID"]);
        exit;
    }
    header("Location: login.php");
    exit;
}

if ($json) {
    if (isset($json["storage"])){
        if (!is_numeric($json["storage"]) || $json["storage"] < 0) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid value"]);
            exit;
        }
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

<!DOCTYPE html>

<meta name="csrf-token" content="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

<link rel="stylesheet" href="style.css">
<div class="title">Manage - <?php echo urldecode(htmlspecialchars($_POST["email"])); ?></div>
<p class="txt">User ID - <?php echo htmlspecialchars($_POST["id"]); ?></p>
<div class="cont txt">
    Max storage - 
    <input type="text" class="manage-input" id="storage" placeholder="<?php echo htmlspecialchars(getUserStorage($userId)); ?> GB"/>
    <button class="manage-button" id="save-storage" data-id="<?php echo htmlspecialchars($userId); ?>">Save</button>
</div>
<div class="cont txt">
    Expiry - 
    <input type="date" id="expiry" class="expiry-input" value="<?php echo htmlspecialchars(getUserExpiry($userId)); ?>"/>
    <button class="manage-button" id="save-expiry" data-id="<?php echo htmlspecialchars($userId); ?>">Save</button>
</div>

<div id="panel-cont">
    <button class="panel-button txt" id="dash">Dashboard</button>
    <button class="panel-button txt" id="users">Users</button>
    <button class="panel-button txt" id="otps">OTPs</button>
    <button class="panel-button txt" id="logout">Log out</button>
</div>
<script src="app.js"></script>