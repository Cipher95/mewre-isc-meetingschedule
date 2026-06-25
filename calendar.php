<?php
require 'lang.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('calendar'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; padding: 40px 20px; margin: 0; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #004b87; margin-bottom: 30px; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 15px;}
        .event-item { display: flex; gap: 20px; margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px dashed #ccc; }
        .event-date { background: #e5b13a; color: #333; min-width: 80px; text-align: center; padding: 15px; border-radius: 8px; font-weight: bold; }
        .event-date span { display: block; font-size: 24px; }
        .event-details h3 { margin: 0 0 5px 0; color: #004b87; }
        .event-details p { margin: 0; color: #666; }
        .btn-back { display: inline-block; margin-top: 10px; background: #004b87; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-calendar-days"></i> <?php echo t('events'); ?></h2>
        
        <div class="event-item">
            <div class="event-date"><span>28</span> June</div>
            <div class="event-details">
                <h3><?php echo t('maintenance'); ?>: Firewall Upgrade</h3>
                <p><i class="fa-regular fa-clock"></i> 02:00 AM - 04:00 AM (Downtime Expected)</p>
            </div>
        </div>

        <div class="event-item">
            <div class="event-date"><span>02</span> July</div>
            <div class="event-details">
                <h3><?php echo t('deployment'); ?>: MEW Portal v2.0</h3>
                <p><i class="fa-regular fa-clock"></i> 11:00 PM</p>
            </div>
        </div>

        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
    </div>
</body>
</html>