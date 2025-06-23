<?php
include 'db.php';

$id = $_GET['id'];

// Fetch user data
$sql = "SELECT users.*, admin.full_name, admin.contact FROM users 
        LEFT JOIN admin ON users.id = admin.user_id 
        WHERE users.id = $id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $full_name = trim($_POST['full_name']) ?: "N/A";
    $contact = trim($_POST['contact']) ?: "N/A";
    $password = $_POST['password'];

    // If password is provided, update it; otherwise, keep the old password
    if (!empty($password)) {
        $updateUserSQL = "UPDATE users SET username='$username', password='$password', role='$role' WHERE id=$id";
    } else {
        $updateUserSQL = "UPDATE users SET username='$username', role='$role' WHERE id=$id";
    }

    if (mysqli_query($conn, $updateUserSQL)) {
        // If role is admin or placement_officer, update or insert admin details
        if ($role == "admin" || $role == "placement_officer") {
            $checkAdminSQL = "SELECT * FROM admin WHERE user_id = $id";
            $checkAdminResult = mysqli_query($conn, $checkAdminSQL);

            if (mysqli_num_rows($checkAdminResult) > 0) {
                // Update existing admin details
                $updateAdminSQL = "UPDATE admin SET full_name='$full_name', contact='$contact' WHERE user_id=$id";
                mysqli_query($conn, $updateAdminSQL);
            } else {
                // Insert new admin record
                $insertAdminSQL = "INSERT INTO admin (user_id, full_name, contact) VALUES ($id, '$full_name', '$contact')";
                mysqli_query($conn, $insertAdminSQL);
            }
        } else {
            // Remove admin details if role is changed to something else
            mysqli_query($conn, "DELETE FROM admin WHERE user_id = $id");
        }

        header("Location: user_management.php?success=User updated successfully");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            <h3 class="text-center text-primary"><i class="fa-solid fa-user-edit"></i> Edit User</h3>
            <hr>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username:</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> New Password (Leave blank to keep old):</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user-tag"></i> Role:</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="placement_officer" <?= ($user['role'] == 'placement_officer') ? 'selected' : '' ?>>Placement Officer</option>
                    </select>
                </div>

                <div id="adminFields" style="<?= ($user['role'] == 'admin' || $user['role'] == 'placement_officer') ? '' : 'display: none;' ?>">
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-id-card"></i> Full Name:</label>
                        <input type="text" name="full_name" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><i class="fa-solid fa-phone"></i> Contact:</label>
                        <input type="text" name="contact" class="form-control" value="<?= htmlspecialchars($user['contact']) ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-user-check"></i> Update User</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById("role").addEventListener("change", function() {
            var adminFields = document.getElementById("adminFields");
            if (this.value === "admin" || this.value === "placement_officer") {
                adminFields.style.display = "block";
            } else {
                adminFields.style.display = "none";
            }
        });
    </script>
</body>
</html>
