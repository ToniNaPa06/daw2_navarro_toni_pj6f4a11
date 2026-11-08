<?php
$ordersFile = '../../orders/orders.json';

// Comprovem si existeix el fitxer de comandes
if (file_exists($ordersFile)) {
    $jsonContent = file_get_contents($ordersFile);
    $orders = json_decode($jsonContent, true) ?? [];

    // Enviament del missatge HTTP 200 OK exigit per la rúbrica
    http_response_code(200);
    
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>All Orders</title><link rel='stylesheet' href='../client/style.css'></head><body>";
    echo "<div class='container' style='max-width: 800px; text-align: left;'>";
    echo "<h1>All Stored Orders</h1>";

    if (empty($orders)) {
        echo "<p>No orders found yet.</p>";
    } else {
        echo "<table border='1' cellpadding='8' cellspacing='0' style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>";
        echo "<tr style='background-color: #f2f2f2;'><th>Order ID</th><th>Client Name</th><th>Email</th><th>Phone</th><th>Total with VAT</th></tr>";
        
        foreach ($orders as $ord) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($ord['order_id']) . "</td>";
            echo "<td>" . htmlspecialchars($ord['client_name']) . "</td>";
            echo "<td>" . htmlspecialchars($ord['client_email']) . "</td>";
            echo "<td>" . htmlspecialchars($ord['client_phone']) . "</td>";
            echo "<td>" . number_format($ord['total_with_vat'], 2) . " €</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    echo "<br><a href='../client/view_orders.html' class='btn'>Back to View Orders</a>";
    echo "</div></body></html>";
} else {
    // Control d'errors 404 exigit per la rúbrica si no troba el fitxer
    http_response_code(404);
    echo "<!DOCTYPE html><html lang='en'><head><meta charset='UTF-8'><title>Error 404</title><link rel='stylesheet' href='../client/style.css'></head><body>";
    echo "<div class='container'><h1>Error 404</h1><p>Orders file (orders.json) not found.</p>";
    echo "<br><a href='../client/view_orders.html' class='btn'>Back to View Orders</a>";
    echo "</div></body></html>";
}
?>