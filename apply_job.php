<?php
session_start();
include 'db.php'; // Database connection

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure job ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid job ID.";
    header("Location: student_dashboard.php");
    exit();
}

$job_id = intval($_GET['id']);

// Fetch student_id from students table
$student_query = "SELECT id, cgpa FROM students WHERE user_id = $user_id";
$student_result = $conn->query($student_query);

if ($student_result->num_rows > 0) {
    $student_row = $student_result->fetch_assoc();
    $student_id = $student_row['id'];
    $student_cgpa = $student_row['cgpa'];
} else {
    $_SESSION['error'] = "Error: Student profile not found.";
    header("Location: student_dashboard.php");
    exit();
}

// Fetch job eligibility criteria
$job_query = "SELECT eligibility_criteria FROM jobs WHERE id = $job_id AND status = 'Approved'";
$job_result = $conn->query($job_query);

if ($job_result->num_rows > 0) {
    $job_row = $job_result->fetch_assoc();
    $eligibility_criteria = $job_row['eligibility_criteria'];

    // Check CGPA eligibility
    if (strpos($eligibility_criteria, "CGPA") !== false) {
        preg_match('/CGPA\s*>\s*([0-9.]+)/', $eligibility_criteria, $matches);
        if (isset($matches[1]) && $student_cgpa <= floatval($matches[1])) {
            $_SESSION['error'] = "You do not meet the CGPA eligibility criteria for this job.";
            header("Location: student_dashboard.php");
            exit();
        }
    }
} else {
    $_SESSION['error'] = "Job not found or not approved.";
    header("Location: student_dashboard.php");
    exit();
}

// Check if the student has already applied for this job
$check_query = "SELECT id FROM applications WHERE student_id = $student_id AND job_id = $job_id";
$check_result = $conn->query($check_query);

if ($check_result->num_rows > 0) {
    $_SESSION['error'] = "You have already applied for this job.";
    header("Location: student_dashboard.php");
    exit();
}

// Insert application
$apply_query = "INSERT INTO applications (student_id, job_id, status, applied_at) 
                VALUES ($student_id, $job_id, 'applied', NOW())";

if ($conn->query($apply_query) === TRUE) {
    $_SESSION['success'] = "Application submitted successfully.";
} else {
    $_SESSION['error'] = "Error applying for job.";
}

header("Location: student_dashboard.php");
exit();
?>
