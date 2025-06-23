<?php
include 'db.php';

if (isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']);

    // Fetch Job & Company Details
    $jobQuery = "
        SELECT jobs.title, jobs.description, jobs.eligibility_criteria, jobs.package, jobs.location, 
               jobs.selection_process, jobs.campus_drive_date, 
               companies.name AS company_name, companies.industry, companies.website 
        FROM jobs 
        JOIN companies ON jobs.company_id = companies.id 
        WHERE jobs.id = $job_id";
    
    $jobResult = $conn->query($jobQuery);
    
    if ($jobResult->num_rows > 0) {
        $jobDetails = $jobResult->fetch_assoc();
    } else {
        echo json_encode(["error" => "Job not found."]);
        exit;
    }

    // Fetch Applicants
    $applicantsQuery = "
        SELECT students.name, 
               users.username AS email, 
               IFNULL(students.resume_link, '#') AS resume_link, 
               IFNULL(applications.status, 'Pending') AS status 
        FROM applications 
        JOIN students ON applications.student_id = students.id 
        JOIN users ON students.user_id = users.id
        WHERE applications.job_id = $job_id";

    $applicantsResult = $conn->query($applicantsQuery);
    $applicants = [];

    while ($applicant = $applicantsResult->fetch_assoc()) {
        $applicants[] = array_map('htmlspecialchars_decode', $applicant);
    }

    // Decode HTML entities for job details
    $jobDetails = array_map('htmlspecialchars_decode', $jobDetails);

    // Return Data as JSON
    echo json_encode([
        "job" => $jobDetails,
        "applicants" => $applicants
    ]);
} else {
    echo json_encode(["error" => "Invalid Request"]);
}
?>
