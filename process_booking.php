<?php
session_start();
include 'db_config.php';

// Auth Check: Ensure the user is logged in as staff
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access."); 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $client_id = $_POST['client_id'];
    $stylist_id = $_POST['stylist_id'];
    $service_id = $_POST['service_id'];
    $date = $_POST['date'];

    // 1. Insert the booking into the database
    $stmt = $pdo->prepare("INSERT INTO appointments (client_id, user_id, service_id, appointment_date, status) VALUES (?, ?, ?, ?, 'scheduled')");
    
    if ($stmt->execute([$client_id, $stylist_id, $service_id, $date])) {
        
        // 2. Fetch data for the Python Emailer
        $c_stmt = $pdo->prepare("SELECT first_name, last_name, email FROM clients WHERE id = ?");
        $c_stmt->execute([$client_id]);
        $client = $c_stmt->fetch();

        $s_stmt = $pdo->prepare("SELECT service_name FROM services WHERE id = ?");
        $s_stmt->execute([$service_id]);
        $service = $s_stmt->fetch();

        // 3. Trigger Python script for takeupfunky@gmail.com
        if ($client && !empty($client['email'])) {
            $email = escapeshellarg($client['email']);
            $name = escapeshellarg($client['first_name'] . ' ' . $client['last_name']);
            $s_name = escapeshellarg($service['service_name']);
            $f_date = escapeshellarg($date);
            
            // Runs your Python script with the App Password "nbtg nkmh wbto okcl"
            shell_exec("python send_mail.py $email $name $s_name $f_date");
        }

        header("Location: booking_system.php?success=1");
        exit();
    }
}
?>