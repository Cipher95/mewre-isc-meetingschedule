<?php
require 'lang.php';
require 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $new_password = $_POST['new_password'];

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET full_name='$full_name', password='$hashed_password' WHERE username='$username'";
    } else {
        $sql = "UPDATE users SET full_name='$full_name' WHERE username='$username'";
    }

    if ($conn->query($sql)) {
        $_SESSION['full_name'] = $full_name; // Update session
        $success = t('profile_updated');
    }
}

// Fetch current info
$user_query = $conn->query("SELECT * FROM users WHERE username='$username'");
$user = $user_query->fetch_assoc();
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
        body { background: #f8f9fa; margin: 0; padding: 20px; }
        .form-container { max-width: 600px; margin: 40px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #004b87; margin-bottom: 20px; text-align: center; }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-save { width: 100%; background: #004b87; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-save:hover { background: #e5b13a; color: #333;}
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; }
        .success { color: green; text-align: center; font-weight: bold; margin-bottom: 15px;}
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fa-solid fa-id-card"></i> <?php echo t('my_profile'); ?></h2>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        
        <form method="POST" action="">
            <label><?php echo t('username'); ?> (Read-Only)</label>
            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled style="background:#e9ecef;">

            <label><?php echo t('employee_name'); ?></label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

            <label><?php echo t('new_password'); ?></label>
            <input type="password" name="new_password" placeholder="********">

            <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> <?php echo t('update_profile'); ?></button>
            <a href="dashboard.php" class="btn-cancel"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
        </form>
    </div>
</body>
</html>