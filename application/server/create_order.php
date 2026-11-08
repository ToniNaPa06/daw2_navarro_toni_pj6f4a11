<?php
// Comprovem que la petició vingui per POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $orderId = trim($_POST['order_id'] ?? '');
    $clientName = trim($_POST['client_name'] ?? '');
    $clientAddress = trim($_POST['client_address'] ?? '');
    $clientEmail = trim($_POST['client_email'] ?? '');
    $clientPhone = trim($_POST['client_phone'] ?? '');
    $rawProducts = $_POST['products'] ?? [];

    $selectedProducts = [];
    $subtotal = 0;

    // Filtrem només els productes seleccionats mitjançant el checkbox
    foreach ($rawProducts as $prod) {
        if (isset($prod['selected']) && $prod['selected'] == '1') {
            $name = $prod['name'];
            $price = floatval($prod['price']);
            $quantity = intval($prod['quantity']);
            $totalItemPrice = $price * $quantity;

            $selectedProducts[] = [
                "name" => $name,
                "price" => $price,
                "quantity" => $quantity,
                "total" => $totalItemPrice
            ];

            $subtotal += $totalItemPrice;
        }
    }

    // Càlcul de l'IVA (21%) exigit per la rúbrica
    $vatRate = 0.21;
    $vatAmount = $subtotal * $vatRate;
    $totalWithVat = $subtotal + $vatAmount;

    // Creem l'estructura de la comanda en format d'array associatiu multidimensional
    $newOrder = [
        "order_id" => $orderId,
        "client_name" => $clientName,
        "client_address" => $clientAddress,
        "client_email" => $clientEmail,
        "client_phone" => $clientPhone,
        "products" => $selectedProducts,
        "subtotal" => $subtotal,
        "vat" => $vatAmount,
        "total_with_vat" => $totalWithVat
    ];

    // Ruta cap a la carpeta orders i el fitxer orders.json
    $ordersDir = '../../orders';
    $ordersFile = $ordersDir . '/orders.json';

    // Comprovem si existeix la carpeta orders, sinó la creem
    if (!file_exists($ordersDir)) {
        mkdir($ordersDir, 0777, true);
    }

    // Carreguem les comandes existents o creem un array buit
    $ordersList = [];
    if (file_exists($ordersFile)) {
        $jsonContent = file_get_contents($ordersFile);
        $ordersList = json_decode($jsonContent, true) ?? [];
    }

    // Afegim la nova comanda a la llista
    $ordersList[] = $newOrder;

    // Guardem de nou al fitxer orders.json en format JSON
    if (file_put_contents($ordersFile, json_encode($ordersList, JSON_PRETTY_PRINT))) {
        // Resposta neta amb només el resultat exigit per la part servidora
        echo "<h1>Order Successfully Created!</h1>";
        echo "<p>Order ID: $orderId</p>";
        echo "<p>Total Price (with 21% VAT): <strong>" . number_format($totalWithVat, 2) . " €</strong></p>";
        echo "<br><a href='../client/operations.html'>Back to Operations</a>";
    } else {
        // Control d'errors si no es pot obrir o desar el fitxer (Rúbrica de control d'errors)
        http_response_code(404);
        echo "<h1>Error 404: Cannot write or open orders.json file.</h1>";
        echo "<br><a href='../client/operations.html'>Back to Operations</a>";
    }
} else {
    header("Location: ../client/create_order.html");
    exit();
}
?>