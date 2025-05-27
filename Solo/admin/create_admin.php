<?php

require_once '../db_connection.php';

$username = 'admin';
$email = 'admin@shophub.com';
$password = 'admin123';
$role = 'super_admin';

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admin (username, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

if ($stmt->execute()) {
    echo "Admin account created successfully!";
} else {
    echo "Error creating admin account: " . $conn->error;
}

$conn->close();