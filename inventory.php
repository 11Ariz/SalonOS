<?php
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
include 'db_config.php';

// Handle Stock Update
if (isset($_POST['update_stock'])) {
    $stmt = $pdo->prepare("UPDATE inventory SET stock_level = ? WHERE id = ?");
    $stmt->execute([$_POST['stock_level'], $_POST['item_id']]);
}

// Handle Add Item
if (isset($_POST['add_item'])) {
    $is_backbar = isset($_POST['is_backbar']) ? 1 : 0;
    $stmt = $pdo->prepare("INSERT INTO inventory (item_name, stock_level, min_threshold, unit_cost, is_backbar) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['stock'], $_POST['min'], $_POST['cost'], $is_backbar]);
}

$inventory = $pdo->query("SELECT * FROM inventory ORDER BY is_backbar DESC, item_name ASC")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css">
    <title>Inventory Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Inventory & Backbar</h2>
            <a href="dashboard.php" class="text-indigo-600 font-medium hover:underline">← Dashboard</a>
        </div>

        <form method="POST" class="bg-white p-6 rounded-lg shadow-sm mb-8 grid grid-cols-1 md:grid-cols-5 gap-4">
            <input type="text" name="name" placeholder="Item Name" class="border p-2 rounded" required>
            <input type="number" name="stock" placeholder="Initial Stock" class="border p-2 rounded" required>
            <input type="number" name="min" placeholder="Low Threshold" class="border p-2 rounded" required>
            <input type="number" step="0.01" name="cost" placeholder="Unit Cost" class="border p-2 rounded" required>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_backbar" id="bb"> <label for="bb">Backbar?</label>
                <button type="submit" name="add_item" class="bg-indigo-600 text-white px-4 py-2 rounded ml-auto">Add</button>
            </div>
        </form>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="p-4">Item</th>
                        <th class="p-4">Category</th>
                        <th class="p-4">Stock</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($inventory as $item): 
                        $isLow = $item['stock_level'] <= $item['min_threshold'];
                    ?>
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-4 font-medium"><?php echo htmlspecialchars($item['item_name']); ?></td>
                        <td class="p-4 text-sm text-gray-500"><?php echo $item['is_backbar'] ? 'Backbar' : 'Retail'; ?></td>
                        <td class="p-4 <?php echo $isLow ? 'text-red-600 font-bold' : ''; ?>">
                            <?php echo $item['stock_level']; ?>
                        </td>
                        <td class="p-4">
                            <?php if($isLow): ?>
                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">REORDER</span>
                            <?php else: ?>
                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">IN STOCK</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <form method="POST" class="flex gap-2">
                                <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                <input type="number" name="stock_level" value="<?php echo $item['stock_level']; ?>" class="w-20 border rounded p-1">
                                <button type="submit" name="update_stock" class="text-indigo-600 font-bold text-sm">Save</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>