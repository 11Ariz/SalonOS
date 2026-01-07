<?php
session_start();
include 'db_config.php';

if (isset($_SESSION['user_id']) && $_SESSION['role'] !== 'customer') {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role IN ('admin', 'stylist')");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit(); 
    } else {
        $error = "Invalid staff credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Staff Login | SalonOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md px-6">
        <div class="bg-white p-10 rounded-3xl shadow-2xl">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-black text-indigo-900 tracking-tighter uppercase">Salon<span class="text-indigo-500">OS</span></h1>
                <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mt-1">Staff Portal</p>
            </div>

            <?php if($error) echo "<p class='bg-red-50 text-red-500 p-3 rounded-xl text-sm mb-6 border border-red-100'>$error</p>"; ?>

            <form method="POST" class="space-y-5">
                <input type="email" name="email" placeholder="Work Email" class="w-full bg-slate-50 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition" required>
                <input type="password" name="password" placeholder="Password" class="w-full bg-slate-50 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition" required>
                <button type="submit" class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-indigo-700 transition">Login to Dashboard</button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-50 text-center">
                <p class="text-slate-500 text-sm mb-4">New employee?</p>
                <a href="register.php" class="inline-block w-full border-2 border-indigo-600 text-indigo-600 font-bold py-3 rounded-xl hover:bg-indigo-50 transition">
                    REGISTER NEW STAFF
                </a>
                <a href="index.php" class="block mt-6 text-slate-400 hover:text-slate-600 transition text-xs uppercase font-bold tracking-widest">
                    <i class="fa fa-arrow-left mr-1"></i> Back to Homepage
                </a>
            </div>
        </div>
    </div>
</body>
</html>