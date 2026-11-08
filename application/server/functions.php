<?php
/**
 * functions.php - Reusable functions for order management
 */

function render_error_page($statusCode, $badge, $title, $message) {
    http_response_code($statusCode);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <title><?= htmlspecialchars($title) ?> - Order Management System</title>
      <link rel="stylesheet" href="../client/css/styles.css?v=8">
    </head>
    <body class="home-page">
      <main class="container">
        <div class="glass-card">
          <div class="app-badge"><?= htmlspecialchars($badge) ?></div>
          <h1 class="app-title" style="color: #991b1b;"><?= htmlspecialchars($title) ?></h1>
          <p class="app-subtitle"><?= htmlspecialchars($message) ?></p>
          <div class="button-group">
            <a href="../client/orders-menu.html" class="btn btn-secondary">Go to View Orders</a>
            <a href="../client/operations.html" class="btn btn-secondary">Back to Operations</a>
            <a href="../client/index.html" class="btn btn-primary">Back to Home</a>
          </div>
        </div>
      </main>
    </body>
    </html>
    <?php
    exit;
}

function load_orders($filePath) {
    if (!file_exists($filePath)) {
        render_error_page(404, 'File Error', 'Error 404 - File Not Found', 'The orders file (orders.json) could not be found.');
    }

    $jsonContent = @file_get_contents($filePath);
    if ($jsonContent === false) {
        render_error_page(404, 'File Error', 'Error 404 - File Error', 'The orders file (orders.json) could not be opened.');
    }

    $orders = json_decode($jsonContent, true);
    if (!is_array($orders)) {
        render_error_page(404, 'File Error', 'Error 404 - Invalid File', 'The orders file could not be parsed.');
    }

    return $orders;
}

function find_order_by_id($orders, $orderId) {
    foreach ($orders as $order) {
        if (isset($order['order_id']) && $order['order_id'] === $orderId) {
            return $order;
        }
    }
    return null;
}

function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data ?? '')));
}