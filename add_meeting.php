<?php
require 'lang.php';
require 'db.php';

// Block unauthorized access (Only Admin/Moderator allowed)
if (!isset($_SESSION['username']) || $_SESSION['role'] == 'User') {
    header("Location: index.php");
    exit();
}

$error = '';
// Variables to remember form inputs if an error occurs
$val_user = ''; $val_title = ''; $val_date = ''; $val_time = ''; $val_end = ''; $val_room = ''; $val_status = 'pending';

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emp_username = $conn->real_escape_string($_POST['username']);
    $title = $conn->real_escape_string($_POST['title']);
    $date = $conn->real_escape_string($_POST['meeting_date']);
    $time = $conn->real_escape_string($_POST['meeting_time']);
    $end_time = $conn->real_escape_string($_POST['end_time']);
    $room = $conn->real_escape_string($_POST['room']);
    $status = $conn->real_escape_string($_POST['status']);

    // Remember inputs for UX
    $val_user = $_POST['username'];
    $val_title = htmlspecialchars($_POST['title']);
    $val_date = $_POST['meeting_date'];
    $val_time = $_POST['meeting_time'];
    $val_end = $_POST['end_time'];
    $val_room = htmlspecialchars($_POST['room']);
    $val_status = $_POST['status'];

    // 1. Time Logic Validation
    if (strtotime($end_time) <= strtotime($time)) {
        $error = t('time_error');
    } else {
        // 2. Database Conflict Validation
        // Checks if the user or the room is busy during the requested timeframe (Ignoring Cancelled meetings)
        $conflict_sql = "SELECT id FROM meetings 
                         WHERE meeting_date = '$date' 
                         AND status NOT IN ('Cancelled', 'cancelled') 
                         AND (username = '$emp_username' OR (room = '$room' AND room != 'TBD' AND room != '')) 
                         AND ('$time' < end_time AND '$end_time' > meeting_time)";
                         
        $conflict_res = $conn->query($conflict_sql);

        if ($conflict_res && $conflict_res->num_rows > 0) {
            $error = t('conflict_error');
        } else {
            // No conflicts! Insert into Database.
            $sql = "INSERT INTO meetings (username, title, meeting_date, meeting_time, end_time, room, status) 
                    VALUES ('$emp_username', '$title', '$date', '$time', '$end_time', '$room', '$status')";
            
            if($conn->query($sql)) {
                header("Location: schedules.php");
                exit();
            }
        }
    }
}

// Fetch all users for the dropdown
$users_result = $conn->query("SELECT username, full_name FROM users ORDER BY full_name ASC");
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
        label { font-weight: bold; margin-top: 10px; display: block; font-size: 14px;}
        input, select { width: 100%; padding: 10px; margin-top: 5px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px; font-size: 15px; }
        input:focus, select:focus { border-color: #004b87; outline: none; }
        .btn-save { width: 100%; background: #28a745; color: white; padding: 12px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; font-weight: bold; }
        .btn-save:hover { background: #218838; }
        .btn-cancel { display: block; text-align: center; margin-top: 15px; color: red; text-decoration: none; font-weight: bold;}
        .error { color: #dc3545; font-size: 13px; margin-bottom: 15px; background: #f8d7da; padding: 10px; border-radius: 5px; text-align: <?php echo $lang == 'ar' ? 'right' : 'left'; ?>; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2><i class="fa-solid fa-calendar-plus"></i> <?php echo t('add_meeting'); ?></h2>
        
        <?php if($error) echo "<div class='error'><i class='fa-solid fa-triangle-exclamation'></i> $error</div>"; ?>

        <form method="POST" action="">
            <label><?php echo t('employee_name'); ?></label>
            <select name="username" required>
                <option value=""><?php echo t('select_user'); ?></option>
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