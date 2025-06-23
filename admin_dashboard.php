<?php
include 'db.php'; 
session_start();

// Fetch total number of students
$students_query = "SELECT COUNT(*) AS total_students FROM students";
$students_result = $conn->query($students_query);
$total_students = $students_result->fetch_assoc()['total_students'];

// Fetch total number of partnered companies
$companies_query = "SELECT COUNT(*) AS total_companies FROM companies";
$companies_result = $conn->query($companies_query);
$total_companies = $companies_result->fetch_assoc()['total_companies'];

// Fetch upcoming drives count
$upcoming_drives_query = "SELECT COUNT(*) AS upcoming_drives FROM jobs WHERE status = 'Approved' AND campus_drive_date >= CURDATE()";
$upcoming_drives_result = $conn->query($upcoming_drives_query);
$upcoming_drives = $upcoming_drives_result->fetch_assoc()['upcoming_drives'];

// Fetch newly posted jobs (Pending Approval)
$new_jobs_query = "SELECT title, company_id FROM jobs WHERE status = 'Pending' ORDER BY created_at DESC";
$new_jobs_result = $conn->query($new_jobs_query);
$pending_jobs_count = $new_jobs_result->num_rows;

// Fetch company names for new job postings
$new_jobs = [];
while ($row = $new_jobs_result->fetch_assoc()) {
    $company_id = $row['company_id'];
    $company_query = "SELECT name FROM companies WHERE id = $company_id";
    $company_result = $conn->query($company_query);
    $company_name = $company_result->num_rows > 0 ? $company_result->fetch_assoc()['name'] : "Unknown";
    $new_jobs[] = ["title" => $row['title'], "company" => $company_name];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f4f9; }
        .content { margin-left: 260px; padding: 20px; }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            cursor: pointer;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); }
        .modal-content { border-radius: 10px; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?> 

    <div class="content">
        <h2 class="mb-4">Welcome, Admin</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="fa-solid fa-calendar-check"></i> Upcoming Drives</h5>
                    <p><?= $upcoming_drives ?> scheduled this week</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="fa-solid fa-user-graduate"></i> Total Students</h5>
                    <p><?= $total_students ?> students registered</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3">
                    <h5><i class="fa-solid fa-building"></i> Companies</h5>
                    <p><?= $total_companies ?> partnered companies</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for New Job Postings -->
    <?php if ($pending_jobs_count > 0): ?>
    <div class="modal fade" id="newJobsModal" tabindex="-1" aria-labelledby="newJobsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="newJobsModalLabel"><i class="fa-solid fa-bell"></i> New Job Postings</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php foreach ($new_jobs as $job): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialchars($job['title']) ?></strong> by <em><?= htmlspecialchars($job['company']) ?></em>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="modal-footer">
                    <a href="job_openings.php" class="btn btn-success">Review Jobs</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show the modal if there are new job postings
        window.onload = function() {
            <?php if ($pending_jobs_count > 0): ?>
                var myModal = new bootstrap.Modal(document.getElementById('newJobsModal'));
                myModal.show();
            <?php endif; ?>
        };
    </script>

</body>
</html>
