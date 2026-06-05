<?php
function getConnection(){
    $host = getenv("DB_HOST");
    $db = getenv("DB_NAME");
    $user = getenv("DB_USER");
    $password = getenv("DB_PASSWORD");

    return new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $password, []);
}

function updateUserStorage($id, $gb) {
    try {
        $db = getConnection();
        
        $bytes = $gb * 1024 * 1024 * 1024;

        $d = $db->prepare("UPDATE subscriptions SET storage = :storage WHERE user_id = :user_id");
        $d->execute([
            ':storage' => $bytes,
            ':user_id' => $id
        ]);

        return true;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}

function updateUserExpiry($id, $t) {
    try {
        $db = getConnection();
        
        $d = $db->prepare("UPDATE subscriptions SET expiry_time = :expiry_time WHERE user_id = :user_id");
        $d->execute([
            ':expiry_time' => $t,
            ':user_id' => $id
        ]);

        return true;
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}
?>