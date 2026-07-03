<?php
require 'lang.php';
require 'db.php';

// Fetch ONLY Admins and Moderators. Order by Admin first, then alphabetically.
$sql = "SELECT full_name, role FROM users WHERE role IN ('Admin', 'Moderator') ORDER BY role ASC, full_name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('staff_directory'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 40px 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h2 { color: #004b87; margin-bottom: 30px; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 15px;}
        
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        
        .staff-card { 
            background: #fff; 
            border: 1px solid #ddd; 
            padding: 25px; 
            border-radius: 8px; 
            text-align: center; 
            transition: 0.3s; 
            border-top: 5px solid #004b87;
        }
        .staff-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .staff-card.admin { border-top-color: #dc3545; } /* Red top border for Admins */
        .staff-card.mod { border-top-color: #e5b13a; } /* Gold top border for Moderators */
        
        .icon-circle {
            width: 60px; height: 60px; 
            background: #f4f6f9; 
            border-radius: 50%; 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 24px; 
            color: #004b87; 
            margin-bottom: 15px;
        }
        
        .staff-card h3 { margin: 0 0 10px 0; color: #333; font-size: 18px; }
        .badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 12px; color: white;}
        .badge.admin { background: #dc3545; }
        .badge.mod { background: #e5b13a; color: #333;}
        
        .btn-back { display: inline-block; margin-top: 30px; background: #004b87; color: white; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold;}
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fa-solid fa-address-book" style="color: #e5b13a;"></i> <?php echo t('staff_directory'); ?></h2>
        
        <div class="grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="staff-card <?php echo $row['role'] == 'Admin' ? 'admin' : 'mod'; ?>">
                        <div class="icon-circle">
                            <i class="fa-solid <?php echo $row['role'] == 'Admin' ? 'fa-user-shield' : 'fa-user-tie'; ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($row['full_name']); ?></h3>
                        <div class="badge <?php echo $row['role'] == 'Admin' ? 'admin' : 'mod'; ?>">
                            <?php echo $row['role'] == 'Admin' ? t('role_admin') : t('role_mod'); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; width: 100%; color: #666;">No staff found.</p>
            <?php endif; ?>
        </div>

        <div style="text-align: center;">
            <a href="index.php" class="btn-back"><i class="fa-solid fa-arrow-left"></i> <?php echo t('back_home'); ?></a>
        </div>
    </div>
</body>
</html>