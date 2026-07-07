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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- NEW: Bilingual Title -->
    <title><?php echo t('contact_us'); ?> | <?php echo t('mew_kuwait'); ?></title>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body, html { 
            margin: 0; 
            padding: 0; 
            width: 100%; 
            height: 100%; 
            overflow: hidden; 
            background-color: #f4f6f9; 
            font-family: <?php echo t('font'); ?>;
            display: flex;
            flex-direction: column; /* Stacks the map and address vertically */
        }
        .contactUs-container {
            /*flex-grow: 1;*/ /* Automatically stretches the map to fill remaining space */
            width: 100%;
            margin: 0 auto;
        }
        .contactUs-container h3 { margin: 0 0 5px 0; color: #004b87; }
        embed { 
            border: none; 
            width: 100%; 
            height: 100%; 
        }
        .address-bar {
            background: #004b87; /* MEW Primary Blue */
            color: white;
            padding: 8px 10px;
            text-align: center;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn-back { display: inline-block; margin-top: 10px; background: #004b87; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;}
        .container { max-width: 900px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #004b87; margin-bottom: 30px; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 15px;}
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
        <h2><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo t('contact_us'); ?></h2>
    <!-- Map Section -->
    <div class="contactUs-container">
        <h3><?php echo t('contactUs_para_1'); ?></h3>
        <br/>
        <h3><?php echo t('contactUs_para_2'); ?></h3>
    
        <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
    </div>
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

</body>
</html>
