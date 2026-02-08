<?php
// save_upi.php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo "Invalid request method";
    exit;
}

$upi_id = isset($_POST['upi_id']) ? trim($_POST['upi_id']) : '';
$time   = isset($_POST['time'])   ? trim($_POST['time'])   : '';

if ($upi_id === '' || $time === '') {
    http_response_code(400); // Bad Request
    echo "Missing data";
    exit;
}

// Server-side UPI validation (same pattern as JS)
$pattern = '/^[a-zA-Z0-9.\-_]{2,256}@[a-zA-Z]{2,64}$/';
if (!preg_match($pattern, $upi_id)) {
    http_response_code(400);
    echo "Invalid UPI format on server";
    exit;
}

// Convert time to DATETIME
$checked_at = date('Y-m-d H:i:s', strtotime($time));

$ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;

try {
    $stmt = $pdo->prepare(
        "INSERT INTO upi_checks (upi_id, checked_at, ip_address, user_agent)
         VALUES (:upi_id, :checked_at, :ip_address, :user_agent)"
    );

    $stmt->execute([
        ':upi_id'    => $upi_id,
        ':checked_at'=> $checked_at,
        ':ip_address'=> $ip_address,
        ':user_agent'=> $user_agent
    ]);

    echo "Saved successfully";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Failed to save";
    // Debug (optional):
    // echo "Error: " . $e->getMessage();
}
