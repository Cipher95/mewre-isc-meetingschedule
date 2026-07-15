<?php
require 'lang.php';
require 'db.php';

// Block unauthorized access
if (!isset($_SESSION['username']) || $_SESSION['role'] == 'User') {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: schedules.php");
    exit();
}

$id = intval($_GET['id']);
$error = '';

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_username = $conn->real_escape_string($_POST['username']);
    $title = $conn->real_escape_string($_POST['title']);
    $date = $conn->real_escape_string($_POST['meeting_date']);
    $time = $conn->real_escape_string($_POST['meeting_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $room = $conn->real_escape_string($_POST['room']);
    $status = $conn->real_escape_string($_POST['status']);

    // 1. Time Logic Validation
    if (strtotime($end_time) <= strtotime($time)) {
        $error = t('time_error');
    } else {
        // 2. Conflict Validation (Make sure to exclude the CURRENT meeting ID from the check)
        $conflict_sql = "SELECT id FROM meetings 
                         WHERE meeting_date = '$date' 
                         AND id != $id 
                         AND status NOT IN ('Cancelled', 'cancelled') 
                         AND (username = '$emp_username' OR (room = '$room' AND room != 'TBD' AND room != '')) 
                         AND ('$time' < end_time AND '$end_time' > meeting_time)";
                         
        $conflict_res = $conn->query($conflict_sql);

        if ($conflict_res && $conflict_res->num_rows > 0) {
            $error = t('conflict_error');
        } else {
            // No conflict! Update Database.
            $sql = "UPDATE meetings SET 
                    username='$emp_username', title='$title', meeting_date='$date', meeting_time='$time', end_time='$end_time', room='$room', status='$status' 
                    WHERE id=$id";
            
            if($conn->query($sql)) {
                header("Location: schedules.php");
                exit();
            }
        }
    }
}

// Fetch current meeting details to pre-fill the form
$meeting_query = $conn->query("SELECT * FROM meetings WHERE id=$id");
$meeting = $meeting_query->fetch_assoc();

// Memory for UX if an error occurs
$val_user = isset($_POST['username']) ? $_POST['username'] : $meeting['username'];
$val_title = isset($_POST['title']) ? htmlspecialchars($_POST['title']) : htmlspecialchars($meeting['title']);
$val_date = isset($_POST['meeting_date']) ? $_POST['meeting_date'] : $meeting['meeting_date'];
$val_time = isset($_POST['meeting_time']) ? $_POST['meeting_time'] : $meeting['meeting_time'];
$val_end = isset($_POST['end_time']) ? $_POST['end_time'] : $meeting['end_time'];
$val_room = isset($_POST['room']) ? htmlspecialchars($_POST['room']) : htmlspecialchars($meeting['room']);
$val_status = isset($_POST['status']) ? $_POST['status'] : $meeting['status'];

$users_result = $conn->query("SELECT username, full_name FROM users ORDER BY full_name ASC");
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>" dir="<?php echo t('dir'); ?>">
<head>
    <meta charset="UTF-8">
    <title><?php echo t('edit'); ?> | MEW ISC</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; font-family: <?php echo t('font'); ?>; }
        body { background: #f8f9fa; margin: 0; padding: 20px; }
        .form-container { max-width: 600px; margin: 40px auto; padding: 30px; background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        h2 { color: #e5b13a; margin-bottom: 20px; text-align: center; }
        label { font-weight: bold; margin-top: 10px; display: block; font-size: 14px;}
        input, select { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; }
        input:focus, select:focus { border-color: #004b87; outline: none; }
        .btn-save { width: 100%; background: #004b87; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-save:hover { background: #e5b13a; color: #333;}
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: red; text-decoration: none; font-weight: bold;}
        .error { color: #dc3545; font-size: 13px; margin-bottom: 15px; background: #f8d7da; padding: 10px; border-radius: 5px; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fa-solid fa-pen-to-square"></i> <?php echo t('edit'); ?></h2>
        
        <?php if($error) echo "<div class='error'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>

        <form method="POST" action="">
            <label><?php echo t('employee_name'); ?></label>
            <select name="username" required>
                <?php while($u = $users_result->fetch_assoc()): ?>
                    <option value="<?php echo $u['username']; ?>" <?php if($val_user == $u['username']) echo 'selected'; ?>>
                        <?php echo $u['full_name']; ?> (<?php echo $u['username']; ?>)
                    </option>
                <?php endwhile; ?>
            </select>

            <label><?php echo t('meeting_title'); ?></label>
            <input type="text" name="title" value="<?php echo $val_title; ?>" required>

            <label><?php echo t('date'); ?></label>
            <input type="date" name="meeting_date" value="<?php echo $val_date; ?>" required>

            <div style="display: flex; gap: 15px;">
                <div style="width: 50%;">
                    <label><?php echo t('from'); ?></label>
                    <input type="time" name="meeting_time" value="<?php echo $val_time; ?>" required>
                </div>
                <div style="width: 50%;">
                    <label><?php echo t('to'); ?></label>
                    <input type="time" name="end_time" value="<?php echo $val_end; ?>" required>
                </div>
            </div>

            <label><?php echo t('room'); ?></label>
            <input type="text" name="room" value="<?php echo $val_room; ?>" required>

            <label><?php echo t('status'); ?></label>
            <select name="status">
                <option value="not_started" <?php if($val_status == 'not_started') echo 'selected'; ?>><?php echo t('not_started'); ?></option>
                <option value="pending" <?php if($val_status == 'pending') echo 'selected'; ?>><?php echo t('pending'); ?></option>
                <option value="in_progress" <?php if($val_status == 'in_progress') echo 'selected'; ?>><?php echo t('in_progress'); ?></option>
                <option value="completed" <?php if($val_status == 'completed' || $val_status == 'Completed') echo 'selected'; ?>><?php echo t('completed'); ?></option>
            </select>

            <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk"></i> <?php echo t('save'); ?></button>
            <a href="schedules.php" class="btn-cancel"><?php echo t('cancel'); ?></a>
        </form>
    </div>
</body>
</html>