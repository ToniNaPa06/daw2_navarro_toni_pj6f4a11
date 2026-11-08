<?php
$searchId = trim($_GET['order_id'] ?? '');
$ordersFile = '../../orders/orders.json';

if (file_exists($ordersFile)) {
    $jsonContent = file_get_contents($ordersFile);
    $orders = json_decode($jsonContent, true) ?? [];
    
    $foundOrder = null;
    foreach ($orders as $ord) {
        if ($ord['order_id'] === $searchId) {
            $foundOrder = $ord;
            break;
        }
    }

    if ($foundOrder) {
        http_response_code(200);
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Order Details</title><link rel='stylesheet' href='../client/style.css'></head><body>";
        echo "<div class='container' style='max-width: 600px; text-align: left;'>";
        echo "<h1>Order Details: " . htmlspecialchars($foundOrder['order_id']) . "</h1>";
        echo "<p><strong>Client:</strong> " . htmlspecialchars($foundOrder['client_name']) . "</p>";
        echo "<p><strong>Address:</strong> " . htmlspecialchars($foundOrder['client_address']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($foundOrder['client_email']) . "</p>";
        echo "<p><strong>Phone:</strong> " . htmlspecialchars($foundOrder['client_phone']) . "</p>";
        
        echo "<h3>Selected Products:</h3><ul>";
        foreach ($foundOrder['products'] as $p) {
            echo "<li>" . htmlspecialchars($p['name']) . " - Qty: " . $p['quantity'] . " - " . number_format($p['total'], 2) . " €</li>";
        }
        echo "</ul>";

        echo "<p><strong>Subtotal:</strong> " . number_format($foundOrder['subtotal'], 2) . " €</p>";
        echo "<p><strong>VAT (21%):</strong> " . number_format($foundOrder['vat'], 2) . " €</p>";
        echo "<p><strong>Total with VAT:</strong> <strong>" . number_format($foundOrder['total_with_vat'], 2) . " €</strong></p>";

        echo "<br><a href='../client/view_orders.html' class='btn'>Back to View Orders</a>";
        echo "</div></body></html>";
    } else {
        // Error 404 si no es troba la comanda específica (Requisit de la rúbrica)
        http_response_code(404);
        echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Order Not Found</title><link rel='stylesheet' href='../client/style.css'></head><body>";
        echo "<div class='container'><h1>Error 404</h1><p>Order with ID '$searchId' was not found.</p>";
        echo "<br><a href='../client/view_orders.html' class='btn'>Back to View Orders</a>";
        echo "</div></body></html>";
    }
} else {
    http_response_code(404);
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Error 404</title><link rel='stylesheet' href='../client/style.css'></head><body>";
    echo "<div class='container'><h1>Error 404</h1><p>Orders file not found.</p>";
    echo "<br><a href='../client/view_orders.html' class='btn'>Back to View Orders</a>";
    echo "</div></body></html>";
}
?>