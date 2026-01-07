<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

// HANDLE CUSTOMER BOOKING PROCESS
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_now'])) {
    $cust_id = $_SESSION['customer_id']; 
    $stylist_id = $_POST['stylist_id']; 
    $service_id = $_POST['service_id'];
    $date = $_POST['appointment_date'];

    try {
        $stmt = $pdo->prepare("INSERT INTO appointments (client_id, user_id, service_id, appointment_date, status) VALUES (NULL, ?, ?, ?, 'scheduled')");
        if ($stmt->execute([$stylist_id, $service_id, $date])) {
            
            // Get data for email
            $u_data = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
            $u_data->execute([$cust_id]);
            $user = $u_data->fetch();

            $s_data = $pdo->prepare("SELECT service_name FROM services WHERE id = ?");
            $s_data->execute([$service_id]);
            $service = $s_data->fetch();

            if ($user && $user['email']) {
                $email = escapeshellarg($user['email']);
                $name = escapeshellarg($user['name']);
                $s_name = escapeshellarg($service['service_name']);
                $f_date = escapeshellarg($date);
                shell_exec("python send_mail.py $email $name $s_name $f_date");
            }

            header("Location: customer_dashboard.php?success=1");
            exit();
        }
    } catch (PDOException $e) { $error_msg = "Booking Error: " . $e->getMessage(); }
}

$services = $pdo->query("SELECT * FROM services")->fetchAll();
$stylists = $pdo->query("SELECT id, name FROM users WHERE role IN ('stylist', 'admin')")->fetchAll();
/** * FETCH ONLY RETAIL PRODUCTS WITH STOCK */
$products = $pdo->query("SELECT * FROM inventory WHERE is_backbar = 0 AND stock_level > 0")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>My Dashboard | SalonOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 font-sans">
    <nav class="bg-white border-b-4 border-rose-500 p-4 flex justify-between items-center px-10 shadow-sm">
        <h1 class="text-xl font-black text-rose-600 tracking-tighter uppercase italic">SalonOS</h1>
        <div class="flex items-center gap-6">
            <span class="font-bold text-slate-700 underline decoration-rose-300 decoration-2">Hello, <?php echo htmlspecialchars($_SESSION['customer_name']); ?></span>
            <a href="logout.php" class="text-red-500 font-bold hover:text-red-700 transition"><i class="fa fa-sign-out-alt mr-1"></i> Logout</a>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto p-10 grid lg:grid-cols-2 gap-10">
        <section class="bg-white p-8 rounded-3xl shadow-xl border border-rose-100 h-fit">
            <h3 class="text-2xl font-black mb-6 text-rose-600 uppercase tracking-tighter italic flex items-center gap-2">
                <i class="fa fa-calendar-check"></i> Book Session
            </h3>
            
            <?php if(isset($_GET['success'])) echo "<p class='bg-green-100 text-green-600 p-4 rounded-xl mb-6 font-bold border border-green-200'><i class='fa fa-check-circle mr-2'></i> Booking Confirmed & Email Sent!</p>"; ?>
            <?php if(isset($error_msg)) echo "<p class='bg-red-100 text-red-600 p-4 rounded-xl mb-6 font-bold border border-red-200'>$error_msg</p>"; ?>

            <form method="POST" class="space-y-5">
                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-1 tracking-widest">Select Service</label>
                    <select name="service_id" class="w-full p-4 bg-slate-50 border-none rounded-xl outline-none focus:ring-2 focus:ring-rose-400 transition" required>
                        <option value="">Choose treatment...</option>
                        <?php foreach($services as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['service_name']); ?> - ₹<?php echo number_format($s['price'], 0); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-1 tracking-widest">Select Stylist</label>
                    <select name="stylist_id" class="w-full p-4 bg-slate-50 border-none rounded-xl outline-none focus:ring-2 focus:ring-rose-400 transition" required>
                        <option value="">Choose a stylist...</option>
                        <?php foreach($stylists as $st): ?>
                            <option value="<?php echo $st['id']; ?>"><?php echo htmlspecialchars($st['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-400 uppercase mb-1 tracking-widest">Date & Time</label>
                    <input type="datetime-local" name="appointment_date" class="w-full p-4 bg-slate-50 border-none rounded-xl outline-none focus:ring-2 focus:ring-rose-400 transition" required>
                </div>
                <button type="submit" name="book_now" class="w-full bg-rose-600 text-white font-black py-4 rounded-xl uppercase shadow-lg hover:bg-rose-700 transition transform active:scale-95">Confirm Booking</button>
            </form>
        </section>

        <section class="bg-white p-8 rounded-3xl shadow-xl border border-emerald-100 h-fit">
            <h3 class="text-2xl font-black mb-6 text-emerald-600 uppercase tracking-tighter italic flex items-center gap-2">
                <i class="fa fa-shopping-bag"></i> Premium Retail
            </h3>

            <?php if(isset($_GET['purchase_success'])) echo "<p class='bg-emerald-100 text-emerald-600 p-4 rounded-xl mb-6 font-bold border border-emerald-200'><i class='fa fa-shopping-cart mr-2'></i> Purchase Successful! Inventory Updated.</p>"; ?>

            <div class="space-y-4 max-h-[420px] overflow-y-auto pr-2">
                <?php if(empty($products)): ?>
                    <p class="text-slate-400 text-center py-10 font-medium italic">No products currently in stock.</p>
                <?php else: ?>
                    <?php foreach($products as $p): ?>
                    <div class="flex justify-between items-center p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:border-emerald-200 transition">
                        <div>
                            <p class="font-black text-slate-800 tracking-tight"><?php echo htmlspecialchars($p['item_name']); ?></p>
                            <p class="text-emerald-600 font-bold text-sm">₹<?php echo number_format($p['unit_cost'] * 1.4, 2); ?></p>
                            <p class="text-slate-400 text-[10px] uppercase font-bold tracking-widest mt-1"><?php echo $p['stock_level']; ?> in stock</p>
                        </div>
                        <a href="purchase.php?product_id=<?php echo $p['id']; ?>" class="bg-emerald-500 text-white px-5 py-2.5 rounded-xl font-black text-xs hover:bg-emerald-600 transition shadow-md uppercase tracking-widest">
                            Buy
                        </a>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>