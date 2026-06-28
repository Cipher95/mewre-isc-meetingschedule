<?php
require 'lang.php';
require 'db.php';

// strictly ADMIN ONLY (Server-side lock)
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    die("<h1 style='color:red;text-align:center;margin-top:50px;'>" . t('admin_only') . "</h1>");
}

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$id = intval($_GET['id']);
$error = '';

// Fetch the user information we are resetting
$user_query = $conn->query("SELECT * FROM users WHERE id=$id");
if ($user_query->num_rows == 0) {
    header("Location: manage_users.php");
    exit();
}
$target_user = $user_query->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate Password Strength (Min 8 chars, 1 Uppercase, 1 Lowercase, 1 Number)
    if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $error = t('password_weak');
    } 
    // Check if passwords match
    elseif ($new_password !== $confirm_password) {
        $error = t('passwords_mismatch');
    } else {
        // Encrypt the new password and update
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password='$hashed_password' WHERE id=$id";
        
        if ($conn->query($sql)) {
            // Redirect back to user directory with success message
            header("Location: manage_users.php?reset_success=1");
            exit();
        } else {
            $error = "Database error.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('admin_reset_title'); ?> | Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh;}
        .form-container { width: 100%; max-width: 500px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative;}
        h2 { color: #004b87; margin-bottom: 20px; text-align: center; }
        label { font-weight: bold; margin-top: 10px; display: block; font-size: 14px; color: #333;}
        input { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 15px;}
        input:focus { border-color: #004b87; outline: none; }
        .btn-save { width: 100%; background: #dc3545; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.2s;}
        .btn-save:hover { background: #004b87; color: white;}
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-weight: bold;}
        .btn-cancel:hover { color: #dc3545; }
        
        .error { color: #dc3545; font-size: 13px; margin-bottom: 15px; background: #f8d7da; padding: 10px; border-radius: 5px; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
        .lang-switch { position: absolute; top: 15px; <?php echo $lang == 'ar' ? 'left: 15px;' : 'right: 15px;'; ?> text-decoration: none; color: #004b87; font-weight: bold; }
        .employee-badge { background: #f4f6f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; border-left: 4px solid #e5b13a; }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        <h2><i class="fa-solid fa-user-shield"></i> <?php echo t('reset_password'); ?></h2>
        
        <!-- Display Who we are resetting the password for -->
        <div class="employee-badge">
            <strong><?php echo htmlspecialchars($target_user['full_name']); ?></strong><br>
            <span style="color:#666; font-size:13px;"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($target_user['username']); ?> | <i class="fa-solid fa-shield-halved"></i> <?php echo $target_user['role']; ?></span>
        </div>

        <?php if($error) echo "<div class='error'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>
        
        <form method="POST" action="">
            <label><?php echo t('new_password'); ?></label>
            <input type="password" name="new_password" required placeholder="********">
            
            <label><?php echo t('confirm_password'); ?></label>
            <input type="password" name="confirm_password" required placeholder="********">

            <button type="submit" class="btn-save"><i class="fa-solid fa-key"></i> <?php echo t('reset_password'); ?></button>
            <a href="manage_users.php" class="btn-cancel"><i class="fa-solid fa-arrow-left"></i> <?php echo t('cancel'); ?></a>
        </form>
    </div>
</body>
</html>