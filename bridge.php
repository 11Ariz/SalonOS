<?php
session_start();

// AUTH CHECK
if (!isset($_SESSION['user_id']) || $_SESSION['role'] == 'customer') {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit();
}

$basePath = __DIR__ . DIRECTORY_SEPARATOR;

// 1. DASHBOARD CHART DATA (AJAX)
if (isset($_GET['type']) && $_GET['type'] === 'ChartData') {
    $script = escapeshellarg($basePath . "chart_data.py");
    $output = shell_exec("python $script 2>&1");
    header('Content-Type: application/json');
    echo $output;
    exit();
}

// 2. PDF REPORT GENERATION
$type_input = isset($_GET['type']) ? $_GET['type'] : 'ServicePopularity';
$type = escapeshellarg($type_input);

$start = !empty($_GET['start']) ? escapeshellarg($_GET['start']) : escapeshellarg(date('Y-m-01'));
$end   = !empty($_GET['end']) ? escapeshellarg($_GET['end']) : escapeshellarg(date('Y-m-d'));

$engineScript = escapeshellarg($basePath . "analytics_engine.py");
$command = "python $engineScript $type $start $end 2>&1";
$output = shell_exec($command);

$response = json_decode($output, true);

if (json_last_error() === JSON_ERROR_NONE && isset($response['status'])) {
    if ($response['status'] === 'success') {
        $filePath = $basePath . $response['file'];
        if (file_exists($filePath)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $type_input . '_Analytics.pdf"');
            readfile($filePath);
            exit();
        }
    } else {
        show_styled_error("Engine Error: " . $response['message']);
    }
} else {
    show_styled_error("System Failure: " . $output);
}

function show_styled_error($msg) {
    echo '<link rel="stylesheet" href="style.css">';
    echo "<body style='background:#0f172a; padding:50px;'><div style='border:1px solid #f43f5e; padding:20px; color:#f43f5e; border-radius:15px; background:rgba(244,63,94,0.1); font-family:sans-serif;'>";
    echo "<strong>REPORT ERROR:</strong><br>" . htmlspecialchars($msg);
    echo "</div></body>";
}
?>