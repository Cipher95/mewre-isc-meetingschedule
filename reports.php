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
          /* Search Bar CSS */
        .search-container { position: relative; margin-bottom: 25px; }
        .search-container i { position: absolute; top: 12px; color: #888; <?php echo $lang == 'ar' ? 'right: 15px;' : 'left: 15px;'; ?> }
        .search-input { width: 100%; padding: 10px <?php echo $lang == 'ar' ? '40px 10px 10px' : '10px 10px 40px'; ?>; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; }
        .search-input:focus { outline: none; border-color: #004b87; box-shadow: 0 0 5px rgba(0,75,135,0.3); }
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
         <!-- SEARCH BAR -->
        <div class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="<?php echo t('search'); ?>">
        </div>
        <div class="report-section">
            <h4>Official Order System MEW Website (Made by Admin)</h4>
            <p>https://cipher95.github.io/Official-Order-System-MEW/ This website is used to create orders. Once the user has finished entering the required information, they can generate and download the order as a PDF file. Note: The current PDF template is not the official template and does not match the format currently used by the department. All orders generated through this website use the same template. Although it is suitable for general use, it should not be considered an official document until the PDF template is updated and officially approved.</p>
        </div>

<div class="report-section">
            <h4>MEW Projects Implementation Department 2026 (Made by Admin)</h4>
            <p>https://cipher95.github.io/MEW-Projects-Implementation-Department-2026/ This site is intended for the MEW Projects Implementation Department only. It contains the monthly SMS report; however, the report details are for reference only and should not be considered official, as the administrator has not yet received the official report information.</p>
        </div>

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
     <!-- Filter Script -->
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let events = document.querySelectorAll('.event-item');

            events.forEach(function(event) {
                // Get all the text inside this specific event (Title, Date, Time, etc.)
                let text = event.innerText.toLowerCase();
                
                // If the text includes the search word, show it (as flex), otherwise hide it
                if (text.includes(filter)) {
                    event.style.display = 'flex';
                } else {
                    event.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>