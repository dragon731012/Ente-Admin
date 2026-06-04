<?php
require_once "db.php";

try {
    $db = getConnection();
    $key = getenv('ENTE_ENCRYPTION_KEY');

    if (!$key){
        echo "Key not found!";
    }

    $r = $db->fetch();
    var_dump($r["encrypted_email"]);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>