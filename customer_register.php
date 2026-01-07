<?php
include 'db_config.php';
$registration_success = false;
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        // 1. Insert into users table for authentication
        $stmtUser = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
        $stmtUser->execute([$name, $email, $phone, $pass]);
        
        // 2. Insert into clients table so they appear in Dashboard stats
        $nameParts = explode(' ', $name, 2);
        $fName = $nameParts[0];
        $lName = isset($nameParts[1]) ? $nameParts[1] : '';
        
        $stmtClient = $pdo->prepare("INSERT INTO clients (first_name, last_name, phone, email) VALUES (?, ?, ?, ?)");
        $stmtClient->execute([$fName, $lName, $phone, $email]);

        $pdo->commit();
        $registration_success = true;
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = "Registration failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Customer Registration | SalonOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-rose-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-rose-100">
        <?php if ($registration_success): ?>
            <div class="text-center py-6">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6"><i class="fa fa-check text-4xl"></i></div>
                <h2 class="text-2xl font-bold mb-2">Registration Successful!</h2>
                <p class="text-gray-500 mb-8">You are now registered as a client. Log in to book your session.</p>
                <a href="customer_login.php" class="block w-full bg-rose-600 text-white py-4 rounded-xl font-black text-center shadow-lg hover:bg-rose-700 transition">GO TO LOGIN PAGE</a>
            </div>
        <?php else: ?>
            <h2 class="text-3xl font-black text-rose-600 mb-6 text-center uppercase tracking-tighter">Join SalonOS</h2>
            <?php if($msg) echo "<p class='text-red-500 text-sm mb-4'>$msg</p>"; ?>
            <form method="POST" class="space-y-4">
                <input type="text" name="name" placeholder="Full Name" class="w-full bg-gray-50 p-3 rounded-xl outline-none focus:ring-2 focus:ring-rose-400" required>
                <input type="email" name="email" placeholder="Email Address" class="w-full bg-gray-50 p-3 rounded-xl outline-none focus:ring-2 focus:ring-rose-400" required>
                <input type="text" name="phone" placeholder="Phone Number" class="w-full bg-gray-50 p-3 rounded-xl outline-none focus:ring-2 focus:ring-rose-400">
                <input type="password" name="password" placeholder="Password" class="w-full bg-gray-50 p-3 rounded-xl outline-none focus:ring-2 focus:ring-rose-400" required>
                <button type="submit" class="w-full bg-rose-600 text-white py-4 rounded-xl font-black shadow-lg hover:bg-rose-700 transition">CREATE ACCOUNT</button>
            </form>
            <p class="mt-6 text-center text-sm text-gray-500">Already a client? <a href="customer_login.php" class="text-rose-600 font-bold hover:underline">Login</a></p>
        <?php endif; ?>
    </div>
</body>
</html>