<?php
session_start();
require_once '../db_connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get and sanitize input
$email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

// Validate input
if (empty($email) || empty($password)) {
    echo json_encode(['error' => 'Please fill in all fields']);
    exit;
}

try {
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    $admin = $result->fetch_assoc();

    // Verify password
    if (!password_verify($password, $admin['password'])) {
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    // Update last login time
    $updateStmt = $conn->prepare("UPDATE admin SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    $updateStmt->bind_param("i", $admin['id']);
    $updateStmt->execute();

    // Set session variables
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    $_SESSION['admin_role'] = $admin['role'];

    // Set remember me cookie if requested (30 days expiration)
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('admin_remember', $token, time() + (86400 * 30), '/', '', true, true);
        
        // Store token in database (you might want to create a separate table for this)
        $tokenStmt = $conn->prepare("UPDATE admin SET remember_token = ? WHERE id = ?");
        $tokenStmt->bind_param("si", $token, $admin['id']);
        $tokenStmt->execute();
    }

    echo json_encode([
        'success' => true,
        'redirect' => 'admin_dashboard.html',
        'admin' => [
            'username' => $admin['username'],
            'role' => $admin['role']
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error occurred']);
}

$conn->close();