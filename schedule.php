<?php
require 'lang.php';
require 'db.php';

// 1. Check if logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 2. SERVER-SIDE PROTECTION: Block standard Users from viewing this page entirely
if ($_SESSION['role'] == 'User') {
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
        ORDER BY meeting_date DESC, meeting_time ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('schedule'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 0; }
        header { background: #e5b13a; color: #333; padding: 20px; text-align: center; position: relative; }
        .lang-switch { position: absolute; top: 20px; <?php echo $lang == 'ar' ? 'left: 20px;' : 'right: 20px;'; ?> color: #333; text-decoration: none; border: 1px solid #333; padding: 5px 10px; border-radius: 5px; font-weight: bold;}
        .lang-switch:hover { background: #333; color: #e5b13a; }
        .container { max-width: 1200px; margin: 40px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        /* Flexbox to put Title on the left and Buttons on the right */
        .header-flex { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #f0f0f0; padding-bottom: 15px;}
        h2 { color: #004b87; margin: 0;}
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
        th { background-color: #004b87; color: white; }
        .btn-back { display: inline-block; margin-top: 30px; background: #004b87; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;}
        .badge { background: #333; color: white; padding: 3px 10px; border-radius: 12px; font-size: 13px; font-weight: bold; }
        
        /* Action Buttons CSS */
        .btn-add { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.2s;}
        .btn-add:hover { background: #218838; transform: translateY(-2px); }
        
        .btn-manage { background: #333; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; margin-right: 10px; margin-left: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: 0.2s;}
        .btn-manage:hover { background: #000; transform: translateY(-2px); }

        .btn-edit { background: #ffc107; color: #333; padding: 6px 12px; text-decoration: none; border-radius: 5px; font-size: 14px; margin: 0 2px;}
        .btn-delete { background: #dc3545; color: white; padding: 6px 12px; text-decoration: none; border-radius: 5px; font-size: 14px; margin: 0 2px;}
        .search-container { position: relative; margin-bottom: 15px; }
        .search-container i { position: absolute; top: 12px; color: #888; <?php echo $lang == 'ar' ? 'right: 15px;' : 'left: 15px;'; ?> }
        .search-input { width: 100%; padding: 10px <?php echo $lang == 'ar' ? '40px 10px 10px' : '10px 10px 40px'; ?>; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; }
        .search-input:focus { outline: none; border-color: #004b87; box-shadow: 0 0 5px rgba(0,75,135,0.3); }
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
        /* Go to Bottom Button */
        .go-to-bottom {
            position: fixed;
            bottom: 30px; 
            /* Smart positioning for English/Arabic */
            <?php echo $lang == 'ar' ? 'left: 30px;' : 'right: 30px;'; ?>
            background-color: #004b87; /* Primary Blue */
            color: #ffffff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 9998;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
            visibility: visible;
        }
        
        .go-to-bottom.hide {
            opacity: 0;
            visibility: hidden;
        }
        
        .go-to-bottom:hover {
            transform: translateY(5px); /* Animates downwards */
            background-color: #e5b13a; /* Secondary Gold */
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        <h1><i class="fa-solid fa-shield-halved"></i> <?php echo t('schedule'); ?></h1>
        <p><?php echo t('user_role'); ?> <span class="badge"><?php echo $_SESSION['role']; ?></span></p>
        <p style="margin-top: 10px; font-size: 15px; font-weight: bold; color: #004b87;">
            <i class="fa-regular fa-clock"></i> <span class="live-clock"></span>
        </p>
    </header>

    <div class="container">
        <!-- New Flex Header with Add Button -->
        <div class="header-flex">
            <h2><?php echo t('schedule'); ?></h2>
            
            <div style="display: flex; gap: 10px;">
                <?php 
                // Only show "Manage Users" if the logged-in user is an Admin
                if($_SESSION['role'] == 'Admin'): 
                ?>
                    <a href="manage_users.php" class="btn-manage"><i class="fa-solid fa-users-gear"></i> <?php echo t('manage_users'); ?></a>
                <?php endif; ?>
                
                <!-- Both Admins and Moderators will see this Add button -->
                <a href="add_meeting.php" class="btn-add"><i class="fa-solid fa-plus"></i> <?php echo t('add_meeting'); ?></a>
            </div>
        </div>
        <!-- SEARCH BAR -->
        <div class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="<?php echo t('search'); ?>">
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
                    <th><?php echo t('action'); ?></th>
                </tr>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><i class="fa-solid fa-user" style="color: #666; margin: 0 5px;"></i> <?php echo htmlspecialchars($row['full_name']); ?></td>
                    <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                    <td><?php echo $row['meeting_date']; ?></td>
                    <td>
                        <?php 
                        echo date("h:i A", strtotime($row['meeting_time'])) . ' - ' . date("h:i A", strtotime($row['end_time'])); 
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['room']); ?></td>
                    <td>
                        <?php 
                        if ($row['status'] == 'not_started') {
                            echo '<span style="color:#6c757d; font-weight:bold;"><i class="fa-solid fa-circle-pause"></i> ' . t('not_started') . '</span>';
                        } 
                        elseif ($row['status'] == 'pending') {
                            echo '<span style="color:#e5b13a; font-weight:bold;"><i class="fa-solid fa-hourglass-half"></i> ' . t('pending') . '</span>';
                        } 
                        elseif ($row['status'] == 'in_progress') {
                            echo '<span style="color:#004b87; font-weight:bold;"><i class="fa-solid fa-spinner"></i> ' . t('in_progress') . '</span>';
                        } 
                        elseif ($row['status'] == 'completed' || $row['status'] == 'Completed') {
                            echo '<span style="color:#28a745; font-weight:bold;"><i class="fa-solid fa-check-double"></i> ' . t('completed') . '</span>';
                        } 
                        else {
                            // Fallback for cancelled or old statuses
                            echo '<span style="color:red; font-weight:bold;">' . htmlspecialchars($row['status']) . '</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="edit_meeting.php?id=<?php echo $row['id']; ?>" class="btn-edit" title="<?php echo t('edit'); ?>"><i class="fa-solid fa-pen"></i></a>
                        <a href="delete_meeting.php?id=<?php echo $row['id']; ?>" class="btn-delete" title="<?php echo t('delete'); ?>" onclick="return confirm('<?php echo t('confirm_delete'); ?>');"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p style="text-align: center; font-size: 18px; color: #666; margin: 40px 0;"><i class="fa-regular fa-calendar-xmark" style="font-size: 40px; margin-bottom: 10px; display: block; color: #ccc;"></i> <?php echo t('no_meetings'); ?></p>
        <?php endif; ?>

        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
    </div>
<!-- Back to Top Button -->
    <button id="backToTop" class="back-to-top" title="Go to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    <!-- Go to Bottom Button -->
    <button id="goToBottom" class="go-to-bottom" title="<?php echo $lang == 'ar' ? 'النزول للأسفل' : 'Go to bottom'; ?>">
        <i class="fa-solid fa-arrow-down"></i>
    </button>

    <!-- Live Clock Script -->
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
        // Table Search/Filter Logic
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('table tr');

            for (let i = 1; i < rows.length; i++) {
                let match = false;
                let tds = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < tds.length; j++) {
                    if (tds[j].innerText.toLowerCase().includes(filter)) {
                        match = true;
                        break;
                    }
                }
                rows[i].style.display = match ? '' : 'none';
            }
        });
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
     <script>
        // Go to Bottom Logic
        const goToBottomBtn = document.getElementById("goToBottom");
        
        function checkScrollPosition() {
            // 1. If the page is too short to scroll, hide it
            if (document.body.scrollHeight <= window.innerHeight) {
                goToBottomBtn.classList.add("hide");
            } 
            // 2. If the user has scrolled to the absolute bottom, hide it
            else if ((window.innerHeight + window.scrollY) >= document.body.scrollHeight - 50) {
                goToBottomBtn.classList.add("hide");
            } 
            // 3. Otherwise, show it
            else {
                goToBottomBtn.classList.remove("hide");
            }
        }

        // Run checks on scroll, on page load, and if screen size changes
        window.addEventListener("scroll", checkScrollPosition);
        window.addEventListener("resize", checkScrollPosition);
        document.addEventListener("DOMContentLoaded", checkScrollPosition);
        
        // Smooth scroll to bottom when clicked
        goToBottomBtn.addEventListener("click", () => {
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: "smooth"
            });
        });
    </script>
</body>
</html>