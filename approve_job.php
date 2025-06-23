<?php
session_start();
include 'db.php'; // Database connection

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['placement_officer', 'admin'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $job_id = intval($_POST['job_id']);
    $visibility_date = $_POST['visibility_date'];
    $deadline = $_POST['deadline'];
    $recruitment_mode = $_POST['recruitment_mode'];
    $approved_by = $_SESSION['user_id'];

    // Update job status in jobs table
    $update_job_status_query = "UPDATE jobs SET status = 'Approved' WHERE id = $job_id";

    // Insert approval details into job_approvals table
    $insert_approval_query = "INSERT INTO job_approvals (job_id, approved_by, visibility_date, deadline, recruitment_mode) 
                              VALUES ($job_id, $approved_by, '$visibility_date', '$deadline', '$recruitment_mode')";

    if ($conn->query($update_job_status_query) === TRUE && $conn->query($insert_approval_query) === TRUE) {
        header("Location: upcoming_drives.php?success=Job approved successfully.");
    } else {
        header("Location: upcoming_drives.php?error=Error approving job.");
    }
}

exit();
?>
