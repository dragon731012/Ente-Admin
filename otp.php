<?php
require_once "auth.php";
checkAdminSession();

function getOTPs() {
    $otpfile = '/var/www/otps.txt';
    $day = 24 * 60 * 60;
    $now = time();
    
    if (file_exists($otpfile)) {
        $raw = file_get_contents($otpfile);
        $codes = json_decode($raw, true);
        
        if (is_array($codes) && !empty($codes)) {
            $html = "";
            $valid = false;

            usort($codes, function($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });

            foreach ($codes as $item) {
                if (($now - $item['timestamp']) > $day) {
                    continue;
                }
                
                $valid = true;
                $email = htmlspecialchars($item['email']);
                $code = htmlspecialchars($item['code']);
                $timestring = date("H:i:s", $item['timestamp']);

                $html .= "
                    <div class='code-cont txt'>
                        $email - $code
                        <div class='code-time txt'>
                            $timestring
                        </div>
                    </div>
                ";
            }

            if ($valid) {
                return $html;
            }
        }
    }
    return "<div class='txt'>None found.</div>";
}

echo "<div style='padding-top: 5vh;'>".getOTPs()."</div>";
?>

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