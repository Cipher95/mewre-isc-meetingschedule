<?php
require 'lang.php';
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check for username instead of civil_id
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username']; // Stored as username
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role']; 
            header("Location: index.php");
            exit();
        } else {
            $error = t('invalid_pass');
        }
    } else {
        $error = t('username_not_found'); // Updated error message
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('login'); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 100%; max-width: 400px; text-align: center; position: relative; }
        .login-box h2 { color: #004b87; margin-bottom: 20px; }
        input { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; background: #004b87; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 10px;}
        button:hover { background: #e5b13a; color: #333; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
        .lang-switch { position: absolute; top: 15px; <?php echo $lang == 'ar' ? 'left: 15px;' : 'right: 15px;'; ?> text-decoration: none; color: #004b87; font-weight: bold; }
    </style>
</head>
<body>
    <div class="login-box">
        <a href="?lang=<?php echo t('lang_toggle'); ?>" class="lang-switch"><i class="fa-solid fa-globe"></i> <?php echo t('lang_btn'); ?></a>
        
        <h2><?php echo t('login'); ?></h2>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        
        <form method="POST" action="">
            <!-- Changed input to username -->
            <input type="text" name="username" placeholder="<?php echo t('username'); ?>" required>
            <input type="password" name="password" placeholder="<?php echo t('password'); ?>" required>
            <button type="submit"><?php echo t('login'); ?></button>
        </form>
        <p style="margin-top: 15px;">
            <a href="register.php" style="color: #28a745; font-weight: bold; text-decoration: none;">
                <i class="fa-solid fa-user-plus"></i> <?php echo t('create_account'); ?>
            </a>
        </p>
        
        <p style="margin-top: 20px;">
            <a href="index.php" style="color: #004b87; text-decoration: none;">
                <i class="fa-solid <?php echo $lang == 'ar' ? 'fa-arrow-right' : 'fa-arrow-left'; ?>"></i> <?php echo t('back_home'); ?>
            </a>
        </p>
    </div>
</body>
</html>
