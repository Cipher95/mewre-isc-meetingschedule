<?php
require 'lang.php';
require 'db.php';

// strictly ADMIN ONLY
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    die("<h1 style='color:red;text-align:center;margin-top:50px;'>" . t('admin_only') . "</h1>");
}

$success = '';

// Check for status parameters returned from delete_user.php or admin_reset_password.php
if (isset($_GET['reset_success'])) {
    $success = "<span style='color:green;'>" . t('password_reset_success') . "</span>";
} elseif (isset($_GET['delete_success'])) {
    $success = "<span style='color:green;'>" . t('user_deleted_success') . "</span>";
} elseif (isset($_GET['delete_error']) && $_GET['delete_error'] == 'self') {
    $success = "<span style='color:red;'>" . t('delete_self_error') . "</span>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $conn->real_escape_string($_POST['role']);

    if ($_SESSION['user_id'] == $user_id && $new_role !== 'Admin') {
        $success = "<span style='color:red;'>You cannot demote yourself.</span>";
    } else {
        $conn->query("UPDATE users SET role='$new_role' WHERE id=$user_id");
        $success = "<span style='color:green;'>" . t('role_updated') . "</span>";
    }
}

$users_result = $conn->query("SELECT * FROM users ORDER BY role ASC, full_name ASC");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('manage_users'); ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: 20px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #004b87; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; border-bottom: 1px solid #ddd; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
        th { background-color: #333; color: white; }
        select { padding: 5px; border-radius: 4px; }
        .btn-update { background: #004b87; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-update:hover { background: #e5b13a; color: #333; }
        .btn-back { display: inline-block; margin-top: 20px; background: #666; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px;}
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
    <div class="container">
        <h2><i class="fa-solid fa-users-gear"></i> <?php echo t('manage_users'); ?></h2>
        <?php if($success) echo "<p>$success</p>"; ?>
        <!-- SEARCH BAR -->
        <div class="search-container">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="<?php echo t('search'); ?>">
        </div>
        
        <table>
            <tr>
                <th><?php echo t('employee_name'); ?></th>
                <th><?php echo t('username'); ?></th>
                <th><?php echo t('user_role'); ?></th>
                <th><?php echo t('action'); ?></th>
            </tr>
            <?php while($u = $users_result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td>
                    <form method="POST" action="" style="display:flex; gap:10px; align-items:center;">
                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                        <select name="role">
                            <option value="User" <?php if($u['role'] == 'User') echo 'selected'; ?>>User</option>
                            <option value="Moderator" <?php if($u['role'] == 'Moderator') echo 'selected'; ?>>Moderator</option>
                            <option value="Admin" <?php if($u['role'] == 'Admin') echo 'selected'; ?>>Admin</option>
                        </select>
                </td>
                <td>
                        <button type="submit" class="btn-update"><i class="fa-solid fa-check"></i> <?php echo t('update_role'); ?></button>
                    </form>
             <!-- NEW RESET PASSWORD LINK FOR ADMINS -->
                    <a href="admin_reset_password.php?id=<?php echo $u['id']; ?>" class="btn-update" style="background-color: #dc3545; color: white; text-decoration: none; margin-top: 2px; display: inline-block;">
                        <i class="fa-solid fa-key"></i> <?php echo t('reset_password'); ?>
                    </a>
             <!-- NEW: DELETE USER BUTTON -->
                    <a href="delete_user.php?id=<?php echo $u['id']; ?>" class="btn-update" style="background-color: #dc3545; color: white; text-decoration: none; margin-top: 2px; display: inline-block;" onclick="return confirm('<?php echo t("confirm_delete_user"); ?>');">
                        <i class="fa-solid fa-user-minus"></i> <?php echo t('delete_user'); ?>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        
        <a href="schedule.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back'); ?></a>
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
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('table tr');

            for (let i = 1; i < rows.length; i++) {
                let match = false;
                let tds = rows[i].getElementsByTagName('td');
                
                for (let j = 0; j < tds.length; j++) {
                    // Check if there is a dropdown in this cell
                    let selectElement = tds[j].querySelector('select');
                    let cellText = "";
                    
                    if (selectElement) {
                        // Get the text of the currently selected role
                        cellText = selectElement.options[selectElement.selectedIndex].text;
                    } else {
                        // Just get the normal text
                        cellText = tds[j].innerText;
                    }

                    if (cellText.toLowerCase().includes(filter)) {
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