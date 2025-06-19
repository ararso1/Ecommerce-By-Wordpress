<?php
$conn = new mysqli('localhost', 'root', '', 'tirushhr_wp411');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully!";
?>
