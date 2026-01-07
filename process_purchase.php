<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cust_id = $_SESSION['customer_id'];
    $prod_id = $_POST['product_id'];
    $address = $_POST['address'];
    $amount = $_POST['amount'];

    try {
        $pdo->beginTransaction();

        // 1. Deduct from Inventory
        $deduct = $pdo->prepare("UPDATE inventory SET stock_level = stock_level - 1 WHERE id = ? AND stock_level > 0");
        $deduct->execute([$prod_id]);

        if ($deduct->rowCount() === 0) {
            throw new Exception("Item recently went out of stock.");
        }

        // 2. Track as Revenue in Appointments (using a special 'Retail' service entry)
        // Note: You may want to create a 'Retail Sale' status or specific table for cleaner tracking.
        // For now, we add it to appointments to reflect in your existing 'Today Revenue' metric
        $sale = $pdo->prepare("INSERT INTO appointments (user_id, appointment_date, status, client_id, service_id) VALUES (?, NOW(), 'PAID', NULL, NULL)");
        $sale->execute([$cust_id]);
        
        // Assuming you have a 'sales' table for address tracking, add it here.
        // If not, we can use the 'commissions' table or a new 'orders' table.

        $pdo->commit();
        header("Location: customer_dashboard.php?purchase_success=1");
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}