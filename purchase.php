<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_login.php");
    exit();
}

$product_id = $_GET['product_id'];
$stmt = $pdo->prepare("SELECT * FROM inventory WHERE id = ? AND stock_level > 0");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    die("Product unavailable or out of stock.");
}

$selling_price = $product['unit_cost'] * 1.4;
?>

<!DOCTYPE html>
<html lang="en">
<head><link rel="stylesheet" href="style.css">
    <meta charset="UTF-8">
    <title>Complete Your Purchase | SalonOS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-white min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full bg-slate-800 p-8 rounded-[2.5rem] border border-white/10 shadow-2xl">
        <h2 class="text-3xl font-black mb-6 italic uppercase tracking-tighter">Checkout</h2>
        
        <div class="bg-white/5 p-6 rounded-3xl mb-8 border border-white/5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Product</p>
            <h3 class="text-xl font-black mb-4"><?php echo htmlspecialchars($product['item_name']); ?></h3>
            <div class="flex justify-between items-end">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Total Price</p>
                    <p class="text-3xl font-black text-emerald-400">₹<?php echo number_format($selling_price, 2); ?></p>
                </div>
            </div>
        </div>

        <form action="process_purchase.php" method="POST" class="space-y-6">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <input type="hidden" name="amount" value="<?php echo $selling_price; ?>">
            
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Delivery Address</label>
                <textarea name="address" required class="w-full bg-white/5 border border-white/10 rounded-2xl p-4 text-white outline-none focus:ring-2 focus:ring-emerald-500 transition" placeholder="Enter your full address..."></textarea>
            </div>

            <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-slate-900 py-5 rounded-2xl font-black text-xl transition-all shadow-xl shadow-emerald-500/20 uppercase tracking-tighter">
                Confirm & Pay
            </button>
        </form>
    </div>
</body>
</html>