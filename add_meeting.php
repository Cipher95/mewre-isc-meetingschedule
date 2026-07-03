<?php
require 'lang.php';
require 'db.php';

// 1. Block unauthorized access (Only Admin/Moderator allowed)
if (!isset($_SESSION['username']) || $_SESSION['role'] == 'User') {
    header("Location: index.php");
    exit();
}

// 2. Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_username = $conn->real_escape_string($_POST['username']);
    $title = $conn->real_escape_string($_POST['title']);
    $date = $conn->real_escape_string($_POST['meeting_date']);
    $time = $conn->real_escape_string($_POST['meeting_time']);
    $room = $conn->real_escape_string($_POST['room']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "INSERT INTO meetings (username, title, meeting_date, meeting_time, room, status) 
            VALUES ('$emp_username', '$title', '$date', '$time', '$room', '$status')";
    
    if($conn->query($sql)) {
        header("Location: schedule.php");
        exit();
    }
}

// Fetch all users to populate the dropdown menu
$users_result = $conn->query("SELECT username, full_name FROM users");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('add_meeting'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 20px; }
        .form-container { max-width: 600px; margin: 40px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #004b87; margin-bottom: 20px; text-align: center; }
        label { font-weight: bold; margin-top: 10px; display: block; }
        input, select { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; }
        .btn-save { width: 100%; background: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-save:hover { background: #218838; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: red; text-decoration: none; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fa-solid fa-calendar-plus"></i> <?php echo t('add_meeting'); ?></h2>
        <form method="POST" action="">
            <label><?php echo t('employee_name'); ?></label>
            <select name="username" required>
                <option value=""><?php echo t('select_user'); ?></option>
                <?php while($u = $users_result->fetch_assoc()): ?>
                    <option value="<?php echo $u['username']; ?>"><?php echo $u['full_name']; ?> (<?php echo $u['username']; ?>)</option>
                <?php endwhile; ?>
            </select>

            <label><?php echo t('meeting_title'); ?></label>
            <input type="text" name="title" required>

            <label><?php echo t('date'); ?></label>
            <input type="date" name="meeting_date" required>

            <label><?php echo t('time'); ?></label>
            <input type="time" name="meeting_time" required>

            <label><?php echo t('room'); ?></label>
            <input type="text" name="room" required>

            <label><?php echo t('status'); ?></label>
            <select name="status">
                <option value="Not Started"><?php echo t('not_started'); ?></option>
                <option value="In Progress"><?php echo t('in_progress'); ?></option>
                <option value="Pending"><?php echo t('pending'); ?></option>
                <option value="Completed"><?php echo t('completed'); ?></option>
            </select>

            <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> <?php echo t('save'); ?></button>
            <a href="schedule.php" class="btn-cancel"><?php echo t('cancel'); ?></a>
        </form>
    </div>
</body>
</html>