<?php
session_start();
include 'db.php'; // Database connection

// Ensure only placement officers or admins can access this page
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['placement_officer', 'admin'])) {
    header("Location: login.php");
    exit();
}

// Fetch all job postings pending approval
$jobs_query = "SELECT jobs.id, jobs.title, jobs.package, jobs.location, jobs.eligibility_criteria, 
                      jobs.campus_drive_date, jobs.status, companies.name AS company_name 
               FROM jobs 
               JOIN companies ON jobs.company_id = companies.id 
               WHERE jobs.status = 'Pending'";

$jobs_result = $conn->query($jobs_query);

// Error handling for query execution
if (!$jobs_result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Openings - Job Approvals</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        .content {
            margin-left: 270px; /* Adjust content to align with the sidebar */
            padding: 20px;
            width: calc(100% - 270px);
        }
    </style>
</head>
<body>

    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <div class="content">
        <h2>Job Openings - Approve Jobs</h2>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead class="table-dark">
                    <tr>
                        <th>Company</th>
                        <th>Job Title</th>
                        <th>Package</th>
                        <th>Location</th>
                        <th>Eligibility</th>
                        <th>Drive Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $jobs_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['package']) . " LPA"; ?></td>
                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                            <td><?php echo htmlspecialchars($row['eligibility_criteria']); ?></td>
                            <td><?php echo htmlspecialchars($row['campus_drive_date']); ?></td>
                            <td>
                                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal<?php echo $row['id']; ?>">Approve</button>
                                <button class="btn btn-danger btn-sm" onclick="rejectJob(<?php echo $row['id']; ?>)">Reject</button>
                            </td>
                        </tr>

                        <!-- Approve Job Modal -->
                        <div class="modal fade" id="approveModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Approve Job - <?php echo htmlspecialchars($row['title']); ?></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form action="approve_job.php" method="POST">
                                        <div class="modal-body">
                                            <input type="hidden" name="job_id" value="<?php echo $row['id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Visibility Date</label>
                                                <input type="date" name="visibility_date" class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Application Deadline</label>
                                                <input type="date" name="deadline" class="form-control" required>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Mode of Recruitment</label>
                                                <select name="recruitment_mode" class="form-control" required>
                                                    <option value="On-campus">On-campus</option>
                                                    <option value="Virtual">Virtual</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Approve</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function rejectJob(jobId) {
            if (confirm("Are you sure you want to reject this job?")) {
                window.location.href = "reject_job.php?id=" + jobId;
            }
        }
    </script>

</body>
</html>
