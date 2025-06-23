<?php
include 'db.php'; // Include database connection

// Check if a delete request was made
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Step 1: Get the associated user_id from the students table
    $stmt = $conn->prepare("SELECT user_id FROM students WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Step 2: Delete from users table (triggers cascade delete)
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            header("Location: student_management.php?success=Student deleted successfully");
            exit();
        } else {
            header("Location: student_management.php?error=Failed to delete student");
            exit();
        }
    } else {
        header("Location: student_management.php?error=Student not found");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?> <!-- Include Sidebar -->

    <div class="content" style="margin-left: 260px; padding: 20px;">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="m-0"><i class="fa-solid fa-user-graduate"></i> Student Management</h2>
                <a href="add_student.php" class="btn" style="background-color: #26469C; color: white;">
                    <i class="fa-solid fa-user-plus"></i> Add Student
                </a>
            </div>

            <?php
            if (isset($_GET['success'])) {
                echo '<div class="alert alert-success">' . htmlspecialchars($_GET['success']) . '</div>';
            } elseif (isset($_GET['error'])) {
                echo '<div class="alert alert-danger">' . htmlspecialchars($_GET['error']) . '</div>';
            }
            ?>

            <div class="table-responsive">
                <table class="table table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Username</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>CGPA</th>
                            <th>Resume</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Fetch students with their user details
                        $sql = "SELECT students.id AS student_id, users.username, students.name, students.department, students.cgpa, students.resume_link, students.status
                                FROM users
                                INNER JOIN students ON users.id = students.user_id
                                WHERE users.role = 'student'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['student_id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['department']) ?></td>
                                    <td><?= number_format($row['cgpa'], 2) ?></td>
                                    <td>
                                        <?php if (!empty($row['resume_link'])) { ?>
                                            <a href="<?= htmlspecialchars($row['resume_link']) ?>" target="_blank">View Resume</a>
                                        <?php } else {
                                            echo 'N/A';
                                        } ?>
                                    </td>
                                    <td><?= ucfirst($row['status']) ?></td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <a href="edit_student.php?id=<?= $row['student_id'] ?>" class="btn btn-outline-warning btn-sm d-inline-flex align-items-center">
                                            <i class="fa-solid fa-edit"></i> Edit
                                        </a>
                                        <a href="student_management.php?delete_id=<?= $row['student_id'] ?>" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center" 
                                           onclick="return confirm('Are you sure you want to delete this student? This action cannot be undone.');">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted">No students found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
