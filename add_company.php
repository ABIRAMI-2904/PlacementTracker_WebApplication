<?php
include 'db.php'; // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $industry = trim($_POST['industry']);
    $website = trim($_POST['website']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = 'company'; // Fixed role

    if (!empty($name) && !empty($username) && !empty($password) && !empty($industry)) {
        
        mysqli_begin_transaction($conn);
        
        $sql_user = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
        $stmt_user = mysqli_prepare($conn, $sql_user);
        mysqli_stmt_bind_param($stmt_user, "sss", $username, $password, $role);
        
        if (mysqli_stmt_execute($stmt_user)) {
            $user_id = mysqli_insert_id($conn);
            
            $sql_company = "INSERT INTO companies (user_id, name, industry, website) VALUES (?, ?, ?, ?)";
            $stmt_company = mysqli_prepare($conn, $sql_company);
            mysqli_stmt_bind_param($stmt_company, "isss", $user_id, $name, $industry, $website);
            
            if (mysqli_stmt_execute($stmt_company)) {
                mysqli_commit($conn);
                header("Location: company_management.php?success=Company added successfully");
                exit();
            } else {
                mysqli_rollback($conn);
                $error = "Error adding company: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt_company);
        } else {
            mysqli_rollback($conn);
            $error = "Error adding user: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_user);
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Company</title>
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
            <h3 class="text-center text-primary"><i class="fa-solid fa-building"></i> Add Company</h3>
            <hr>
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger text-center"> <?= htmlspecialchars($error) ?> </div>
            <?php } ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-building"></i> Company Name:</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-industry"></i> Industry:</label>
                    <select name="industry" class="form-control" required>
                        <option value="">-- Select Industry --</option>
                        <option value="IT">IT</option>
                        <option value="Finance">Finance</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Education">Education</option>
                        <option value="Retail">Retail</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-globe"></i> Website:</label>
                    <input type="url" name="website" class="form-control" placeholder="https://example.com">
                </div>
                <hr>
                <h4 class="text-primary">Login Credentials</h4>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username (Email):</label>
                    <input type="email" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> Password:</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-plus"></i> Add Company</button>
            </form>
        </div>
    </div>
</body>
</html>
