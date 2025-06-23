<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['placement_officer', 'admin'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $job_id = intval($_GET['id']);

    // Update job status to 'Rejected'
    $reject_query = "UPDATE jobs SET status = 'Rejected' WHERE id = $job_id";

    if ($conn->query($reject_query) === TRUE) {
        header("Location: upcoming_drives.php?success=Job rejected successfully.");
    } else {
        header("Location: upcoming_drives.php?error=Error rejecting job.");
    }
}

exit();
?>
