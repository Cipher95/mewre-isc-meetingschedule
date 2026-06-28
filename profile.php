<?php
require 'lang.php';
require 'db.php';

// Check if logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Trim whitespaces
    $full_name = trim($_POST['full_name']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Validate Full Name (Min 3 chars, letters and spaces only)
    if (mb_strlen($full_name) < 3 || !preg_match('/^[\p{L}\s]+$/u', $full_name)) {
        $error = t('name_invalid');
    } 
    // 3. Validate New Password (ONLY if the user actually typed a new password)
    elseif (!empty($new_password)) {
        if (strlen($new_password) < 8 || !preg_match('/[A-Z]/', $new_password) || !preg_match('/[a-z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
            $error = t('password_weak');
        } 
        // 4. Check if passwords match
        elseif ($new_password !== $confirm_password) {
            $error = t('passwords_mismatch');
        }
    }

    // 5. If no errors, update the database
    if (empty($error)) {
        $db_full_name = $conn->real_escape_string($full_name);

        if (!empty($new_password)) {
            // Update Name AND Password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET full_name='$db_full_name', password='$hashed_password' WHERE username='$username'";
        } else {
            // Update Name ONLY
            $sql = "UPDATE users SET full_name='$db_full_name' WHERE username='$username'";
        }

        if ($conn->query($sql)) {
            $_SESSION['full_name'] = $full_name; // Update active session
            $success = t('profile_updated');
        }
    }
}

// Fetch current info to pre-fill the form
$user_query = $conn->query("SELECT * FROM users WHERE username='$username'");
$user = $user_query->fetch_assoc();

// Use the POSTed name if there was an error, otherwise use the Database name
$display_name = isset($_POST['full_name']) && $error ? htmlspecialchars($_POST['full_name']) : htmlspecialchars($user['full_name']);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('my_profile'); ?></title>
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
        .btn-save { width: 100%; background: #004b87; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; transition: 0.2s;}
        .btn-save:hover { background: #e5b13a; color: #333;}
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-weight: bold;}
        .btn-cancel:hover { color: #dc3545; }
        
        /* Message Boxes */
        .error { color: #dc3545; font-size: 13px; margin-bottom: 15px; background: #f8d7da; padding: 10px; border-radius: 5px; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
        .success { color: #155724; font-size: 14px; margin-bottom: 15px; font-weight: bold; background: #d4edda; padding: 10px; border-radius: 5px; text-align: center;}
        .lang-switch { position: absolute; top: 15px; <?php echo $lang == 'ar' ? 'left: 15px;' : 'right: 15px;'; ?> text-decoration: none; color: #004b87; font-weight: bold; }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        <h2><i class="fa-solid fa-id-card"></i> <?php echo t('my_profile'); ?></h2>
        
        <?php if($error) echo "<div class='error'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>
        <?php if($success) echo "<div class='success'><i class='fa-solid fa-check-circle'></i> $success</div>"; ?>
        
        <form method="POST" action="">
            <label><?php echo t('username'); ?> (Read-Only)</label>
            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled style="background:#e9ecef; cursor: not-allowed;">

            <label><?php echo t('employee_name'); ?></label>
            <input type="text" name="full_name" value="<?php echo $display_name; ?>" required>

            <label><?php echo t('new_password'); ?></label>
            <input type="password" name="new_password" placeholder="********">
            
            <!-- NEW: Confirm Password Field -->
            <label><?php echo t('confirm_password'); ?> (<?php echo $lang == 'ar' ? 'مطلوب فقط عند التغيير' : 'Only required if changing'; ?>)</label>
            <input type="password" name="confirm_password" placeholder="********">

            <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> <?php echo t('update_profile'); ?></button>
            <a href="dashboard.php" class="btn-cancel"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
        </form>
    </div>
</body>
</html>