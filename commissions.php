<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_config.php';

// Filter by Stylist if not Admin
$user_id = $_SESSION['user_id'];
$isAdmin = $_SESSION['role'] === 'admin';

$query = "SELECT c.*, a.appointment_date, s.service_name, u.name as stylist_name 
          FROM commissions c 
          JOIN appointments a ON c.appointment_id = a.id 
          JOIN services s ON a.service_id = s.id 
          JOIN users u ON c.stylist_id = u.id";

if (!$isAdmin) {
    $query .= " WHERE c.stylist_id = " . intval($user_id);
}

$commissions = $pdo->query($query . " ORDER BY a.appointment_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Staff Commissions</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Earnings Dashboard (20% Commission)</h2>
            <a href="index.php" class="text-indigo-600 font-medium">← Dashboard</a>
        </div>

        <div class="bg-white rounded-xl shadow-md p-6">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-gray-400 border-b">
                        <th class="pb-3">Date</th>
                        <?php if($isAdmin): ?><th class="pb-3">Stylist</th><?php endif; ?>
                        <th class="pb-3">Service</th>
                        <th class="pb-3">Commission Earned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach($commissions as $row): 
                        $total += $row['amount'];
                    ?>
                    <tr class="border-b">
                        <td class="py-4"><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                        <?php if($isAdmin): ?><td class="py-4"><?php echo $row['stylist_name']; ?></td><?php endif; ?>
                        <td class="py-4"><?php echo $row['service_name']; ?></td>
                        <td class="py-4 font-bold text-green-600">$<?php echo number_format($row['amount'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="text-xl font-black">
                        <td colspan="<?php echo $isAdmin ? 3 : 2; ?>" class="pt-6">Total Payout:</td>
                        <td class="pt-6 text-green-700">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</body>
</html>