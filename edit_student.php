<?php
include 'db.php'; // Include database connection

// Check if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$student_id = $_GET['id'];

// Fetch student details
$query = "SELECT s.id, s.name, s.department, s.cgpa, s.resume_link, s.status, u.username 
          FROM students s JOIN users u ON s.user_id = u.id WHERE s.id = $student_id";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $department = mysqli_real_escape_string($conn, trim($_POST['department']));
    $cgpa = floatval($_POST['cgpa']);
    $resume_link = mysqli_real_escape_string($conn, trim($_POST['resume_link']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];

    // Ensure status is received
    if (!isset($_POST['status'])) {
        die("Error: Status not received.");
    }
    $status = mysqli_real_escape_string($conn, trim($_POST['status'])); // No strtolower() needed

    if (!empty($name) && !empty($department) && !empty($username)) {
        // Update student details
        $update_query = "UPDATE students 
                         SET name='$name', department='$department', cgpa=$cgpa, resume_link='$resume_link', status='$status' 
                         WHERE id=$student_id";
        if (mysqli_query($conn, $update_query)) {
            // Update username
            $update_user_query = "UPDATE users 
                                  SET username='$username' 
                                  WHERE id = (SELECT user_id FROM students WHERE id=$student_id)";
            mysqli_query($conn, $update_user_query);

            // Update password if provided
            if (!empty($password)) {
                $update_password_query = "UPDATE users 
                                          SET password='$password' 
                                          WHERE id = (SELECT user_id FROM students WHERE id=$student_id)";
                mysqli_query($conn, $update_password_query);
            }

            header("Location: student_management.php?success=Student updated successfully");
            exit();
        } else {
            $error = "Error updating student.";
        }
    } else {
        $error = "All required fields must be filled.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f7f9;
        }
        .card {
            max-width: 500px;
            margin: auto;
            margin-top: 50px;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #ffffff;
        }
        .btn-custom {
            background-color: #26469C;
            color: white;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: #1e3a8a;
        }
        .form-control:focus {
            border-color: #26469C;
            box-shadow: 0 0 0 0.2rem rgba(38, 70, 156, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h3 class="text-center text-primary"><i class="fa-solid fa-user-graduate"></i> Edit Student</h3>
            <hr>
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger text-center"> <?= htmlspecialchars($error) ?> </div>
            <?php } ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Student Name:</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-building-columns"></i> Department:</label>
                    <select name="department" class="form-control" required>
                        <option value="">-- Select Department --</option>
                        <option value="CSE" <?= ($student['department'] == 'CSE') ? 'selected' : '' ?>>CSE</option>
                        <option value="IT" <?= ($student['department'] == 'IT') ? 'selected' : '' ?>>IT</option>
                        <option value="ECE" <?= ($student['department'] == 'ECE') ? 'selected' : '' ?>>ECE</option>
                        <option value="EEE" <?= ($student['department'] == 'EEE') ? 'selected' : '' ?>>EEE</option>
                        <option value="MECH" <?= ($student['department'] == 'MECH') ? 'selected' : '' ?>>MECH</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-graduation-cap"></i> CGPA:</label>
                    <input type="number" step="0.01" name="cgpa" class="form-control" value="<?= htmlspecialchars($student['cgpa']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-file"></i> Resume Link:</label>
                    <input type="url" name="resume_link" class="form-control" value="<?= htmlspecialchars($student['resume_link']) ?>" placeholder="https://example.com/resume.pdf">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-toggle-on"></i> Status:</label>
                    <select name="status" class="form-control">
                        <option value="unplaced" <?= ($student['status'] == 'unplaced') ? 'selected' : '' ?>>Unplaced</option>
                        <option value="placed" <?= ($student['status'] == 'placed') ? 'selected' : '' ?>>Placed</option>
                    </select>
                </div>
                <hr>
                <h4 class="text-primary">Login Credentials</h4>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username (Email):</label>
                    <input type="email" name="username" class="form-control" value="<?= htmlspecialchars($student['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> New Password (Leave blank to keep current password):</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-save"></i> Save Changes</button>
                <a href="student_management.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
