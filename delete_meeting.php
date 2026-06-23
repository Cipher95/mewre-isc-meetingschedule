<?php
session_start();
require 'db.php';

// Block unauthorized access (Server-Side Protection)
if (!isset($_SESSION['username']) || $_SESSION['role'] == 'User') {
    header("Location: index.php");
    exit();
}

// Delete query
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM meetings WHERE id = $id");
}

// Send them right back
header("Location: schedules.php");
exit();
?>