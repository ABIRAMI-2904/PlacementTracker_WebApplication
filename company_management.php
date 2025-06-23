<?php
include 'db.php'; // Include database connection

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Fetch user_id before deleting the user
    $stmt = $conn->prepare("SELECT user_id FROM companies WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Delete the user (company will be deleted automatically due to ON DELETE CASCADE)
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Company and associated user deleted successfully.'); window.location.href='company_management.php';</script>";
    } else {
        echo "<script>alert('Error: Company not found.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Companies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .content {
            margin-left: 260px;
            padding: 20px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?> <!-- Include Sidebar -->

    <div class="content">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="m-0"><i class="fa-solid fa-building"></i> Manage Companies</h2>
                <a href="add_company.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Add Company
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Company Name</th>
                            <th>Industry</th>
                            <th>Website</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $sql = "SELECT id, name, industry, website, created_at FROM companies";
                        $result = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['name']) ?></td>
                                    <td><?= htmlspecialchars($row['industry'] ?: 'N/A') ?></td>
                                    <td>
                                        <?php if (!empty($row['website'])) { ?>
                                            <a href="<?= htmlspecialchars($row['website']) ?>" target="_blank">Visit</a>
                                        <?php } else {
                                            echo 'N/A';
                                        } ?>
                                    </td>
                                    <td><?= $row['created_at'] ?></td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <a href="edit_company.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning btn-sm">
                                            <i class="fa-solid fa-edit"></i> Edit
                                        </a>
                                        <a href="company_management.php?delete_id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this company?')">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No companies found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
