<?php
include 'db.php';

// Count pending job approvals
$pending_jobs_query = "SELECT COUNT(*) AS pending_count FROM jobs WHERE status = 'Pending'";
$pending_jobs_result = $conn->query($pending_jobs_query);
$pending_jobs_count = $pending_jobs_result->fetch_assoc()['pending_count'];
?>

<div class="sidebar">
    <a href="admin_dashboard.php">Admin Dashboard</a>
    <a href="upcoming_drives.php">Upcoming Drives</a>
    <a href="job_openings.php">
        Job Openings
        <?php if ($pending_jobs_count > 0): ?>
            <span class="badge bg-danger ms-2"><?= $pending_jobs_count ?></span>
        <?php endif; ?>
    </a>
    <a href="student_management.php">Manage Students</a>
    <a href="company_management.php">Manage Companies</a>
    <a href="user_management.php">User Management</a>
    <a href="logout.php">Logout</a>
</div>

<style>
    .sidebar {
        height: 100vh;
        width: 250px;
        position: fixed;
        background: #212529;
        padding-top: 20px;
    }
    .sidebar a {
        padding: 15px;
        display: flex;
        align-items: center;
        color: white;
        text-decoration: none;
        transition: 0.3s;
    }
    .sidebar a:hover, .sidebar a.active {
        background: #343a40;
        border-left: 4px solid #00aaff;
    }
    .badge {
        font-size: 0.8rem;
        padding: 4px 8px;
        border-radius: 8px;
    }
</style>
