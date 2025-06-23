<?php
session_start();
include 'db.php'; // Include database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Username and password are required!";
    } else {
        // Prepare SQL statement to fetch user details
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        if (!$stmt) {
            die("Database error: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $db_username, $db_password, $role);
            $stmt->fetch();

            // Debugging log to check role value
            error_log("User Role from DB: " . var_export($role, true));

            // Direct password comparison (NO HASHING)
            if ($password === $db_password) {
                // Store user session
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $db_username;
                $_SESSION['role'] = $role;

                // Update last login timestamp
                $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("i", $id);
                $update_stmt->execute();
                $update_stmt->close();

                // Redirect based on role
                if ($role === 'student') {
                    header("Location: student_dashboard.php");
                } elseif ($role === 'placement_officer') {
                    header("Location: admin_dashboard.php");
                } elseif ($role === 'company') {
                    header("Location: company_dashboard.php");
                } elseif ($role === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    error_log("Invalid role detected: " . var_export($role, true));
                    $error = "Invalid role detected. Please contact admin.";
                }
                exit(); // Ensure no further code executes after redirection

            } else {
                $error = "Invalid username or password!";
                error_log("Failed login attempt for user: $username");
            }
        } else {
            $error = "Invalid username or password!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Placement Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&family=Lora:wght@400;600&family=Montserrat:wght@300;400;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #121212;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 6px 12px rgba(255, 255, 255, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        h2 {
            font-family: 'Montserrat', sans-serif;
            color: #3cbeff;
        }
        .form-control {
            background: #2a2a2a;
            color: #ffffff;
            border: none;
            padding: 12px;
        }
        .form-control:focus {
            background: #333;
            color: #ffffff;
            box-shadow: none;
        }
        .btn-primary {
            background: #00aaff;
            border: none;
            padding: 12px;
            width: 100%;
            font-size: 1.1rem;
            border-radius: 8px;
        }
        .btn-primary:hover {
            background: #0088cc;
        }
        .error-msg {
            color: #ff4c4c;
            font-size: 0.9rem;
            margin-top: 10px;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p class='error-msg'>$error</p>"; ?>
        <form action="" method="post">
            <div class="mb-3">
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
</body>
</html>
