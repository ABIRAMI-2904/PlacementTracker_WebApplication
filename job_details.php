<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'company') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$job_id = intval($_GET['id']);

// Fetch job details
$job_query = "SELECT * FROM jobs WHERE id = $job_id";
$job_result = $conn->query($job_query);
if ($job_result->num_rows === 0) {
    die("Job not found.");
}
$job = $job_result->fetch_assoc();

// Fetch applicants with email
$applicants_query = "
    SELECT applications.id AS application_id, students.id AS student_id, students.name, 
           users.username AS email, students.resume_link, applications.status 
    FROM applications 
    JOIN students ON applications.student_id = students.id 
    JOIN users ON students.user_id = users.id
    WHERE applications.job_id = $job_id";
$applicants_result = $conn->query($applicants_query);

// Update application statuses
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['post_results'])) {
    foreach ($_POST['status'] as $application_id => $new_status) {
        $new_status = $conn->real_escape_string($new_status);
        $application_id = intval($application_id);

        // Get the current status from the database
        $current_status_query = "SELECT status FROM applications WHERE id = $application_id";
        $current_status_result = $conn->query($current_status_query);

        if ($current_status_result->num_rows > 0) {
            $current_status_row = $current_status_result->fetch_assoc();
            $current_status = $current_status_row['status'];

            // Only update if the new status is different from the current status
            if ($new_status !== $current_status) {
                $update_query = "UPDATE applications 
                                 SET status = '$new_status', status_updated_at = NOW() 
                                 WHERE id = $application_id";
                $conn->query($update_query);

                // If status is "selected", mark student as "placed"
                if ($new_status === 'selected') {
                    $update_student_status = "UPDATE students SET status = 'placed' WHERE id = 
                        (SELECT student_id FROM applications WHERE id = $application_id)";
                    $conn->query($update_student_status);
                }
            }
        }
    }
    $_SESSION['success'] = "Application statuses updated successfully!";
    header("Location: job_details.php?id=$job_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        body { background-color: #f4f7f9; font-family: 'Arial', sans-serif; }
        .navbar { background-color: #26469C; }
        .navbar-brand { font-weight: bold; color: #fff; }
        .card { border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; }
        .table thead { background-color: #26469C; color: white; }
        .btn-primary { background-color: #26469C; border-color: #26469C; }
        .btn-primary:hover { background-color: #1a357c; border-color: #1a357c; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="company_dashboard.php">Back to Dashboard</a>
    </div>
</nav>

<!-- Job Details -->
<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <h2 class="text-primary text-center"><?= htmlspecialchars($job['title']) ?></h2>
                <hr>
                <p><strong>Salary:</strong> <span class="text-success"><?= htmlspecialchars($job['package']) ?> LPA</span></p>
                <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
                <p><strong>Eligibility:</strong> <?= htmlspecialchars($job['eligibility_criteria']) ?></p>
                <p><strong>Selection Process:</strong> <?= htmlspecialchars($job['selection_process']) ?></p>
                <p><strong>Campus Drive Date:</strong> <?= htmlspecialchars($job['campus_drive_date']) ?></p>
            </div>

            <!-- Search Bar -->
            <div class="mt-4">
                <input type="text" id="search" class="form-control" placeholder="Search applicants by name or email...">
            </div>

            <!-- Applicants List -->
            <div class="card mt-4">
                <h4 class="text-primary">Applicants List</h4>
                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <?php } ?>
                <form method="POST">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Resume</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="applicantTable">
                            <?php while ($applicant = $applicants_result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= htmlspecialchars($applicant['name']) ?></td>
                                    <td><?= htmlspecialchars($applicant['email']) ?></td>
                                    <td><a href="<?= htmlspecialchars($applicant['resume_link']) ?>" class="btn btn-primary btn-sm" target="_blank">View</a></td>
                                    <td>
                                        <select name="status[<?= $applicant['application_id'] ?>]" class="form-select">
                                            <option value="applied" <?= ($applicant['status'] == 'applied') ? 'selected' : '' ?>>Applied</option>
                                            <option value="shortlisted" <?= ($applicant['status'] == 'shortlisted') ? 'selected' : '' ?>>Shortlisted</option>
                                            <option value="rejected" <?= ($applicant['status'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                                            <option value="selected" <?= ($applicant['status'] == 'selected') ? 'selected' : '' ?>>Selected</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if ($applicants_result->num_rows === 0) { ?>
                                <tr><td colspan="4" class="text-center text-muted">No applicants yet.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <button type="submit" name="post_results" class="btn btn-success mt-3">Post Results</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Search functionality
    $(document).ready(function () {
        $("#search").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#applicantTable tr").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    });
</script>

</body>
</html>
