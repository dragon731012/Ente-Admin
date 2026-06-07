<?php
require_once "auth.php";
checkAdminSession();
?>

<div class="title">Dashboard</div>
<link rel="stylesheet" href="style.css">
<script src="app.js"></script>

<div id="panel-cont">
    <button class="panel-button txt" id="dash">Dashboard</button>
    <button class="panel-button txt" id="users">Users</button>
    <button class="panel-button txt" id="otps">OTPs</button>
    <button class="panel-button txt" id="logout">Log out</button>
</div>
<script src="app.js"></script>