<?php
include 'db.php'; // Include database connection

// Check if ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$company_id = $_GET['id'];

// Fetch company details
$stmt = $conn->prepare("SELECT c.id, c.name, c.industry, c.website, u.username FROM companies c JOIN users u ON c.user_id = u.id WHERE c.id = ?");
$stmt->bind_param("i", $company_id);
$stmt->execute();
$result = $stmt->get_result();
$company = $result->fetch_assoc();

if (!$company) {
    die("Company not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $industry = trim($_POST['industry']);
    $website = trim($_POST['website']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($name) && !empty($username) && !empty($industry)) {
        $update_stmt = $conn->prepare("UPDATE companies SET name = ?, industry = ?, website = ? WHERE id = ?");
        $update_stmt->bind_param("sssi", $name, $industry, $website, $company_id);
        
        if ($update_stmt->execute()) {
            // Update username
            $update_user_stmt = $conn->prepare("UPDATE users SET username = ? WHERE id = (SELECT user_id FROM companies WHERE id = ?)");
            $update_user_stmt->bind_param("si", $username, $company_id);
            $update_user_stmt->execute();
            
            // Update password if provided
            if (!empty($password)) {
                $update_password_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = (SELECT user_id FROM companies WHERE id = ?)");
                $update_password_stmt->bind_param("si", $password, $company_id);
                $update_password_stmt->execute();
            }

            header("Location: company_management.php");
            exit();
        } else {
            $error = "Error updating company.";
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
    <title>Edit Company</title>
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
            <h3 class="text-center text-primary"><i class="fa-solid fa-building"></i> Edit Company</h3>
            <hr>
            <?php if (isset($error)) { ?>
                <div class="alert alert-danger text-center"> <?= htmlspecialchars($error) ?> </div>
            <?php } ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-building"></i> Company Name:</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($company['name']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-industry"></i> Industry:</label>
                    <select name="industry" class="form-control" required>
                        <option value="">-- Select Industry --</option>
                        <option value="IT" <?= ($company['industry'] == 'IT') ? 'selected' : '' ?>>IT</option>
                        <option value="Finance" <?= ($company['industry'] == 'Finance') ? 'selected' : '' ?>>Finance</option>
                        <option value="Manufacturing" <?= ($company['industry'] == 'Manufacturing') ? 'selected' : '' ?>>Manufacturing</option>
                        <option value="Healthcare" <?= ($company['industry'] == 'Healthcare') ? 'selected' : '' ?>>Healthcare</option>
                        <option value="Education" <?= ($company['industry'] == 'Education') ? 'selected' : '' ?>>Education</option>
                        <option value="Retail" <?= ($company['industry'] == 'Retail') ? 'selected' : '' ?>>Retail</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-globe"></i> Website:</label>
                    <input type="url" name="website" class="form-control" value="<?= htmlspecialchars($company['website']) ?>" placeholder="https://example.com">
                </div>
                <hr>
                <h4 class="text-primary">Login Credentials</h4>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-user"></i> Username (Email):</label>
                    <input type="email" name="username" class="form-control" value="<?= htmlspecialchars($company['username']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><i class="fa-solid fa-lock"></i> New Password (Leave blank to keep current password):</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <button type="submit" class="btn btn-custom w-100"><i class="fa-solid fa-save"></i> Save Changes</button>
                <a href="company_management.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
