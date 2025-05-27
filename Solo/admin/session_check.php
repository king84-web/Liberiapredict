<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    // Check for remember me cookie
    if (isset($_COOKIE['admin_remember'])) {
        require_once '../db_connection.php';
        
        $token = $_COOKIE['admin_remember'];
        $stmt = $conn->prepare("SELECT id, username, role FROM admin WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_role'] = $admin['role'];
        } else {
            header('Location: login.html');
            exit;
        }
        
        $conn->close();
    } else {
        header('Location: login.html');
        exit;
    }
}