<?php
// report_upi.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Invalid request method";
    exit;
}

$upi_id = isset($_POST['upi_id']) ? trim($_POST['upi_id']) : '';
$reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';

if ($upi_id === '' || $reason === '') {
    http_response_code(400);
    echo "⚠️ UPI ID and reason are required.";
    exit;
}

$reported_at = date('Y-m-d H:i:s');
$ip_address  = $_SERVER['REMOTE_ADDR'] ?? null;
$user_agent  = $_SERVER['HTTP_USER_AGENT'] ?? null;

try {
    $stmt = $pdo->prepare(
        "INSERT INTO upi_reports (upi_id, reason, reported_at, ip_address, user_agent)
         VALUES (:upi_id, :reason, :reported_at, :ip_address, :user_agent)"
    );

    $stmt->execute([
        ':upi_id'     => $upi_id,
        ':reason'     => $reason,
        ':reported_at'=> $reported_at,
        ':ip_address' => $ip_address,
        ':user_agent' => $user_agent
    ]);

    echo "✅ Report saved successfully (logged for NTPC-style review).";
} catch (PDOException $e) {
    http_response_code(500);
    echo "❌ Failed to save report.";
    // Debug optional:
    // echo "Error: " . $e->getMessage();
}
