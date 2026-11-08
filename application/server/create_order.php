<?php
require_once __DIR__ . '/functions.php';

$catalog = [
    "BK-001" => ["name" => "Clean Code", "price" => 32.50],
    "BK-002" => ["name" => "The Pragmatic Programmer", "price" => 39.90],
    "BK-003" => ["name" => "Designing Data-Intensive Apps", "price" => 44.00],
    "BK-004" => ["name" => "The Linux Command Line", "price" => 25.00]
];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["success" => false, "message" => "Method Not Allowed. Only POST requests are allowed."]);
    exit;
}

$orderId         = sanitize_input($_POST["order_id"] ?? "");
$customerName    = sanitize_input($_POST["customer_name"] ?? "");
$customerAddress = sanitize_input($_POST["customer_address"] ?? "");
$customerEmail   = sanitize_input($_POST["customer_email"] ?? "");
$customerPhone   = sanitize_input($_POST["customer_phone"] ?? "");

$selectedProducts = $_POST["products"] ?? [];

$subtotal = 0;
$orderItems = [];

foreach ($selectedProducts as $ref => $qty) {
    $qty = (int)$qty;
    if (isset($catalog[$ref]) && $qty > 0) {
        $price = $catalog[$ref]["price"];
        $itemTotal = $price * $qty;
        $subtotal += $itemTotal;

        $orderItems[] = [
            "ref"        => $ref,
            "name"       => $catalog[$ref]["name"],
            "unit_price" => $price,
            "quantity"   => $qty,
            "subtotal"   => $itemTotal
        ];
    } else {
        echo json_encode(["success" => false, "message" => "Could not process order."]);
        exit;
    }
}

$vatRate = 0.21;
$vatAmount = $subtotal * $vatRate;
$totalWithVat = $subtotal + $vatAmount;

$newOrder = [
    "order_id"         => $orderId,
    "customer_name"    => $customerName,
    "customer_address" => $customerAddress,
    "customer_email"   => $customerEmail,
    "customer_phone"   => $customerPhone,
    "items"            => $orderItems,
    "subtotal"         => $subtotal,
    "vat_rate"         => 0.21,
    "vat_amount"       => $vatAmount,
    "total_with_vat"   => $totalWithVat
];

$ordersFile = __DIR__ . '/../../orders/orders.json';
$allOrders = [];

if (file_exists($ordersFile)) {
    $fileData = file_get_contents($ordersFile);
    $allOrders = json_decode($fileData, true) ?? [];
    foreach ($allOrders as $orders) {
        if ($orders['order_id'] === $orderId) {
            echo json_encode(["success" => false, "message" => "This id order already exists."]);
            exit;
        }
    }
}

$allOrders[] = $newOrder;
$saved = @file_put_contents($ordersFile, json_encode($allOrders, JSON_PRETTY_PRINT));

if ($saved === false) {
    render_error_page(404, 'File Error', 'Error 404', 'Could not find or open the orders data file.');
}

echo json_encode(["success" => true, "total_with_vat" => number_format($totalWithVat, 2), "message" => "Order processed successfully.", "new_order" => $newOrder]);