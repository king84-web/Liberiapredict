<?php
require_once '../db_connection.php';

// Add at the top after require_once for testing
if ($conn->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $conn->connect_error]));
}

header('Content-Type: application/json');

try {
    // Check if ID was provided
    if (!isset($_POST['id'])) {
        throw new Exception('No product ID provided');
    }

    $product_id = intval($_POST['id']);

    // Begin transaction
    $conn->begin_transaction();

    // First check if product exists
    $check = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $check->bind_param("i", $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Product not found');
    }

    // Delete related records in cart table first
    $delete_cart = $conn->prepare("DELETE FROM cart WHERE product_id = ?");
    $delete_cart->bind_param("i", $product_id);
    $delete_cart->execute();

    // Delete related records in buy table
    $delete_purchases = $conn->prepare("DELETE FROM buy WHERE product_id = ?");
    $delete_purchases->bind_param("i", $product_id);
    $delete_purchases->execute();

    // Then delete the product
    $delete_product = $conn->prepare("DELETE FROM products WHERE id = ?");
    $delete_product->bind_param("i", $product_id);
    
    if (!$delete_product->execute()) {
        throw new Exception('Failed to delete product');
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true, 
        'message' => 'Product deleted successfully',
        'product_id' => $product_id
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    if ($conn->connect_error === null) {
        $conn->rollback();
    }
    echo json_encode([
        'error' => 'Error: ' . $e->getMessage()
    ]);
}

$conn->close();