<?php
session_start();
include 'db_config.php';

// FIX: Safely check if 'role' exists to prevent Undefined Array Key warnings 
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : 'staff';

// Auth Check: Only Staff/Admin [cite: 5]
if (!isset($_SESSION['user_id']) || $user_role == 'customer') {
    header("Location: login.php");
    exit();
}

/**
 * UPDATED MASTER QUERY:
 * This query combines the 'clients' table and the 'users' table (where role='customer')
 * to ensure all bookings and revenue are tracked regardless of how the account was created.
 */
$clients_query = $pdo->query("
    SELECT 
        combined.id,
        combined.name,
        combined.email,
        combined.phone,
        COUNT(a.id) AS total_appointments,
        SUM(CASE WHEN UPPER(a.status) IN ('COMPLETED', 'PAID') THEN s.price ELSE 0 END) AS total_spent
    FROM (
        -- Get all manual clients
        SELECT id, CONCAT(first_name, ' ', last_name) as name, email, phone, 'manual' as source FROM clients
        UNION
        -- Get all registered customers
        SELECT id, name, email, phone, 'registered' as source FROM users WHERE role = 'customer'
    ) AS combined
    LEFT JOIN appointments a ON (
        (combined.source = 'manual' AND a.client_id = combined.id) OR 
        (combined.source = 'registered' AND a.user_id = combined.id AND a.client_id IS NULL)
    )
    LEFT JOIN services s ON a.service_id = s.id
    GROUP BY combined.email -- Group by email to avoid duplicates if they exist in both tables
    ORDER BY total_spent DESC
");
$clients = $clients_query->fetchAll();

// Fetch Grand Totals for Header [cite: 10]
$total_revenue = array_sum(array_column($clients, 'total_spent'));
$total_visits = array_sum(array_column($clients, 'total_appointments'));
?>

<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Client Directory | SalonOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 p-8 font-sans">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-black text-indigo-900 uppercase tracking-tighter">Client <span class="text-indigo-500">Directory</span></h1>
                <p class="text-slate-500 font-medium">Tracking lifetime value for all salon guests.</p>
            </div>
            <a href="dashboard.php" class="bg-white border border-slate-200 px-6 py-2 rounded-xl text-indigo-600 font-bold hover:bg-slate-50 transition">← Dashboard</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-indigo-600 p-8 rounded-3xl shadow-xl shadow-indigo-100 text-white flex items-center gap-6">
                <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center text-3xl"><i class="fa fa-coins"></i></div>
                <div>
                    <p class="text-indigo-100 font-bold text-xs uppercase tracking-widest">Total Lifetime Revenue</p>
                    <h3 class="text-4xl font-black">₹<?php echo number_format($total_revenue, 2); ?></h3>
                </div>
            </div>
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-6">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl"><i class="fa fa-calendar-check"></i></div>
                <div>
                    <p class="text-slate-400 font-bold text-xs uppercase tracking-wider">Total Client Visits</p>
                    <h3 class="text-4xl font-black"><?php echo $total_visits; ?></h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-xl border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 uppercase text-xs font-black border-b border-slate-100">
                        <th class="py-5 px-8">Client Name</th>
                        <th class="py-5 px-8">Contact</th>
                        <th class="py-5 px-8 text-center">Visits</th>
                        <th class="py-5 px-8 text-right">Lifetime Spent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php if (empty($clients)): ?>
                        <tr>
                            <td colspan="4" class="py-10 text-center text-slate-400 font-medium">No clients found in the system.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($clients as $c): ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-5 px-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-2xl flex items-center justify-center font-black text-lg">
                                        <?php echo strtoupper(substr($c['name'], 0, 1)); ?>
                                    </div>
                                    <span class="font-bold text-slate-800 text-lg"><?php echo htmlspecialchars($c['name']); ?></span>
                                </div>
                            </td>
                            <td class="py-5 px-8">
                                <p class="text-slate-600 font-medium text-sm"><?php echo htmlspecialchars($c['email']); ?></p>
                                <p class="text-slate-400 text-xs mt-1"><?php echo htmlspecialchars($c['phone']); ?></p>
                            </td>
                            <td class="py-5 px-8 text-center">
                                <span class="bg-slate-100 text-slate-600 px-4 py-1.5 rounded-xl text-xs font-black">
                                    <?php echo $c['total_appointments']; ?> Visits
                                </span>
                            </td>
                            <td class="py-5 px-8 text-right">
                                <span class="text-emerald-600 font-black text-xl">
                                    ₹<?php echo number_format($c['total_spent'], 2); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>