<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "buying_db";

try {
    // Create database connection using mysqli
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Connection failed: " . $e->getMessage());
}

// Set character set to utf8 (optional but recommended)
mysqli_set_charset($conn, "utf8");

// Uncomment the line below if you want to check if connection is successful
// echo "Connected successfully";
?>
