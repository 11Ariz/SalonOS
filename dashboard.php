<?php
session_start();
include 'db_config.php';

// Auth Check: Safely handle missing session keys [cite: 4]
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// FIX: Set defaults to prevent Undefined Array Key warnings [cite: 4]
$user_role = $_SESSION['role'] ?? 'staff'; 
$display_name = $_SESSION['user_name'] ?? 'Staff Member';

if ($user_role == 'customer') {
    header("Location: customer_dashboard.php");
    exit();
}

/** * 1. UNIFIED TOTAL CLIENTS  */
$client_count_query = $pdo->query("
    SELECT COUNT(DISTINCT email) FROM (
        SELECT email FROM clients
        UNION
        SELECT email FROM users WHERE role = 'customer'
    ) as all_clients
");
$total_clients = $client_count_query->fetchColumn();

/** * 2. TODAY'S REVENUE [cite: 2, 4] */
$revenue_query = $pdo->query("
    SELECT SUM(s.price) 
    FROM appointments a
    JOIN services s ON a.service_id = s.id
    WHERE DATE(a.appointment_date) = CURDATE() 
    AND UPPER(a.status) IN ('COMPLETED', 'PAID')
");
$todays_revenue = $revenue_query->fetchColumn() ?: 0.00;

/** * 3. STOCK ALERTS [cite: 2, 6] */
$stock_alert_query = $pdo->query("
    SELECT COUNT(*) FROM inventory 
    WHERE stock_level <= min_threshold
");
$low_stock_count = $stock_alert_query->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Salon Analytics Dashboard | SalonOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex h-screen overflow-hidden">

    <aside class="w-64 bg-indigo-900 text-white flex flex-col p-6 shadow-2xl">
        <h1 class="text-2xl font-black mb-12 tracking-tighter uppercase italic">Salon<span class="text-indigo-400">OS</span></h1>
        <nav class="flex-1 space-y-4">
            <a href="dashboard.php" class="flex items-center gap-3 p-3 bg-indigo-800 rounded-2xl font-bold transition">
                <i class="fa fa-th-large text-indigo-300"></i> Dashboard
            </a>
            <a href="clients.php" class="flex items-center gap-3 p-3 hover:bg-indigo-800 rounded-2xl font-bold transition">
                <i class="fa fa-users text-indigo-300"></i> Clients
            </a>
            <a href="booking_system.php" class="flex items-center gap-3 p-3 hover:bg-indigo-800 rounded-2xl font-bold transition">
                <i class="fa fa-calendar-alt text-indigo-300"></i> Bookings
            </a>
            <a href="inventory.php" class="flex items-center gap-3 p-3 hover:bg-indigo-800 rounded-2xl font-bold transition">
                <i class="fa fa-box text-indigo-300"></i> Inventory
            </a>
        </nav>
        <a href="logout.php" class="flex items-center gap-3 p-3 text-rose-400 font-bold hover:bg-rose-900/20 rounded-2xl transition mt-auto">
            <i class="fa fa-power-off"></i> Logout
        </a>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto">
        <header class="flex justify-between items-end mb-12">
            <div>
                <h2 class="text-4xl font-black text-slate-800 tracking-tight italic uppercase">Welcome back, <?php echo htmlspecialchars($display_name); ?></h2>
                <p class="text-slate-500 font-medium">Here's the latest data for your salon today.</p>
            </div>
            <div class="text-right">
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-1"><?php echo date('l, F jS'); ?></p>
                <span class="bg-emerald-100 text-emerald-600 px-3 py-1 rounded-lg text-xs font-black uppercase">Active Session</span>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100 flex items-center gap-6">
                <div class="w-16 h-16 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="fa fa-users"></i></div>
                <div>
                    <p class="text-slate-400 font-black text-xs uppercase tracking-widest mb-1">Total Clients</p>
                    <h3 class="text-3xl font-black text-slate-800"><?php echo $total_clients; ?></h3>
                </div>
            </div>
<div class="bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100 flex flex-col items-center gap-4">
    <p class="text-slate-400 font-black text-xs uppercase tracking-widest">Automation</p>
    <form method="POST">
        <button name="run_reminders" class="bg-indigo-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-indigo-700 transition">
            <i class="fa fa-paper-plane mr-2"></i> Send Tomorrow's Reminders
        </button>
    </form>
</div>

<?php
if(isset($_POST['run_reminders'])) {
    // This triggers your python script to find tomorrow's bookings
    shell_exec("python cron_reminders.py");
    echo "<script>alert('Reminders have been sent to tomorrow\'s clients!');</script>";
}
?>
            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100 flex items-center gap-6">
                <div class="w-16 h-16 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl shadow-inner"><i class="fa fa-wallet"></i></div>
                <div>
                    <p class="text-slate-400 font-black text-xs uppercase tracking-widest mb-1">Today's Revenue</p>
                    <h3 class="text-3xl font-black text-slate-800">₹<?php echo number_format($todays_revenue, 2); ?></h3>
                </div>
            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-xl border border-slate-100 flex items-center gap-6">
                <div class="w-16 h-16 <?php echo $low_stock_count > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-400'; ?> rounded-2xl flex items-center justify-center text-2xl shadow-inner">
                    <i class="fa <?php echo $low_stock_count > 0 ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?>"></i>
                </div>
                <div>
                    <p class="text-slate-400 font-black text-xs uppercase tracking-widest mb-1">Stock Alerts</p>
                    <h3 class="text-3xl font-black text-slate-800"><?php echo $low_stock_count; ?> Items Low</h3>
                </div>
            </div>
        </div>

        <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-slate-100 mb-12">
            <h3 class="text-xl font-black mb-6 text-slate-800 uppercase italic">Weekly Revenue Trend</h3>
            <canvas id="revenueChart" height="100"></canvas>
        </div>

        <div class="bg-white p-10 rounded-[2.5rem] shadow-2xl shadow-indigo-100/50 border border-indigo-50 relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-2xl font-black mb-8 text-slate-800 flex items-center gap-3 uppercase tracking-tighter">
                    <i class="fa fa-robot text-indigo-600"></i> AI Analytics Engine
                </h3>
                <form action="bridge.php" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Report Type</label>
                        <select name="type" class="w-full bg-slate-50 border-none p-4 rounded-2xl font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 outline-none">
                            <option value="ServicePopularity">1. Service Popularity</option>
                            <option value="RevenueTrend">2. Daily Revenue Trend</option>
                            <option value="StylistPerformance">3. Stylist Performance</option>
                            <option value="CustomerLTV">4. Customer Lifetime Value</option>
                            <option value="InventoryHealth">5. Inventory Health Audit</option>
                            <option value="PeakBookingHours">6. Peak Booking Hours</option>
                        </select>
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-black text-slate-400 uppercase mb-2 ml-1">Start Date</label>
                        <input type="date" name="start" value="<?php echo date('Y-m-01'); ?>" class="w-full bg-slate-50 border-none p-4 rounded-2xl font-bold text-slate-600 focus:ring-2 focus:ring-indigo-500 outline-none">
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="submit" class="w-full bg-indigo-600 text-white p-4 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:-translate-y-1 transition">
                            Generate Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
    async function loadWeeklyChart() {
        try {
            const response = await fetch('bridge.php?type=ChartData');
            const data = await response.json();
            if (data.labels && data.values) {
                const ctx = document.getElementById('revenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Daily Revenue (₹)',
                            data: data.values,
                            backgroundColor: 'rgba(99, 102, 241, 0.8)',
                            borderColor: '#6366f1',
                            borderWidth: 2,
                            borderRadius: 12
                        }]
                    },
                    options: { responsive: true, scales: { y: { beginAtZero: true } } }
                });
            }
        } catch (error) { console.error("Chart failed:", error); }
    }
    window.onload = loadWeeklyChart;
    </script>
</body>
</html>