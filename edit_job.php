<?php
session_start();
include 'db.php';

// Ensure the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = $success_message = "";

// Fetch company_id
$company_query = "SELECT id FROM companies WHERE user_id = $user_id";
$company_result = $conn->query($company_query);

if ($company_result->num_rows > 0) {
    $company_row = $company_result->fetch_assoc();
    $company_id = $company_row['id'];
} else {
    die("Error: Company profile not found.");
}

// Ensure job ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid job ID.");
}

$job_id = intval($_GET['id']);

// Fetch job details
$job_query = "SELECT * FROM jobs WHERE id = $job_id AND company_id = $company_id";
$job_result = $conn->query($job_query);

if ($job_result->num_rows > 0) {
    $job = $job_result->fetch_assoc();
} else {
    die("Error: Job not found or unauthorized access.");
}

// Handle job update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $eligibility_criteria = $conn->real_escape_string($_POST['eligibility_criteria']);
    $package = (float) $_POST['package'];
    $location = $conn->real_escape_string($_POST['location']);
    $selection_process = $conn->real_escape_string($_POST['selection_process']);
    $campus_drive_date = $conn->real_escape_string($_POST['campus_drive_date']);
    $status = $conn->real_escape_string($_POST['status']);

    $update_query = "UPDATE jobs 
                     SET title = '$title', description = '$description', 
                         eligibility_criteria = '$eligibility_criteria', package = $package, 
                         location = '$location', selection_process = '$selection_process', 
                         campus_drive_date = '$campus_drive_date', status = '$status'
                     WHERE id = $job_id AND company_id = $company_id";

    if ($conn->query($update_query)) {
        $_SESSION['success_message'] = "Job updated successfully!";
        header("Location: company_dashboard.php");
        exit();
    } else {
        $error_message = "Error updating job: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Job</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="company_dashboard.php">Company Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="post_job.php">Post Job</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container mt-5">
    <h2 class="text-center">Edit Job</h2>

    <?php if ($error_message) echo "<div class='alert alert-danger'>$error_message</div>"; ?>
    <?php if ($success_message) echo "<div class='alert alert-success'>$success_message</div>"; ?>

    <div class="card shadow-sm p-4 mt-4">
        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Job Title</label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($job['title']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Job Description</label>
                <textarea name="description" class="form-control" rows="4" required><?= htmlspecialchars($job['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Eligibility Criteria</label>
                <input type="text" name="eligibility_criteria" class="form-control" required value="<?= htmlspecialchars($job['eligibility_criteria']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Salary (LPA)</label>
                <input type="number" step="0.1" name="package" class="form-control" required value="<?= htmlspecialchars($job['package']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Job Location</label>
                <input type="text" name="location" class="form-control" required value="<?= htmlspecialchars($job['location']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Selection Process</label>
                <input type="text" name="selection_process" class="form-control" required value="<?= htmlspecialchars($job['selection_process']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Campus Drive Date</label>
                <input type="date" name="campus_drive_date" class="form-control" required value="<?= htmlspecialchars($job['campus_drive_date']) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Job Status</label>
                <select name="status" class="form-control">
                    <option value="Pending" <?= ($job['status'] == 'Pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="Approved" <?= ($job['status'] == 'Approved') ? 'selected' : '' ?>>Approved</option>
                    <option value="Closed" <?= ($job['status'] == 'Closed') ? 'selected' : '' ?>>Closed</option>
                </select>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success">Update Job</button>
                <a href="company_dashboard.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
