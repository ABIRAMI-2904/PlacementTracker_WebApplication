<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password']; 
    $role = $_POST['role'];

    // Start transaction to ensure both queries execute successfully
    $conn->begin_transaction();
    try {
        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $role);
        $stmt->execute();
        $user_id = $stmt->insert_id; // Get the last inserted user ID
        $stmt->close();

        // Insert into admin table if role is admin or placement_officer
        $full_name = trim($_POST['full_name']) ?: "N/A";
        $contact = trim($_POST['contact']) ?: "N/A";

        $stmt2 = $conn->prepare("INSERT INTO admin (user_id, full_name, contact) VALUES (?, ?, ?)");
        $stmt2->bind_param("iss", $user_id, $full_name, $contact);
        $stmt2->execute();
        $stmt2->close();

        // Commit transaction
        $conn->commit();
        header("Location: user_management.php?success=User added successfully");
        exit();
    } catch (Exception $e) {
        $conn->rollback(); // Rollback on failure
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
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
            <h3 class="text-center text-primary"><i class="fa-solid fa-user-plus"></i> Add User</h3>
            <hr>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user-tag"></i> Role:</label>
                    <select name="role" class="form-control" required>
                        <option value="admin">Admin</option>
                        <option value="placement_officer">Placement Officer</option>
                    </select>
                </div>

                <div id="adminFields">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-id-card"></i> Full Name:</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-phone"></i> Contact:</label>
                        <input type="text" name="contact" class="form-control" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-user-check"></i> Add User</button>
            </form>
        </div>
    </div>
</body>
</html>
