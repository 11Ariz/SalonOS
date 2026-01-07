<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['customer_id'] = $user['id'];
        $_SESSION['customer_name'] = $user['name'];
        header("Location: customer_dashboard.php");
        exit();
    } else {
        $error = "Invalid Credentials. Make sure you are registered as a Customer.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Customer Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-rose-50 flex items-center justify-center h-screen">
    <div class="bg-white p-10 rounded-3xl shadow-2xl w-96 border-t-8 border-rose-500">
        <h2 class="text-3xl font-bold text-center mb-8 text-rose-600 uppercase tracking-tighter">Customer Login</h2>
        <?php if(isset($error)) echo "<p class='text-red-500 mb-4 text-center'>$error</p>"; ?>
        <form method="POST" class="space-y-4">
            <input type="email" name="email" placeholder="Email" class="w-full p-4 bg-gray-50 rounded-xl outline-none border border-gray-100" required>
            <input type="password" name="password" placeholder="Password" class="w-full p-4 bg-gray-50 rounded-xl outline-none border border-gray-100" required>
            <button type="submit" class="w-full bg-rose-600 text-white py-4 rounded-xl font-black shadow-lg shadow-rose-200">LOGIN & BOOK</button>
        </form>
        <p class="mt-6 text-center text-sm">Need an account? <a href="customer_register.php" class="text-rose-600 font-bold">Register here</a></p>
    </div>
</body>
</html>