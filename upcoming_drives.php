<?php
include 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Placement Drives</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eef1f7;
            color: #333;
        }
        .content {
            margin-left: 250px;
            padding: 30px;
        }
        .table-container {
            background: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0px 3px 10px rgba(0, 0, 0, 0.15);
        }
        .table thead {
            background-color: #26469C;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: #f7f8fc;
            cursor: pointer;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .modal-content {
            border-radius: 12px;
        }
        .modal-header {
            background-color: #26469C;
            color: white;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .modal-body {
            padding: 25px;
        }
        .modal-body h5 {
            font-size: 18px;
            font-weight: 600;
            margin-top: 15px;
            color: #26469C;
        }
        .table-bordered th, .table-bordered td {
            text-align: center;
            vertical-align: middle;
        }
        .no-data {
            text-align: center;
            color: #888;
            font-size: 18px;
            padding: 20px;
        }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?> <!-- Include Sidebar -->

    <div class="content">
        <h2 class="mb-4"><i class="fa-solid fa-calendar-check"></i> Upcoming Placement Drives</h2>

        <div class="table-container">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Package (LPA)</th>
                        <th>Drive Date</th>
                        <th>Recruitment Mode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "
                        SELECT jobs.id, jobs.title, companies.name AS company_name, jobs.package, jobs.campus_drive_date, job_approvals.recruitment_mode
                        FROM jobs
                        JOIN job_approvals ON jobs.id = job_approvals.job_id
                        JOIN companies ON jobs.company_id = companies.id
                        WHERE jobs.status = 'Approved'
                        ORDER BY jobs.campus_drive_date ASC";

                    $result = $conn->query($query);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr data-job-id='" . $row['id'] . "' data-bs-toggle='modal' data-bs-target='#applicantsModal'>
                                    <td>" . htmlspecialchars($row['title']) . "</td>
                                    <td>" . htmlspecialchars($row['company_name']) . "</td>
                                    <td><strong>" . htmlspecialchars($row['package']) . " LPA</strong></td>
                                    <td>" . htmlspecialchars($row['campus_drive_date']) . "</td>
                                    <td>" . htmlspecialchars($row['recruitment_mode']) . "</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='no-data'>No upcoming drives available.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal for Job & Applicants Details -->
    <div class="modal fade" id="applicantsModal" tabindex="-1" aria-labelledby="applicantsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-briefcase"></i> Job Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h4 id="jobTitle"></h4>
                    <p><strong>Description:</strong> <span id="jobDescription"></span></p>
                    <p><strong>Eligibility:</strong> <span id="jobEligibility"></span></p>
                    <p><strong>Package:</strong> <span id="jobPackage"></span> LPA</p>
                    <p><strong>Location:</strong> <span id="jobLocation"></span></p>
                    <p><strong>Selection Process:</strong> <span id="jobSelectionProcess"></span></p>
                    <p><strong>Drive Date:</strong> <span id="jobDriveDate"></span></p>

                    <hr>

                    <h5><i class="fa-solid fa-building"></i> Company Details</h5>
                    <p><strong>Name:</strong> <span id="companyName"></span></p>
                    <p><strong>Industry:</strong> <span id="companyIndustry"></span></p>
                    <p><strong>Website:</strong> <span id="companyWebsite"></span></p>

                    <hr>

                    <h5><i class="fa-solid fa-users"></i> Applicants</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Resume</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="applicantsList">
                            <tr><td colspan="4" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Fetch Applicants Data -->
    <script>
        $(document).ready(function () {
            $(".table tbody").on("click", "tr", function () {
                var jobId = $(this).data("job-id");
                if (jobId) {
                    fetchApplicants(jobId);
                }
            });
        });

        function fetchApplicants(jobId) {
            $.ajax({
                url: 'fetch_applicants.php',
                type: 'POST',
                data: { job_id: jobId },
                dataType: 'json',
                success: function (response) {
                    $('#jobTitle').text(response.job.title);
                    $('#jobDescription').text(response.job.description);
                    $('#jobEligibility').text(response.job.eligibility_criteria);
                    $('#jobPackage').text(response.job.package);
                    $('#jobLocation').text(response.job.location);
                    $('#jobSelectionProcess').text(response.job.selection_process);
                    $('#jobDriveDate').text(response.job.campus_drive_date);
                    
                    $('#companyName').text(response.job.company_name);
                    $('#companyIndustry').text(response.job.industry);
                    $('#companyWebsite').html(`<a href="${response.job.website}" target="_blank">${response.job.website}</a>`);

                    let applicantsHTML = response.applicants.length > 0 
                        ? response.applicants.map(applicant => `<tr><td>${applicant.name}</td><td>${applicant.email}</td><td><a href="${applicant.resume_link}" target="_blank">View Resume</a></td><td>${applicant.status}</td></tr>`).join('')
                        : '<tr><td colspan="4" class="text-center">No applicants yet.</td></tr>';

                    $('#applicantsList').html(applicantsHTML);
                }
            });
        }
    </script>

</body>
</html>
