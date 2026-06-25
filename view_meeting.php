<?php
require 'lang.php';
require 'db.php';

// Check if logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = intval($_GET['id']);
$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Fetch the meeting. 
// SECURITY: If the user is NOT an Admin/Moderator, ensure they can ONLY see their own meetings
if ($role == 'User') {
    $sql = "SELECT * FROM meetings WHERE id = $id AND username = '$username'";
} else {
    $sql = "SELECT * FROM meetings WHERE id = $id"; // Admins/Mods can view any details
}

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("<h2 style='color:red; text-align:center; margin-top:50px; font-family: sans-serif;'>" . t('unauthorized') . "</h2>");
}

$meeting = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('meeting_details'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        
        /* The Card Design */
        .ticket-card { background: white; max-width: 500px; width: 100%; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; position: relative; }
        .ticket-header { background: #004b87; color: white; padding: 30px 20px; text-align: center; }
        .ticket-header h2 { margin: 0; font-size: 24px; }
        .ticket-header p { margin: 5px 0 0; opacity: 0.8; font-size: 14px; }
        
        .ticket-body { padding: 30px; }
        .info-group { margin-bottom: 20px; border-bottom: 1px dashed #eee; padding-bottom: 10px; }
        .info-group:last-child { border-bottom: none; margin-bottom: 0; }
        .info-label { color: #666; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: bold; margin-bottom: 5px;}
        .info-value { color: #333; font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; color: white; margin-top: 10px;}
        .status-Upcoming { background: #e5b13a; color: #333; }
        .status-Completed { background: #28a745; }
        .status-Cancelled { background: #dc3545; }

        .ticket-footer { background: #f1f1f1; padding: 20px; display: flex; justify-content: space-between; align-items: center; }
        
        .btn { padding: 10px 20px; border-radius: 5px; text-decoration: none; font-weight: bold; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px;}
        .btn-print { background: #333; color: white; }
        .btn-print:hover { background: #000; }
        .btn-back { background: #004b87; color: white; }
        .btn-back:hover { background: #e5b13a; color: #333; }
        /* Back to Top Button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            /* Smart positioning based on language direction */
            <?php echo $lang == 'ar' ? 'left: 30px;' : 'right: 30px;'; ?>
            background-color: #e5b13a; /* Secondary Gold */
            color: #333;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            opacity: 0; /* Hidden by default */
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .back-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        
        .back-to-top:hover {
            transform: translateY(-5px);
            background-color: #004b87; /* Primary Blue */
            color: #ffffff;
        }

        /* Print CSS - Hides buttons when physically printing */
        @media print {
            body { background: white; }
            .ticket-card { box-shadow: none; border: 1px solid #ccc; }
            .ticket-footer { display: none; }
        }
    </style>
</head>
<body>

    <div class="ticket-card">
        <div class="ticket-header">
            <i class="fa-solid fa-qrcode" style="font-size: 40px; margin-bottom: 10px; color: #e5b13a;"></i>
            <h2><?php echo t('meeting_details'); ?></h2>
            <p><?php echo t('meeting_id'); ?>: #MEW-<?php echo str_pad($meeting['id'], 5, "0", STR_PAD_LEFT); ?></p>
        </div>
        
        <div class="ticket-body">
            <div class="info-group">
                <div class="info-label"><?php echo t('meeting_title'); ?></div>
                <div class="info-value"><i class="fa-solid fa-briefcase" style="color: #004b87;"></i> <?php echo htmlspecialchars($meeting['title']); ?></div>
            </div>

            <div style="display: flex; justify-content: space-between; flex-wrap: wrap;">
                <div class="info-group" style="width: 48%;">
                    <div class="info-label"><?php echo t('date'); ?></div>
                    <div class="info-value"><i class="fa-regular fa-calendar" style="color: #004b87;"></i> <?php echo $meeting['meeting_date']; ?></div>
                </div>
                
                <div class="info-group" style="width: 48%;">
                    <div class="info-label"><?php echo t('time'); ?></div>
                    <div class="info-value"><i class="fa-regular fa-clock" style="color: #004b87;"></i> <?php echo date("h:i A", strtotime($meeting['meeting_time'])); ?></div>
                </div>
            </div>

            <div class="info-group">
                <div class="info-label"><?php echo t('room'); ?></div>
                <div class="info-value"><i class="fa-solid fa-door-open" style="color: #004b87;"></i> <?php echo htmlspecialchars($meeting['room']); ?></div>
            </div>

            <div class="info-group">
                <div class="info-label"><?php echo t('status'); ?></div>
                <div class="status-badge status-<?php echo $meeting['status']; ?>">
                    <?php 
                        if($meeting['status'] == 'Upcoming') echo t('upcoming');
                        elseif($meeting['status'] == 'Completed') echo t('completed');
                        else echo t('cancelled');
                    ?>
                </div>
            </div>
        </div>

        <div class="ticket-footer">
            <a href="dashboard.php" class="btn btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
            <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> <?php echo t('print'); ?></button>
        </div>
    </div>
    <!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" title="Go to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    <script>
        // Back to Top Logic
        const backToTopBtn = document.getElementById("backToTop");
        
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add("show");
            } else {
                backToTopBtn.classList.remove("show");
            }
        });
        
        backToTopBtn.addEventListener("click", () => {
            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        });
        </script>

</body>
</html>