<?php
session_start();
include 'db.php'; // Database connection

// Ensure the user is logged in as a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student_id and details
$student_query = "SELECT id, name, department, cgpa, resume_link, status FROM students WHERE user_id = $user_id";
$student_result = $conn->query($student_query);

if ($student_result->num_rows > 0) {
    $student_row = $student_result->fetch_assoc();
    $student_id = $student_row['id'];
    $student_name = $student_row['name'];
    $cgpa = $student_row['cgpa'];
} else {
    die("Error: Student profile not found.");
}

// Fetch job statistics
$total_jobs = $conn->query("SELECT COUNT(*) AS count FROM jobs 
                            JOIN job_approvals ON jobs.id = job_approvals.job_id 
                            WHERE jobs.status = 'Approved' 
                            AND job_approvals.visibility_date <= CURDATE()
                            AND job_approvals.deadline >= CURDATE()")->fetch_assoc()['count'];

$applied_jobs = $conn->query("SELECT COUNT(*) AS count FROM applications WHERE student_id = $student_id")->fetch_assoc()['count'];

$selected_jobs = $conn->query("SELECT COUNT(*) AS count FROM applications WHERE student_id = $student_id AND status = 'selected'")->fetch_assoc()['count'];

// Fetch available jobs (visible within date range)
$jobs_query = "SELECT jobs.*, companies.name AS company_name, job_approvals.visibility_date, job_approvals.deadline 
               FROM jobs 
               JOIN companies ON jobs.company_id = companies.id 
               JOIN job_approvals ON jobs.id = job_approvals.job_id
               WHERE jobs.status = 'Approved'
               AND job_approvals.visibility_date <= CURDATE()
               AND job_approvals.deadline >= CURDATE()
               ORDER BY jobs.created_at DESC";
$jobs_result = $conn->query($jobs_query);

// Fetch applications
$applications_query = "SELECT applications.*, jobs.title AS job_title, companies.name AS company_name 
                       FROM applications 
                       JOIN jobs ON applications.job_id = jobs.id
                       JOIN companies ON jobs.company_id = companies.id
                       WHERE applications.student_id = $student_id";
$applications_result = $conn->query($applications_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f7f9; }
        .card { border-radius: 10px; padding: 20px; }
        .stat-card { border-left: 5px solid #26469C; }
        .text-success { color: green; }
        .text-warning { color: orange; }
        .text-danger { color: red; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Student Dashboard</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Show Success/Error Messages -->
<div class="container mt-3">
    <?php if (isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
        <div id="alertMessage" class="alert alert-<?= isset($_SESSION['success']) ? 'success' : 'danger' ?> text-center" role="alert">
            <?= htmlspecialchars($_SESSION['success'] ?? $_SESSION['error']) ?>
        </div>
        <script>
            setTimeout(function() {
                document.getElementById("alertMessage").style.display = "none";
            }, 10000); // Hide after 10 seconds
        </script>
        <?php unset($_SESSION['success'], $_SESSION['error']); ?>
    <?php endif; ?>
</div>

<!-- Dashboard Overview -->
<div class="container mt-4">
    <h2 class="text-center text-primary">Welcome, <?= htmlspecialchars($student_name) ?></h2>

    <div class="row text-center mt-4">
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h5>Total Jobs Available</h5>
                <p class="fs-4"><?= $total_jobs ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h5>Jobs Applied</h5>
                <p class="fs-4 text-success"><?= $applied_jobs ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-card p-3">
                <h5>Selected Jobs</h5>
                <p class="fs-4 text-warning"><?= $selected_jobs ?></p>
            </div>
        </div>
    </div>

    <!-- Available Jobs -->
    <div class="mt-5">
        <h4>Available Jobs</h4>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Company</th>
                    <th>Job Title</th>
                    <th>Location</th>
                    <th>Salary (LPA)</th>
                    <th>Eligibility</th>
                    <th>Selection Process</th>
                    <th>Deadline</th>
                    <th>Apply</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($jobs_result->num_rows > 0) { 
                    $count = 1;
                    while ($job = $jobs_result->fetch_assoc()) { 
                        $job_id = $job['id'];
                ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($job['company_name']) ?></td>
                            <td><?= htmlspecialchars($job['title']) ?></td>
                            <td><?= htmlspecialchars($job['location']) ?></td>
                            <td><?= htmlspecialchars($job['package']) ?></td>
                            <td><?= htmlspecialchars($job['eligibility_criteria']) ?></td>
                            <td><?= htmlspecialchars($job['selection_process']) ?></td>
                            <td><?= htmlspecialchars($job['deadline']) ?></td>
                            <td>
                                <?php if ($job['deadline'] >= date('Y-m-d')): ?>
                                    <a href="apply_job.php?id=<?= $job_id ?>" class="btn btn-primary btn-sm">Apply</a>
                                <?php else: ?>
                                    <button class="btn btn-secondary btn-sm" disabled>Closed</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                <?php } } else { ?>
                    <tr><td colspan="9" class="text-center text-muted">No jobs available.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Application Tracking -->
    <div class="mt-5">
        <h4>Your Applications</h4>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Company</th>
                    <th>Job Title</th>
                    <th>Applied On</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($applications_result->num_rows > 0) { 
                    $count = 1;
                    while ($application = $applications_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($application['company_name']) ?></td>
                            <td><?= htmlspecialchars($application['job_title']) ?></td>
                            <td><?= htmlspecialchars($application['applied_at']) ?></td>
                            <td><?= htmlspecialchars($application['status']) ?></td>
                        </tr>
                <?php } } else { ?>
                    <tr><td colspan="5" class="text-center text-muted">No applications submitted.</td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
