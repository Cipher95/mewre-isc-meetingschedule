<?php
require 'lang.php';
require 'db.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = t('passwords_mismatch');
    } else {
        // Check if username exists
        $check = $conn->query("SELECT id FROM users WHERE username = '$username'");
        if ($check->num_rows > 0) {
            $error = t('username_taken');
        } else {
            // Hash password and insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            // 'User' role is assigned automatically by the database schema DEFAULT
            $sql = "INSERT INTO users (username, password, full_name) VALUES ('$username', '$hashed_password', '$full_name')";
            
            if ($conn->query($sql)) {
                $success = t('register_success');
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
        body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 450px; text-align: center; position: relative; }
        .login-box h2 { color: #004b87; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; background: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px;}
        button:hover { background: #218838; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
        .success { color: green; font-size: 14px; margin-bottom: 10px; font-weight: bold; }
        .lang-switch { position: absolute; top: 15px; <?php echo $lang == 'ar' ? 'left: 15px;' : 'right: 15px;'; ?> text-decoration: none; color: #004b87; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-box">
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        <h2><i class="fa-solid fa-user-plus"></i> <?php echo t('create_account'); ?></h2>
        
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        
        <form method="POST" action="">
            <input type="text" name="full_name" placeholder="<?php echo t('employee_name'); ?>" required>
            <input type="text" name="username" placeholder="<?php echo t('username'); ?>" required>
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