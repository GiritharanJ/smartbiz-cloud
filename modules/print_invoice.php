<?php
session_start();

require_once __DIR__ . '/../config/db.php';

$database = new Database();
$pdo = $database->connect();

if (!isset($_GET['id'])) {
    die("Invoice ID missing.");
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare("SELECT * FROM invoices WHERE id = ?");
$stmt->execute([$id]);
$invoice = $stmt->fetch();

if (!$invoice) {
    die("Invoice not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Print Invoice</title>
</head>
<body>
    <h2>Invoice #<?= htmlspecialchars($invoice['invoice_number']) ?></h2>
    <p>Date: <?= date('d M Y', strtotime($invoice['created_at'])) ?></p>
    <p>Total Amount: ₹<?= number_format($invoice['total_amount'], 2) ?></p>

    <br>
    <button onclick="window.print()">Print</button>
</body>
</html>

