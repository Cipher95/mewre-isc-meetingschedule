<?php
session_start();
require 'db.php';

// 1. Strict Server-Side Admin Check
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Admin') {
    header("HTTP/1.1 403 Forbidden");
    exit("Access Denied.");
}

if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $current_admin_id = intval($_SESSION['user_id']);

    // 2. Prevent the Admin from deleting themselves
    if ($user_id === $current_admin_id) {
        header("Location: manage_users.php?delete_error=self");
        exit();
    }

    // 3. Retrieve the username before deleting so we can clean up their meetings
    $user_query = $conn->query("SELECT username FROM users WHERE id = $user_id");
    if ($user_query && $user_query->num_rows > 0) {
        $user_data = $user_query->fetch_assoc();
        $target_username = $conn->real_escape_string($user_data['username']);

        // 4. Delete associated meetings first to avoid orphaned records
        $conn->query("DELETE FROM meetings WHERE username = '$target_username'");

        // 5. Delete the user account
        $conn->query("DELETE FROM users WHERE id = $user_id");

        header("Location: manage_users.php?delete_success=1");
        exit();
    }
}

header("Location: manage_users.php");
exit();
?>