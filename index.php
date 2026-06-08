<?php
require_once "db.php";

require_once "auth.php";
checkAdminSession();

function getEnteStorageMetrics() {
    try {
        $db = getConnection();
        
        $totalDisk = disk_total_space("/");
        $freeDisk = disk_free_space("/");
        $totalHostUsed = $totalDisk - $freeDisk;

        $dbName = getenv("DB_NAME") ?: "ente_db";
        $q1 = $db->prepare("SELECT pg_database_size(:db_name) AS db_size");
        $q1->execute([':db_name' => $dbName]);
        $enteDbSize = (float)($q1->fetch(PDO::FETCH_ASSOC)['db_size'] ?? 0);

        $photoSize = 0;
        try {
            $q2 = $db->query("SELECT COALESCE(SUM(storage_consumed), 0) AS global_photo_size FROM public.usage");
            $photoSize = (float)($q2->fetch(PDO::FETCH_ASSOC)['global_photo_size'] ?? 0);
        } catch (Exception $subError) {
            error_log("Usage rollup query failed: " . $subError->getMessage());
            $photoSize = 0;
        }

        $enteOverheadExcludingPhotos = max($enteDbSize - $photoSize, 0);

        $otherAppsSize = $totalHostUsed - $enteDbSize;
        if ($otherAppsSize < 0) $otherAppsSize = 0;

        $availableForPhotos = $totalDisk - $otherAppsSize - $enteOverheadExcludingPhotos;
        $gb = 1024 * 1024 * 1024;

        return [
            "photos_used" => round($photoSize / $gb, 2),
            "photos_available" => round($availableForPhotos / $gb, 2)
        ];

    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return false;
    }
}
$metrics = getEnteStorageMetrics();
?>

<div class="title">Dashboard</div>
<script src="app.js"></script>

<div id="panel-cont">
    <button class="panel-button txt" id="dash">Dashboard</button>
    <button class="panel-button txt" id="users">Users</button>
    <button class="panel-button txt" id="otps">OTPs</button>
    <button class="panel-button txt" id="logout">Log out</button>
</div>
<script src="app.js"></script>

<?php if ($metrics): ?>
<div class="small-title">
    Storage
</div>
<div id="storage-cont">
    <div class="txt">
        <?php echo $metrics['photos_used']; ?> GB / <?php echo $metrics['photos_available']; ?> GB
    </div>
    <div class="storage-bar" data-percent="<?php echo $metrics['photos_used']/$metrics['photos_available']; ?>"></div>
    <div class="txt">
        <?php echo round($metrics['photos_used']/$metrics['photos_available'])*100; ?>%
    </div>
<?php endif; ?>

<link rel="stylesheet" href="style.css">