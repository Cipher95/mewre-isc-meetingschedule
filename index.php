<?php 
require 'lang.php'; 
require 'db.php'; // Required to fetch the notification

// Fetch the most recent Upcoming meeting to show as a notification
$notification_meeting = null;
if (isset($_SESSION['username'])) {
    $uname = $conn->real_escape_string($_SESSION['username']);
    $notif_sql = "SELECT * FROM meetings WHERE username = '$uname' AND status = 'Upcoming' ORDER BY id DESC LIMIT 1";
    $notif_res = $conn->query($notif_sql);
    if ($notif_res && $notif_res->num_rows > 0) {
        $notification_meeting = $notif_res->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:title" content="MEW ISC | Meeting Schedule" />
    <meta property="og:description" content="Official Information Systems Center portal for the Ministry of Electricity, Water & Renewable Energy, Kuwait. Manage meeting schedule, and system reports." />
    <meta property="og:url" content="https://mewre-isc-meetingschedule.infinityfreeapp.com/" />
    <meta property="og:type" content="website" />
    <meta property="og:image" content="https://mewre-isc-meetingschedule.infinityfreeapp.com/Emblem_of_the_State_of_Kuwait.svg" />
    
    <!-- Mobile Browser Theme Color (Official MEW Blue) -->
    <meta name="theme-color" content="#004b87" />
    <title><?php echo t('title'); ?></title>
    <!-- MEW Favicon -->
    <link rel="icon" type="image/png" href="Emblem_of_the_State_of_Kuwait.svg">
    <!-- Google Fonts (English & Arabic) -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #004b87;
            --secondary-gold: #e5b13a;
            --light-bg: #f8f9fa;
            --text-dark: #333333;
            --white: #ffffff;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background-color: var(--light-bg); color: var(--text-dark); line-height: 1.6; }
        
        /* Top Time Bar */
        .top-bar { background-color: #003b6a; color: var(--white); text-align: center; padding: 8px 20px; font-size: 14px; font-weight: 600; }
        
        /* Navigation */
        nav { background-color: var(--white); box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .nav-container { max-width: 1200px; margin: auto; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; position: relative; }
        .logo { font-size: 24px; font-weight: 700; color: var(--primary-blue); text-decoration: none; display: flex; align-items: center; gap: 8px; }
        
        /* Navigation Links */
        .nav-links { list-style: none; display: flex; gap: 20px; align-items: center; padding: 0; }
        .nav-links a { text-decoration: none; color: var(--text-dark); font-weight: 600; transition: 0.3s; }
        .nav-links a:hover { color: var(--secondary-gold); }
        .btn-login { background-color: var(--primary-blue); color: var(--white) !important; padding: 8px 20px; border-radius: 5px; }
        .lang-btn { color: var(--primary-blue) !important; display: flex; align-items: center; gap: 5px; }
        
        /* Hamburger Button (Hidden on Desktop) */
        .hamburger { display: none; font-size: 28px; color: var(--primary-blue); cursor: pointer; border: none; background: transparent; }

        /* Hero Section */
        .hero { background: linear-gradient(rgba(0, 75, 135, 0.85), rgba(0, 75, 135, 0.85)), url('https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover; color: var(--white); text-align: center; padding: 100px 20px; }
        .hero h1 { font-size: 48px; margin-bottom: 20px; }
        .hero p { font-size: 18px; margin-bottom: 30px; max-width: 600px; margin-left: auto; margin-right: auto; }
        .hero-btns .btn { display: inline-block; padding: 12px 30px; margin: 5px; border-radius: 5px; text-decoration: none; font-weight: 600; transition: transform 0.3s ease; }
        .btn-primary { background-color: var(--secondary-gold); color: var(--text-dark); }
        .btn-outline { border: 2px solid var(--white); color: var(--white); }
        
        /* Services Section */
        .services { max-width: 1200px; margin: 60px auto; padding: 0 20px; text-align: center; }
        .services h2 { font-size: 32px; color: var(--primary-blue); margin-bottom: 40px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; }
        .card { background: var(--white); padding: 40px 20px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); cursor: pointer; transition: 0.3s; }
        .card:hover { transform: translateY(-10px); box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .card i { font-size: 40px; color: var(--secondary-gold); margin-bottom: 20px; }
        .card h3 { margin-bottom: 15px; color: var(--primary-blue); }
        
        /* Emergency Bar */
        .emergency { background-color: #343a40; color: white; text-align: center; padding: 20px; font-size: 18px; font-weight: 600; }
        footer { background-color: var(--primary-blue); color: var(--white); text-align: center; padding: 30px 20px; margin-top: 50px; }
        
        /* Mobile Responsive Styles */
        @media(max-width: 768px) { 
            .hero h1 { font-size: 32px; } 
            
            /* Show Hamburger */
            .hamburger { display: block; }
            
            /* Hide Nav Links initially and format for mobile dropdown */
            .nav-links {
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background-color: var(--white);
                flex-direction: column;
                align-items: center;
                padding: 0;
                box-shadow: 0 10px 15px rgba(0,0,0,0.1);
                /* Modern Dropdown Animation */
                clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
                transition: clip-path 0.4s ease-in-out, padding 0.4s ease-in-out;
            }
            
            /* Class activated by JavaScript */
            .nav-links.nav-active {
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0% 100%);
                padding: 20px 0;
            }

            .nav-links li { margin: 15px 0; width: 100%; text-align: center; }
            .btn-login { display: inline-block; width: 80%; }
            .lang-btn { justify-content: center; }
        }
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
        /* Toast Notification Styles */
        .notification-toast {
            position: fixed;
            top: 90px;
            <?php echo $lang == 'ar' ? 'left: 20px; border-right: 5px solid #28a745;' : 'right: 20px; border-left: 5px solid #28a745;'; ?>
            background: var(--white);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 9999;
            width: 320px;
            cursor: pointer;
            transition: transform 0.2s;
            animation: slideIn 0.5s ease-out forwards;
        }
        .notification-toast:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .notification-toast h4 { color: #28a745; margin-bottom: 8px; font-size: 14px; line-height: 1.4; }
        .notification-toast p { font-size: 13px; color: #555; margin-bottom: 5px; }
        .close-notif { position: absolute; top: 12px; <?php echo $lang == 'ar' ? 'left: 12px;' : 'right: 12px;'; ?> color: #aaa; font-size: 16px; transition: 0.2s; }
        .close-notif:hover { color: #333; }
        
        @keyframes slideIn {
            from { transform: translateX(<?php echo $lang == 'ar' ? '-120%' : '120%'; ?>); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Top Time Bar -->
    <div class="top-bar">
        <i class="fa-regular fa-clock" style="margin: 0 5px; color: var(--secondary-gold);"></i> 
        <span class="live-clock"></span>
    </div>

    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <a href="index.php" class="logo">
    <i class="fa-solid fa-server"></i> <?php echo t('mew_kuwait'); ?>
			</a>
            
            <!-- Hamburger Icon -->
            <button class="hamburger" id="hamburger-btn">
                <i class="fa-solid fa-bars"></i>
            </button>

            <ul class="nav-links" id="nav-menu">
                <li><a href="profile.php"><?php echo t('my_profile'); ?></a></li>
                
                <?php 
                // Admin/Moderator only link
                if (isset($_SESSION['role']) && ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Moderator')): 
                ?>
                    <li><a href="schedule.php" style="color: #e5b13a;"><i class="fa-solid fa-lock-open"></i> <?php echo t('schedule'); ?></a></li>
                <?php endif; ?>

                <li>
                    <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-btn">
                        <i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?>
                    </a>
                </li>
                
                <?php if (isset($_SESSION['username'])): ?>
                    <!-- Shows Dashboard Link -->
                    <li><a href="dashboard.php" style="font-weight: 600; color: var(--primary-blue);"><i class="fa-solid fa-calendar-check"></i> <?php echo t('view_schedule'); ?></a></li>
                    <!-- Shows Red Logout Button -->
                    <li><a href="logout.php" class="btn-login" style="background-color: #dc3545;"><i class="fa-solid fa-right-from-bracket"></i> <?php echo t('logout'); ?></a></li>
                <?php else: ?>
                    <!-- Shows Login Button for Guests -->
                    <li><a href="login.php" class="btn-login"><i class="fa-solid fa-right-to-bracket"></i> <?php echo t('login'); ?></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    

    <!-- Hero Section -->
    <header class="hero">
        
        <!-- NEW WELCOME MESSAGE ADDED HERE -->
        <?php if (isset($_SESSION['username'])): ?>
            <h2 style="color: var(--secondary-gold); margin-bottom: 15px; font-weight: 600;">
                <?php echo t('welcome'); ?> <?php echo htmlspecialchars($_SESSION['full_name']); ?>!
            </h2>
        <?php endif; ?>
        <!-- END OF WELCOME MESSAGE -->

        <h1><?php echo t('hero_title'); ?></h1>
        <p><?php echo t('hero_desc'); ?></p>
        
    </header>

    <!-- Services Grid -->
    <section id="services" class="services">
        <h2><?php echo t('quick_services'); ?></h2>
        <div class="grid">
            <a href="meeting_rooms.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <i class="fa-solid fa-people-roof"></i>
                    <h3><?php echo t('meeting_rooms'); ?></h3>
                    <p><?php echo t('meeting_rooms_desc'); ?></p>
                </div>
            </a>
            
            <a href="calendar.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <i class="fa-solid fa-calendar-days"></i>
                    <h3><?php echo t('calendar'); ?></h3>
                    <p><?php echo t('calendar_desc'); ?></p>
                </div>
            </a>
            
            <a href="reports.php" style="text-decoration: none; color: inherit;">
                <div class="card">
                    <i class="fa-solid fa-chart-line"></i>
                    <h3><?php echo t('reports'); ?></h3>
                    <p><?php echo t('reports_desc'); ?></p>
                </div>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <p><?php echo t('footer'); ?></p>
        <!-- Back to Top Button -->
    </footer>
	<button id="backToTop" class="back-to-top" title="Go to top">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
    <!-- JavaScript for Live Clock & Hamburger Menu -->
    <script>
        // Hamburger Menu Logic
        const hamburgerBtn = document.getElementById('hamburger-btn');
        const navMenu = document.getElementById('nav-menu');
        const icon = hamburgerBtn.querySelector('i');

        hamburgerBtn.addEventListener('click', () => {
            navMenu.classList.toggle('nav-active');
            
            // Switch icon between hamburger (bars) and close (xmark)
            if(navMenu.classList.contains('nav-active')){
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            } else {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        });

        // Close menu if a user clicks on an anchor link (like #services)
        document.querySelectorAll('.nav-links a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('nav-active');
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            });
        });

        // Live Kuwait Clock Logic
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
    <!-- Meeting Notification Pop-up -->
    <?php if ($notification_meeting): ?>
        <div class="notification-toast" id="meetingNotification" onclick="window.location.href='view_meeting.php?id=<?php echo $notification_meeting['id']; ?>'">
            <!-- Close Button (stops the click from triggering the link) -->
            <i class="fa-solid fa-xmark close-notif" onclick="event.stopPropagation(); document.getElementById('meetingNotification').style.display='none';"></i>
            
            <h4><i class="fa-solid fa-circle-check" style="margin: 0 5px;"></i> <?php echo t('meeting_accepted'); ?></h4>
            
            <!-- Meeting Details -->
            <p style="color: var(--primary-blue); font-weight: bold;">
                <?php echo htmlspecialchars($notification_meeting['title']); ?>
            </p>
            <p>
                <i class="fa-regular fa-calendar"></i> <?php echo $notification_meeting['meeting_date']; ?> &nbsp;|&nbsp; 
                <i class="fa-regular fa-clock"></i> <?php echo date("h:i A", strtotime($notification_meeting['meeting_time'])); ?>
            </p>
            <p style="margin-top: 8px; font-size: 12px; font-weight: bold; color: #666; text-decoration: underline;">
                <?php echo $lang == 'ar' ? 'انقر لعرض التفاصيل' : 'Click to view details'; ?> &rarr;
            </p>
        </div>
    <?php endif; ?>
</body>
</html>