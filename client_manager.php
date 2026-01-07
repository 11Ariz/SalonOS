<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_config.php';

// Handle Deletion
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$_GET['delete']]);
    header("Location: client_manager.php");
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("INSERT INTO clients (first_name, last_name, phone, email) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['fname'], $_POST['lname'], $_POST['phone'], $_POST['email']]);
}

$clients = $pdo->query("SELECT * FROM clients ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Client Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between mb-6">
            <h2 class="text-2xl font-bold">Client Directory</h2>
            <a href="index.php" class="text-indigo-600 underline">Back to Dashboard</a>
        </div>

        <form method="POST" class="bg-white p-6 rounded shadow-md mb-8 grid grid-cols-2 gap-4">
            <input type="text" name="fname" placeholder="First Name" class="border p-2 rounded" required>
            <input type="text" name="lname" placeholder="Last Name" class="border p-2 rounded" required>
            <input type="text" name="phone" placeholder="Phone" class="border p-2 rounded" required>
            <input type="email" name="email" placeholder="Email" class="border p-2 rounded" required>
            <button type="submit" class="col-span-2 bg-indigo-600 text-white py-2 rounded">Add Client</button>
        </form>

        <table class="w-full bg-white rounded shadow-md">
            <thead><tr class="bg-gray-200 text-left"><th class="p-3">Name</th><th class="p-3">Phone</th><th class="p-3">Actions</th></tr></thead>
            <tbody>
                <?php foreach($clients as $c): ?>
                <tr class="border-b">
                    <td class="p-3"><?php echo $c['first_name'] . " " . $c['last_name']; ?></td>
                    <td class="p-3"><?php echo $c['phone']; ?></td>
                    <td class="p-3"><a href="?delete=<?php echo $c['id']; ?>" class="text-red-500">Delete</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>