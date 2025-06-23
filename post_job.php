<?php
session_start();
include 'db.php'; // Database connection

// Ensure the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = $error_message = "";

// Fetch company_id from companies table
$company_query = "SELECT id FROM companies WHERE user_id = $user_id";
$company_result = $conn->query($company_query);

if ($company_result->num_rows > 0) {
    $company_row = $company_result->fetch_assoc();
    $company_id = $company_row['id'];
} else {
    $error_message = "Company profile not found.";
    exit();
}

// Handle job posting form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $eligibility_criteria = $conn->real_escape_string($_POST['eligibility_criteria']);
    $package = (float) $_POST['package'];
    $location = $conn->real_escape_string($_POST['location']);
    $selection_process = $conn->real_escape_string($_POST['selection_process']);
    $campus_drive_date = $_POST['campus_drive_date'];

    if (empty($title) || empty($description) || empty($package) || empty($location) || empty($selection_process) || empty($campus_drive_date)) {
        $error_message = "All fields are required.";
    } else {
        $query = "INSERT INTO jobs (company_id, title, description, eligibility_criteria, package, location, selection_process, campus_drive_date, status) 
                  VALUES ($company_id, '$title', '$description', '$eligibility_criteria', $package, '$location', '$selection_process', '$campus_drive_date', 'Pending')";

        if ($conn->query($query)) {
            $_SESSION['success_message'] = "Job posted successfully! Waiting for approval.";
            header("Location: company_dashboard.php");
            exit();
        } else {
            $error_message = "Error posting job: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f9; }
        .card { max-width: 600px; margin: auto; margin-top: 50px; padding: 30px; border-radius: 10px; 
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); background-color: #ffffff; }
        .btn-custom { background-color: #26469C; color: white; transition: 0.3s; }
        .btn-custom:hover { background-color: #1e3a8a; }
        .form-control:focus { border-color: #26469C; box-shadow: 0 0 0 0.2rem rgba(38, 70, 156, 0.25); }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h3 class="text-center text-primary"><i class="fa-solid fa-briefcase"></i> Post a Job</h3>
        <hr>

        <?php if ($success_message): ?>
            <div class="alert alert-success"><?= $success_message ?></div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-file-signature"></i> Job Title:</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-align-left"></i> Description:</label>
                <textarea name="description" class="form-control" rows="4" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-graduation-cap"></i> Eligibility Criteria (Optional):</label>
                <input type="text" name="eligibility_criteria" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> Salary Package (LPA):</label>
                <input type="number" step="0.01" name="package" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-location-dot"></i> Location:</label>
                <input type="text" name="location" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-list-check"></i> Selection Process:</label>
                <textarea name="selection_process" class="form-control" rows="2" required></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fa-solid fa-calendar-days"></i> Campus Drive Date:</label>
                <input type="date" name="campus_drive_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-paper-plane"></i> Post Job</button>
        </form>
    </div>
</div>

</body>
</html>
