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
    <title><?php echo t('reports'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; padding: 40px 20px; margin: 0; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #004b87; margin-bottom: 30px; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 15px;}
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #004b87; color: white; padding: 25px; border-radius: 8px; text-align: center; }
        .stat-card h3 { margin: 0; font-size: 36px; color: #e5b13a; }
        .stat-card p { margin: 10px 0 0 0; font-weight: bold; }
        
        .report-section { margin-bottom: 20px; padding: 20px; background: #f4f6f9; border-radius: 8px; border-left: 5px solid #28a745;}
        .report-section h4 { margin: 0 0 10px 0; color: #333; }
        .report-section p { margin: 0; color: #666; }
        .btn-back { display: inline-block; margin-top: 20px; background: #004b87; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-chart-line"></i> <?php echo t('reports'); ?></h2>
        
        <!-- Live Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>99.98%</h3>
                <p><i class="fa-solid fa-wifi"></i> <?php echo t('uptime'); ?></p>
            </div>
            <div class="stat-card">
                <h3><i class="fa-solid fa-server"></i></h3>
                <p style="color:#28a745;"><?php echo t('server_status'); ?>: <?php echo t('operational'); ?></p>
            </div>
        </div>

        <!-- Deliverables Log -->
        <h3><i class="fa-solid fa-folder-open" style="color: #e5b13a;"></i> <?php echo t('deliverables'); ?></h3>
        
        <div class="report-section">
            <h4>MEW Mobile App Integration</h4>
            <p>Phase 2 completed successfully. API endpoints for user authentication have been finalized and pushed to production.</p>
        </div>

        <div class="report-section" style="border-left-color: #ffc107;">
            <h4>Internal Database Migration</h4>
            <p>Data syncing is currently at 75%. Expected to complete by end of week. No data loss reported.</p>
        </div>

        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
    </div>
</body>
</html>