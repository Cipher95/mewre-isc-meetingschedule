<?php
require 'lang.php';
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

$sql = "SELECT * FROM meetings WHERE username = '$username' ORDER BY meeting_date ASC, meeting_time ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title>Dashboard | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 0; }
        header { background: #004b87; color: white; padding: 20px; text-align: center; position: relative; }
        .lang-switch { position: absolute; top: 20px; <?php echo $lang == 'ar' ? 'left: 20px;' : 'right: 20px;'; ?> color: white; text-decoration: none; border: 1px solid white; padding: 5px 10px; border-radius: 5px;}
        .container { max-width: 1000px; margin: 40px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #333; margin-bottom: 20px;}
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 15px; border-bottom: 1px solid #ddd; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
        th { background-color: #004b87; color: white; }
        .btn-join { background: #004b87; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 14px;}
        .btn-join:hover { background: #e5b13a; color: #333; }
        .btn-logout { display: inline-block; margin-top: 30px; color: red; text-decoration: none; font-weight: bold; border: 1px solid red; padding: 8px 15px; border-radius: 5px;}
        .status-upcoming { color: #e5b13a; font-weight: bold; }
        .status-completed { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <header>
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        <h1><?php echo t('welcome'); ?> <?php echo htmlspecialchars($full_name); ?></h1>
        <p><?php echo t('username'); ?>: <?php echo htmlspecialchars($username); ?></p>
        <p style="margin-top: 10px; font-size: 14px; color: #e5b13a;">
            <i class="fa-regular fa-clock"></i> <span class="live-clock"></span>
        </p>
    </header>

    <div class="container">
        <h2><i class="fa-solid fa-users-viewfinder" style="color: #e5b13a; margin: 0 10px;"></i> <?php echo t('your_meetings'); ?></h2>
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr>
                    <th><?php echo t('meeting_title'); ?></th>
                    <th><?php echo t('date'); ?></th>
                    <th><?php echo t('time'); ?></th>
                    <th><?php echo t('room'); ?></th>
                    <th><?php echo t('status'); ?></th>
                    <th><?php echo t('action'); ?></th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><i class="fa-regular fa-calendar"></i> <?php echo $row['meeting_date']; ?></td>
                    <td><i class="fa-regular fa-clock"></i> <?php echo date("h:i A", strtotime($row['meeting_time'])); ?></td>
                    <td><?php echo htmlspecialchars($row['room']); ?></td>
                    <td class="<?php echo $row['status'] == 'Upcoming' ? 'status-upcoming' : 'status-completed'; ?>">
                        <?php echo $row['status'] == 'Upcoming' ? t('upcoming') : t('completed'); ?>
                    </td>
                    <td>
                        <a href="#" class="btn-join"><i class="fa-solid fa-circle-info"></i> <?php echo t('join'); ?></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p><?php echo t('no_meetings'); ?></p>
        <?php endif; ?>
        <a href="profile.php" class="btn-pay" style="margin-top: 30px; display: inline-block; background: #004b87;"><i class="fa-solid fa-user-pen"></i> <?php echo t('my_profile'); ?></a>

        <a href="logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i> <?php echo t('logout'); ?></a>
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