<?php
$servername = "localhost";
$username = "root";  // Change if needed
$password = "afrinshah";      // Change if needed
$dbname = "placement_tracker";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
