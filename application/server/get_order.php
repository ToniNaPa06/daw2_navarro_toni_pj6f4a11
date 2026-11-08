<?php
require_once __DIR__ . '/functions.php';

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo "Method Not Allowed. Only GET requests are allowed.";
    exit;
}

$orderId = sanitize_input($_GET['order_id'] ?? '');

if (empty($orderId)) {
    render_error_page(404, 'Input Error', 'Error 404 - Missing ID', 'No order identifier was provided.');
}

$ordersFile = __DIR__ . '/../../orders/orders.json';
$allOrders = load_orders($ordersFile);

$order = find_order_by_id($allOrders, $orderId);

if (!$order) {
    render_error_page(404, 'Order Not Found', 'Error 404 - Order Not Found', 'No order was found with identifier: "' . $orderId . '".');
}

http_response_code(200);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Details: <?= htmlspecialchars($order['order_id']) ?> - Order Management System</title>
  <link rel="stylesheet" href="../client/css/styles.css?v=8">
</head>
<body class="home-page">
  <main class="container container-wide">
    <div class="glass-card order-card">
      <div class="app-badge">Order Details</div>
      <h1 class="app-title">Order #<?= htmlspecialchars($order['order_id']) ?></h1>
      <p class="app-subtitle">Commercial customer information, itemized book purchase and calculated taxes.</p>

      <div class="table-wrapper">
        <table class="orders-table">
          <thead>
            <tr>
              <th>Order ID</th>
              <th>Customer Name</th>
              <th>Address</th>
              <th>Contact</th>
              <th>Ordered Books</th>
              <th>Subtotal</th>
              <th>VAT (21%)</th>
              <th>Total Price</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong><?= htmlspecialchars($order['order_id']) ?></strong></td>
              <td><?= htmlspecialchars($order['customer_name']) ?></td>
              <td><?= htmlspecialchars($order['customer_address']) ?></td>
              <td>
                <?= htmlspecialchars($order['customer_email']) ?><br>
                <small style="color: var(--text-muted);"><?= htmlspecialchars($order['customer_phone']) ?></small>
              </td>
              <td>
                <?php if (!empty($order['items'])): ?>
                  <?php foreach ($order['items'] as $item): ?>
                    <div class="badge-item">
                      <?= htmlspecialchars($item['name']) ?> &times; <?= (int)$item['quantity'] ?>
                    </div>
                  <?php endforeach; ?>
                <?php else: ?>
                  <em>No items</em>
                <?php endif; ?>
              </td>
              <td><?= number_format((float)$order['subtotal'], 2) ?> €</td>
              <td><?= number_format((float)$order['vat_amount'], 2) ?> €</td>
              <td><strong><?= number_format((float)$order['total_with_vat'], 2) ?> €</strong></td>
            </tr>
          </tbody>
        </table>
      </div>

      <nav class="order-actions-nav" aria-label="Order Details Navigation">
        <a href="../client/orders-menu.html" class="btn btn-secondary" id="btn-back-view-menu">Back to View Orders</a>
        <a href="../client/operations.html" class="btn btn-secondary" id="btn-back-operations">Back to Operations</a>
        <a href="../client/index.html" class="btn btn-primary" id="btn-back-home">Back to Home</a>
      </nav>

      <footer class="app-footer" style="margin-top: 1.5rem;">
        <p>&copy; 2026-2027 European Bookstore Network &bull; Official Registry</p>
      </footer>
    </div>
  </main>
</body>
</html>