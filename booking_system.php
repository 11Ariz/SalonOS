<?php
session_start();
include 'db_config.php';

$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'staff';
if (!isset($_SESSION['user_id']) || $user_role == 'customer') {
    header("Location: login.php");
    exit();
}

// Handle Status Updates and Deletions
if (isset($_GET['delete_id'])) {
    $pdo->prepare("DELETE FROM appointments WHERE id = ?")->execute([$_GET['delete_id']]);
    header("Location: booking_system.php");
    exit();
}

if (isset($_GET['complete_id'])) {
    $pdo->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?")->execute([$_GET['complete_id']]);
    header("Location: booking_system.php");
    exit();
}

// Master Query: Fetches names from both 'users' and 'clients'
$all_bookings = $pdo->query("
    SELECT a.id, COALESCE(u_cust.name, CONCAT(c.first_name, ' ', c.last_name), 'Walk-in') AS client_display_name,
    COALESCE(u_staff.name, 'Unassigned') AS stylist_display_name, s.service_name, a.appointment_date, a.status 
    FROM appointments a
    LEFT JOIN users u_staff ON a.user_id = u_staff.id AND u_staff.role != 'customer'
    LEFT JOIN users u_cust ON a.user_id = u_cust.id AND u_cust.role = 'customer'
    LEFT JOIN clients c ON a.client_id = c.id
    LEFT JOIN services s ON a.service_id = s.id
    ORDER BY a.appointment_date DESC
")->fetchAll();

$clients_list = $pdo->query("SELECT id, first_name, last_name FROM clients")->fetchAll();
$stylists = $pdo->query("SELECT id, name FROM users WHERE role IN ('stylist', 'admin')")->fetchAll();
$services = $pdo->query("SELECT * FROM services")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking System | SalonOS</title>
    <link rel="stylesheet" href="style.css"> <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-slate-50 p-10 font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-black text-indigo-900 uppercase tracking-tighter italic">Booking System</h1>
            <a href="dashboard.php" class="text-indigo-600 font-bold hover:underline">← Dashboard</a>
        </div>

        <div class="grid lg:grid-cols-3 gap-10">
            <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 h-fit">
                <h2 class="text-xl font-bold mb-6 italic uppercase">Manual Entry</h2>
                <form method="POST" action="process_booking.php" class="space-y-4">
                    <select name="client_id" class="w-full bg-slate-50 p-4 rounded-2xl outline-none" required>
                        <option value="">Select Client</option>
                        <?php foreach($clients_list as $cl) echo "<option value='{$cl['id']}'>{$cl['first_name']} {$cl['last_name']}</option>"; ?>
                    </select>
                    <select name="stylist_id" class="w-full bg-slate-50 p-4 rounded-2xl outline-none" required>
                        <option value="">Assign Stylist</option>
                        <?php foreach($stylists as $st) echo "<option value='{$st['id']}'>{$st['name']}</option>"; ?>
                    </select>
                    <select name="service_id" class="w-full bg-slate-50 p-4 rounded-2xl outline-none" required>
                        <option value="">Select Service</option>
                        <?php foreach($services as $sv) echo "<option value='{$sv['id']}'>{$sv['service_name']}</option>"; ?>
                    </select>
                    <input type="datetime-local" name="date" class="w-full bg-slate-50 p-4 rounded-2xl outline-none" required>
                    <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-2xl font-black shadow-lg uppercase italic">Confirm Booking</button>
                </form>
            </div>

            <div class="lg:col-span-2 bg-white p-8 rounded-3xl shadow-xl border border-slate-100">
                <h2 class="text-xl font-bold mb-6 italic uppercase">Scheduled Appointments</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="text-slate-400 text-xs uppercase border-b border-slate-50 font-black">
                                <th class="py-4 px-2">Client Name</th>
                                <th class="py-4 px-2">Service</th>
                                <th class="py-4 px-2">Stylist</th>
                                <th class="py-4 px-2 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($all_bookings as $b): ?>
                            <tr class="border-b border-slate-50 hover:bg-slate-50 transition">
                                <td class="py-4 px-2 font-bold text-slate-700"><?php echo htmlspecialchars($b['client_display_name']); ?></td>
                                <td class="py-4 px-2 text-slate-600"><?php echo htmlspecialchars($b['service_name']); ?></td>
                                <td class="py-4 px-2 text-indigo-600 font-black italic"><?php echo htmlspecialchars($b['stylist_display_name']); ?></td>
                                <td class="py-4 px-2 flex justify-center gap-2">
                                    <?php if(strtoupper($b['status']) == 'COMPLETED' || strtoupper($b['status']) == 'PAID'): ?>
                                        <span class="text-emerald-500 font-black italic">✓ PAID</span>
                                    <?php else: ?>
                                        <a href="?complete_id=<?php echo $b['id']; ?>" class="bg-indigo-500 text-white px-3 py-1.5 rounded-xl text-xs font-black italic hover:bg-indigo-600 shadow-sm transition">Complete</a>
                                    <?php endif; ?>
                                    
                                    <a href="?delete_id=<?php echo $b['id']; ?>" onclick="return confirm('Delete this booking?')" class="bg-rose-50 text-rose-500 p-1.5 rounded-xl hover:bg-rose-500 hover:text-white transition">
                                        <i class="fa fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>