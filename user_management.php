<?php
include 'db.php'; // Include database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?> <!-- Include Sidebar -->

    <div class="content" style="margin-left: 260px; padding: 20px;">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="m-0"><i class="fa-solid fa-users"></i> User Management</h2>
                <a href="add_user.php" class="btn" style="background-color: #26469C; color: white;">
                    <i class="fa-solid fa-user-plus"></i> Add User
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Full Name</th>
                            <th>Contact</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Fetch only users that have an entry in the admin table
                        $sql = "SELECT users.id, users.username, users.role, admin.full_name, admin.contact
                                FROM users
                                INNER JOIN admin ON users.id = admin.user_id
                                WHERE users.role IN ('admin', 'placement_officer')";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['username']) ?></td>
                                    <td><?= ucfirst($row['role']) ?></td>
                                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                                    <td><?= htmlspecialchars($row['contact']) ?></td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <a href="edit_user.php?id=<?= $row['id'] ?>" class="btn btn-outline-warning btn-sm d-inline-flex align-items-center">
                                            <i class="fa-solid fa-edit"></i> Edit
                                        </a>
                                        <a href="delete_user.php?id=<?= $row['id'] ?>" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center" onclick="return confirm('Are you sure?')">
                                            <i class="fa-solid fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No Admins or Placement Officers found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
