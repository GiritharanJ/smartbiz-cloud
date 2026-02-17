<?php
require_once '../config/db.php';

$db = new Database();
$pdo = $db->connect();

$id = $_GET['id'] ?? 0;

// Get customer details
$stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
$stmt->execute([$id]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

// Get customer invoices
$stmt = $pdo->prepare("SELECT invoice_number, total_amount, created_at FROM invoices WHERE customer_id = ? ORDER BY created_at DESC");
$stmt->execute([$id]);
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

$customer['invoices'] = $invoices;

header('Content-Type: application/json');
echo json_encode($customer);
?>
