<?php
require 'lang.php';
require 'db.php';

// 1. Check if logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. SERVER-SIDE PROTECTION: Block standard Users
if ($_SESSION['role'] == 'User') {
    // Stop execution and show Access Denied
    http_response_code(403);
    die('
        <!DOCTYPE html>
        <html lang="' . $lang . '" dir="' . t('dir') . '">
        <head>
            <meta charset="UTF-8">
            <title>' . t('access_denied') . '</title>
            <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&family=Poppins:wght@400;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: ' . t('font') . '; background: #f8f9fa; text-align: center; padding: 100px 20px; }
                .error-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 500px; margin: auto; border-top: 5px solid red;}
                h1 { color: red; }
                a { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #004b87; color: white; text-decoration: none; border-radius: 5px; }
            </style>
        </head>
        <body>
            <div class="error-box">
                <h1>' . t('access_denied') . '</h1>
                <p>' . t('access_denied_desc') . '</p>
                <a href="index.php">' . t('back_home') . '</a>
            </div>
        </body>
        </html>
    ');
}

// Fetch ALL meetings across the company with User data using a JOIN on username
$sql = "SELECT meetings.*, users.full_name 
        FROM meetings 
        JOIN users ON meetings.username = users.username 
        ORDER BY meeting_date ASC, meeting_time ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('all_schedules'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (Keep your previous styles here) */
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 0; }
        header { background: #e5b13a; color: #333; padding: 20px; text-align: center; position: relative; }
        .lang-switch { position: absolute; top: 20px; <?php echo $lang == 'ar' ? 'left: 20px;' : 'right: 20px;'; ?> color: #333; text-decoration: none; border: 1px solid #333; padding: 5px 10px; border-radius: 5px;}
        .container { max-width: 1200px; margin: 40px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h2 { color: #004b87; margin: 0;}
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
        th { background-color: #004b87; color: white; }
        .btn-back { display: inline-block; margin-top: 20px; background: #004b87; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px;}
        .badge { background: #333; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; }
        
        /* New Buttons CSS */
        .btn-add { background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .btn-add:hover { background: #218838; }
        .btn-edit { background: #ffc107; color: #333; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 14px; margin: 0 2px;}
        .btn-delete { background: #dc3545; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px; font-size: 14px; margin: 0 2px;}
    </style>
</head>
<body>
    <header>
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        <h1><i class="fa-solid fa-shield-halved"></i> <?php echo t('all_schedules'); ?></h1>
        <p><?php echo t('user_role'); ?> <span class="badge"><?php echo $_SESSION['role']; ?></span></p>
        <p style="margin-top: 10px; font-size: 15px; font-weight: bold; color: #004b87;">
            <i class="fa-regular fa-clock"></i> <span class="live-clock"></span>
        </p>
    </header>

   <div class="container">
        <!-- New Flex Header with Add Button -->
        <div class="header-flex">
            <h2><?php echo t('all_schedules'); ?> (Admin / Moderator)</h2>
            <a href="add_meeting.php" class="btn-add"><i class="fa-solid fa-plus"></i> <?php echo t('add_meeting'); ?></a>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th><?php echo t('employee_name'); ?></th>
                    <th><?php echo t('meeting_title'); ?></th>
                    <th><?php echo t('date'); ?></th>
                    <th><?php echo t('time'); ?></th>
                    <th><?php echo t('room'); ?></th>
                    <th><?php echo t('status'); ?></th>
                    <th><?php echo t('action'); ?></th> <!-- NEW ACTION COLUMN -->
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><i class="fa-solid fa-user" style="color: #666; margin: 0 5px;"></i> <?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo $row['meeting_date']; ?></td>
                    <td><?php echo date("h:i A", strtotime($row['meeting_time'])); ?></td>
                    <td><?php echo htmlspecialchars($row['room']); ?></td>
                    <td>
                        <?php 
                        if($row['status'] == 'Upcoming') echo '<span style="color:#e5b13a;font-weight:bold;">'.t('upcoming').'</span>';
                        elseif($row['status'] == 'Completed') echo '<span style="color:#28a745;font-weight:bold;">'.t('completed').'</span>';
                        else echo '<span style="color:red;font-weight:bold;">'.t('cancelled').'</span>';
                        ?>
                    </td>
                    <td>
                        <!-- Edit & Delete Buttons -->
                        <a href="edit_meeting.php?id=<?php echo $row['id']; ?>" class="btn-edit"><i class="fa-solid fa-pen"></i></a>
                        <a href="delete_meeting.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('<?php echo t('confirm_delete'); ?>');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p><?php echo t('no_meetings'); ?></p>
        <?php endif; ?>

        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
    </div>
    <script>
        function updateClock() {
            const now = new Date();
            const lang = document.documentElement.lang === 'ar' ? 'ar-KW' : 'en-US';
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', timeZone: 'Asia/Kuwait' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true, timeZone: 'Asia/Kuwait' };
            
            const dateStr = now.toLocaleDateString(lang, dateOptions);
            const timeStr = now.toLocaleTimeString(lang, timeOptions);
            
            document.querySelectorAll('.live-clock').forEach(el => {
                el.innerText = dateStr + ' - ' + timeStr;
            });
        }
        setInterval(updateClock, 1000);
        document.addEventListener('DOMContentLoaded', updateClock);
    </script>
</body>
</html>