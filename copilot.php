<?php
require 'lang.php';
session_start();

// Strict Access: Only Admins and Moderators
if (!isset($_SESSION['username']) || !in_array($_SESSION['role'], ['Admin', 'Moderator'])) {
    // Kick standard users back to the homepage
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('mod_agent'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; margin: 0; padding: 0; }
        body { background: #eceff1; display: flex; flex-direction: column; height: 100vh; }
        
        /* Dark Theme Header for Moderators */
        header { background: #212529; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.2); z-index: 10; border-bottom: 3px solid #dc3545;}
        .header-title { display: flex; align-items: center; gap: 10px; font-size: 18px; font-weight: bold; }
        .btn-back { color: white; text-decoration: none; background: rgba(255,255,255,0.1); padding: 5px 15px; border-radius: 5px; font-size: 14px; border: 1px solid #444;}
        .btn-back:hover { background: #dc3545; color: white; border-color: #dc3545;}

        /* Chat Container */
        .chat-container { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 15px; max-width: 900px; margin: 0 auto; width: 100%; }
        
        /* Chat Bubbles */
        .message { max-width: 80%; padding: 12px 18px; border-radius: 15px; font-size: 15px; line-height: 1.5; animation: fadeIn 0.3s ease-in-out;}
        .msg-ai { background: white; color: #333; border: 1px solid #ddd; align-self: flex-start; border-top-left-radius: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);}
        .msg-user { background: #343a40; color: white; align-self: flex-end; border-top-right-radius: 2px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);}
        
        /* Input Area */
        .input-area { background: white; padding: 15px 20px; border-top: 1px solid #ddd; display: flex; justify-content: center; }
        .input-wrapper { display: flex; gap: 10px; max-width: 900px; width: 100%; }
        input[type="text"] { flex: 1; padding: 12px 20px; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; outline: none; transition: 0.2s;}
        input[type="text"]:focus { border-color: #dc3545; }
        button { background: #dc3545; color: white; border: none; padding: 0 25px; border-radius: 5px; font-weight: bold; cursor: pointer; transition: 0.2s; font-size: 16px;}
        button:hover { background: #c82333; }
        
        .typing { color: #888; font-size: 13px; font-style: italic; align-self: flex-start; margin-left: 10px; display: none; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
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
        <div class="header-title">
            <i class="fa-solid fa-user-tie" style="font-size: 24px; color: #e5b13a;"></i>
            <?php echo t('mod_agent'); ?> <span style="font-size:12px; background:#dc3545; padding:2px 8px; border-radius:10px; margin-left:10px;">SECURE</span>
        </div>
        <div>
            <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
        </div>
    </header>

    <div class="chat-container" id="chatBox">
        <div class="message msg-ai">
            <i class="fa-solid fa-bolt" style="color: #dc3545;"></i> <?php echo t('hello_mod'); ?>
        </div>
        <div class="typing" id="typingIndicator"><?php echo t('ai_typing'); ?></div>
    </div>

    <div class="input-area">
        <form class="input-wrapper" id="chatForm">
            <input type="text" id="userInput" placeholder="<?php echo t('type_message'); ?>" autocomplete="off" required>
            <button type="submit"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
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
        const chatForm = document.getElementById('chatForm');
        const userInput = document.getElementById('userInput');
        const chatBox = document.getElementById('chatBox');
        const typingIndicator = document.getElementById('typingIndicator');

        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const text = userInput.value.trim();
            if (!text) return;

            appendMessage(text, 'msg-user');
            userInput.value = '';
            
            typingIndicator.style.display = 'block';
            chatBox.appendChild(typingIndicator);
            scrollToBottom();

            // Connect to PHP Back-End
            try {
                const response = await fetch('gemini_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: text })
                });
                
                const data = await response.json();
                typingIndicator.style.display = 'none';
                
                if (data.reply) {
                    let formattedReply = data.reply.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    appendMessage(formattedReply, 'msg-ai');
                } else {
                    appendMessage("Error: " + data.error, 'msg-ai');
                }
            } catch (error) {
                typingIndicator.style.display = 'none';
                appendMessage("Network error: Could not reach the server.", 'msg-ai');
            }
        });

        function appendMessage(text, className) {
            const div = document.createElement('div');
            div.className = `message ${className}`;
            div.innerText = text;
            
            if(className === 'msg-ai') {
                div.innerHTML = `<i class="fa-solid fa-bolt" style="color: #dc3545; margin-right:5px; margin-left:5px;"></i> ` + text;
            }
            
            chatBox.insertBefore(div, typingIndicator);
            scrollToBottom();
        }

        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
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