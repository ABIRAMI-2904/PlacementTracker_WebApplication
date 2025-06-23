<?php
session_start();
include 'db.php';

// Ensure the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $job_id = intval($_GET['id']);

    // Fetch company_id
    $company_query = "SELECT id FROM companies WHERE user_id = $user_id";
    $company_result = $conn->query($company_query);

    if ($company_result->num_rows > 0) {
        $company_id = $company_result->fetch_assoc()['id'];

        // Verify job ownership
        $job_check_query = "SELECT id FROM jobs WHERE id = $job_id AND company_id = $company_id";
        $job_check_result = $conn->query($job_check_query);

        if ($job_check_result->num_rows > 0) {
            // Delete job
            $delete_query = "DELETE FROM jobs WHERE id = $job_id";
            if ($conn->query($delete_query)) {
                $_SESSION['success_message'] = "Job deleted successfully!";
            } else {
                $_SESSION['error_message'] = "Error deleting job: " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "Unauthorized action!";
        }
    }
}

header("Location: company_dashboard.php");
exit();
