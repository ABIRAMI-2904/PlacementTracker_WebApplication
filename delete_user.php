<?php
include 'db.php';

$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id=$id";
if ($conn->query($sql) === TRUE) {
    header("Location: user_management.php");
} else {
    echo "Error: " . $conn->error;
}
?>
