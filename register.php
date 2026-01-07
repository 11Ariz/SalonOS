<?php
include 'db_config.php';
$success = false;
$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role']; // 'admin' or 'stylist'
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $pass, $role])) {
        $success = true;
        $msg = "Staff account created!";
    } else {
        $msg = "Error: Email might already be in use.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Staff Registration | SalonOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-indigo-900 flex items-center justify-center min-h-screen">
    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-md">
        <?php if ($success): ?>
            <div class="text-center">
                <h2 class="text-2xl font-bold mb-4">Welcome to the Team!</h2>
                <p class="mb-8">Your staff account has been created.</p>
                <a href="login.php" class="block w-full bg-indigo-600 text-white py-4 rounded-xl font-bold text-center">GO TO LOGIN</a>
            </div>
        <?php else: ?>
            <h2 class="text-2xl font-bold mb-6 text-indigo-900">Register New Staff</h2>
            <?php if($msg) echo "<p class='text-red-500 mb-4'>$msg</p>"; ?>
            <form method="POST" class="space-y-4">
                <input type="text" name="name" placeholder="Full Name" class="w-full bg-gray-50 p-3 rounded-xl" required>
                <input type="email" name="email" placeholder="Work Email" class="w-full bg-gray-50 p-3 rounded-xl" required>
                
                <select name="role" class="w-full bg-gray-50 p-3 rounded-xl outline-none border-r-8 border-transparent" required>
                    <option value="stylist">Role: Stylist</option>
                    <option value="admin">Role: Admin</option>
                </select>

                <input type="password" name="password" placeholder="Work Password" class="w-full bg-gray-50 p-3 rounded-xl" required>
                <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold shadow-lg">CREATE STAFF ACCOUNT</button>
            </form>
            <p class="mt-6 text-center text-sm"><a href="login.php" class="text-indigo-600 font-bold">Back to Login</a></p>
        <?php endif; ?>
    </div>
</body>
</html>