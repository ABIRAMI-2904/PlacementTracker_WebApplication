<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = intval($_POST['student_id']); // Get the manually entered student ID
    $username = trim($_POST['username']);
    $password = $_POST['password']; 
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $cgpa = $_POST['cgpa'];
    $resume_link = trim($_POST['resume_link']);
    $status = $_POST['status'];
    $role = 'student';

    // Start transaction to ensure both queries execute successfully
    $conn->begin_transaction();
    try {
        // Check if the entered student_id already exists
        $checkStmt = $conn->prepare("SELECT id FROM students WHERE id = ?");
        $checkStmt->bind_param("i", $student_id);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            throw new Exception("Error: Student ID already exists. Please enter a unique ID.");
        }
        $checkStmt->close();

        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        $user_id = $stmt->insert_id; // Get the last inserted user ID
        $stmt->close();

        // Insert into students table with manually entered ID
        $stmt2 = $conn->prepare("INSERT INTO students (id, user_id, name, department, cgpa, resume_link, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iissdss", $student_id, $user_id, $name, $department, $cgpa, $resume_link, $status);
        $stmt2->execute();
        $stmt2->close();

        // Commit transaction
        $conn->commit();
        header("Location: student_management.php?success=Student added successfully");
        exit();
    } catch (Exception $e) {
        $conn->rollback(); // Rollback on failure
        echo "<div class='alert alert-danger text-center'>" . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
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
            <h3 class="text-center text-primary"><i class="fa-solid fa-user-graduate"></i> Add Student</h3>
            <hr>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-hashtag"></i> Student ID:</label>
                    <input type="number" name="student_id" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-id-card"></i> Full Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-building-columns"></i> Department:</label>
                    <input type="text" name="department" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-chart-line"></i> CGPA:</label>
                    <input type="number" step="0.01" name="cgpa" class="form-control" min="0" max="10" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-file"></i> Resume Link:</label>
                    <input type="text" name="resume_link" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-briefcase"></i> Placement Status:</label>
                    <select name="status" class="form-control" required>
                        <option value="unplaced">Unplaced</option>
                        <option value="placed">Placed</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-user-check"></i> Add Student</button>
            </form>
        </div>
    </div>
</body>
</html>
