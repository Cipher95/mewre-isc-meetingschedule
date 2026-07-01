<?php
require 'lang.php';
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];

$sql = "SELECT * FROM meetings ORDER BY meeting_date DESC, meeting_time ASC";
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
        .btn-back { display: inline-block; margin-top: 30px; background: #004b87; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;}
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
        <h1><?php echo t('meeting_rooms'); ?></h1>
        <p style="margin-top: 10px; font-size: 14px; color: #e5b13a;">
            <i class="fa-regular fa-clock"></i> <span class="live-clock"></span>
        </p>
    </header>

    <div class="container">
        <h2><i class="fa-solid fa-users-viewfinder" style="color: #e5b13a; margin: 0 10px;"></i> <?php echo t('meeting_rooms'); ?></h2>
         <!-- SEARCH BAR -->
        <div class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="<?php echo t('search'); ?>">
        </div>
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
                        <a href="view_meeting.php?id=<?php echo $row['id']; ?>" class="btn-join"><i class="fa-solid fa-circle-info"></i> <?php echo t('join'); ?></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p><?php echo t('no_meetings'); ?></p>
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
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('table tr');

            for (let i = 1; i < rows.length; i++) { // Skip table header (i=0)
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