<?php
session_start();
include 'db.php'; // Database connection

// Ensure the user is logged in as a company
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch company_id from the companies table
$company_query = "SELECT id FROM companies WHERE user_id = $user_id";
$company_result = $conn->query($company_query);

if ($company_result->num_rows > 0) {
    $company_row = $company_result->fetch_assoc();
    $company_id = $company_row['id'];
} else {
    die("Error: Company profile not found.");
}

// Fetch job statistics
$total_jobs = $conn->query("SELECT COUNT(*) AS count FROM jobs WHERE company_id = $company_id")->fetch_assoc()['count'];
$approved_jobs = $conn->query("SELECT COUNT(*) AS count FROM jobs WHERE company_id = $company_id AND status = 'Approved'")->fetch_assoc()['count'];
$pending_jobs = $conn->query("SELECT COUNT(*) AS count FROM jobs WHERE company_id = $company_id AND status = 'Pending'")->fetch_assoc()['count'];

// Fetch jobs posted by the company
$jobs_query = "SELECT * FROM jobs WHERE company_id = $company_id ORDER BY created_at DESC";
$jobs_result = $conn->query($jobs_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f7f9; }
        .card { border-radius: 10px; padding: 20px; }
        .stat-card { border-left: 5px solid #26469C; }
        .text-success { color: green; }
        .text-warning { color: orange; }
        tbody tr { cursor: pointer; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Company Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="post_job.php">Post Job</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Dashboard Overview -->
<div class="container mt-4">
    <h2 class="text-center text-primary">Welcome, Company Representative</h2>

    <div class="row text-center mt-4">
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h5><i class="fa-solid fa-briefcase"></i> Total Jobs</h5>
                <p class="fs-4"><?= $total_jobs ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h5><i class="fa-solid fa-check-circle text-success"></i> Approved Jobs</h5>
                <p class="fs-4 text-success"><?= $approved_jobs ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h5><i class="fa-solid fa-clock text-warning"></i> Pending Jobs</h5>
                <p class="fs-4 text-warning"><?= $pending_jobs ?></p>
            </div>
        </div>
    </div>

    <!-- Job Postings Table -->
    <div class="mt-5">
        <h4>Your Job Postings</h4>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Salary (LPA)</th>
                    <th>Status</th>
                    <th>Applications</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($jobs_result->num_rows > 0) { 
                    $count = 1;
                    while ($job = $jobs_result->fetch_assoc()) { 
                        $job_id = $job['id'];
                        $application_count = $conn->query("SELECT COUNT(*) AS count FROM applications WHERE job_id = $job_id")->fetch_assoc()['count'];
                ?>
                        <tr onclick="window.location.href='job_details.php?id=<?= $job_id ?>'">
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($job['title']) ?></td>
                            <td><?= htmlspecialchars($job['location']) ?></td>
                            <td><?= htmlspecialchars($job['package']) ?></td>
                            <td>
                                <span class="badge bg-<?= ($job['status'] == 'Approved') ? 'success' : 'warning' ?>">
                                    <?= htmlspecialchars($job['status']) ?>
                                </span>
                            </td>
                            <td><?= $application_count ?> Applicants</td>
                        </tr>
                <?php } } else { ?>
                    <tr><td colspan="6" class="text-center text-muted">No jobs posted yet.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
