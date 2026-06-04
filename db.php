<?php
function getConnection(){
    $host = getenv("DB_HOST");
    $db = getenv("DB_NAME");
    $user = getenv("DB_USER");
    $password = getenv("DB_PASSWORD");

    return new PDO("pgsql:host=$host;port=5432;dbname=$db", $user, $password, []);
}
?>