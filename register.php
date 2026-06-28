<?php
require 'lang.php';
require 'db.php';

$error = '';
$success = '';

// Variables to remember form inputs
$val_name = '';
$val_user = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Trim whitespaces from inputs
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Save values for UX (so user doesn't have to retype if error occurs)
    $val_name = htmlspecialchars($full_name);
    $val_user = htmlspecialchars($username);

    // 2. Validate Full Name (Min 3 chars, letters and spaces only - supports Arabic & English)
    // \p{L} matches any kind of letter from any language
    if (mb_strlen($full_name) < 3 || !preg_match('/^[\p{L}\s]+$/u', $full_name)) {
        $error = t('name_invalid');
    }
    // 3. Validate Username (4 to 20 chars, alphanumeric and underscores only)
    elseif (strlen($username) < 4 || strlen($username) > 20 || !preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = t('username_invalid');
    }
    // 4. Validate Password Strength (Min 8 chars, 1 Uppercase, 1 Lowercase, 1 Number)
    elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $error = t('password_weak');
    }
    // 5. Check if Passwords Match
    elseif ($password !== $confirm_password) {
        $error = t('passwords_mismatch');
    } 
    // 6. Database Operations
    else {
        // Escape strings for security before querying
        $db_username = $conn->real_escape_string($username);
        $db_full_name = $conn->real_escape_string($full_name);

        // Check if username exists
        $check = $conn->query("SELECT id FROM users WHERE username = '$db_username'");
        if ($check->num_rows > 0) {
            $error = t('username_taken');
        } else {
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (username, password, full_name) VALUES ('$db_username', '$hashed_password', '$db_full_name')";
            
            if ($conn->query($sql)) {
                $success = t('register_success');
                // Clear fields on success
                $val_name = '';
                $val_user = '';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('create_account'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px;}
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; position: relative; }
        .login-box h2 { color: #004b87; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        input:focus { border-color: #004b87; outline: none; }
        button { width: 100%; background: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px; transition: 0.2s;}
        button:hover { background: #218838; }
        .error { color: #dc3545; font-size: 13px; margin-bottom: 15px; background: #f8d7da; padding: 10px; border-radius: 5px; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
        .success { color: #155724; font-size: 14px; margin-bottom: 15px; font-weight: bold; background: #d4edda; padding: 10px; border-radius: 5px;}
        .lang-switch { position: absolute; top: 15px; <?php echo $lang == 'ar' ? 'left: 15px;' : 'right: 15px;'; ?> text-decoration: none; color: #004b87; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-box">
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        <h2><i class="fa-solid fa-user-plus"></i> <?php echo t('create_account'); ?></h2>
        
        <!-- Error & Success Messages -->
        <?php if($error) echo "<div class='error'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>
        <?php if($success) echo "<div class='success'><i class='fa-solid fa-check-circle'></i> $success</div>"; ?>
        
        <form method="POST" action="">
            <!-- Added 'value' attributes to remember inputs -->
            <input type="text" name="full_name" placeholder="<?php echo t('employee_name'); ?>" value="<?php echo $val_name; ?>" required>
            
            <input type="text" name="username" placeholder="<?php echo t('username'); ?>" value="<?php echo $val_user; ?>" required>
            
            <input type="password" name="password" placeholder="<?php echo t('password'); ?>" required>
            
            <input type="password" name="confirm_password" placeholder="<?php echo t('confirm_password'); ?>" required>
            
            <button type="submit"><?php echo t('register'); ?></button>
        </form>
        
        <p style="margin-top: 20px;">
            <a href="login.php" style="color: #004b87; text-decoration: none;"><i class="fa-solid fa-right-to-bracket"></i> <?php echo t('already_have_account'); ?></a>
        </p>
    </div>
</body>
</html>