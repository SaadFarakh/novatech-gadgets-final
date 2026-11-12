<?php
$page_title = 'Checkout | NovaTech Gadgets';
$active = 'products';
require_once __DIR__ . '/includes/auth.php';
require_login();

$userId = (int) $_SESSION['user']['user_id'];
$stmt = $pdo->prepare('SELECT c.quantity, c.selected_color, c.selected_spec, c.unit_price, p.* FROM cart c JOIN products p ON p.product_id = c.product_id WHERE c.user_id = ?');
$stmt->execute([$userId]);
$items = $stmt->fetchAll();

$subtotal = array_reduce($items, fn($sum, $item) => $sum + ((float) ($item['unit_price'] ?? $item['price']) * (int) $item['quantity']), 0.0);
$shipping = $subtotal > 0 ? 15.00 : 0.00;
$tax = $subtotal * 0.06;
$total = $subtotal + $shipping + $tax;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['shipping_name'] ?? '');
    $phone = trim($_POST['shipping_phone'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $payment = trim($_POST['payment_method'] ?? '');

    if (!$items) {
        $error = 'Your cart is empty.';
    } elseif ($name === '' || $phone === '' || $address === '' || $payment === '') {
        $error = 'Please complete all checkout fields.';
    } else {
        $pdo->beginTransaction();
        $order = $pdo->prepare("INSERT INTO orders (user_id, total_amount, order_status, shipping_name, shipping_phone, shipping_address, payment_method) VALUES (?, ?, 'Pending', ?, ?, ?, ?)");
        $order->execute([$userId, $total, $name, $phone, $address, $payment]);
        $orderId = (int) $pdo->lastInsertId();

        $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price, subtotal, selected_options) VALUES (?, ?, ?, ?, ?, ?)');
        $stockStmt = $pdo->prepare('UPDATE products SET stock_quantity = GREATEST(stock_quantity - ?, 0) WHERE product_id = ?');
        foreach ($items as $item) {
            $unitPrice = (float) ($item['unit_price'] ?? $item['price']);
            $line = $unitPrice * (int) $item['quantity'];
            $selectedOptions = trim(($item['selected_spec'] ?? '') . ' ' . ($item['selected_color'] ?? ''));
            $itemStmt->execute([$orderId, $item['product_id'], $item['quantity'], $unitPrice, $line, $selectedOptions]);
            $stockStmt->execute([$item['quantity'], $item['product_id']]);
        }
        $paymentStmt = $pdo->prepare("INSERT INTO payments (order_id, payment_method, payment_status) VALUES (?, ?, 'Pending')");
        $paymentStmt->execute([$orderId, $payment]);
        $clear = $pdo->prepare('DELETE FROM cart WHERE user_id = ?');
        $clear->execute([$userId]);
        $pdo->commit();
        redirect('profile.php?ordered=1');
    }
}

require_once __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="container">
    <div class="page-title"><h1>Checkout</h1><p>Complete your shipping and payment details.</p></div>
    <div class="checkout-layout">
      <form class="panel checkout-form" method="post">
        <h2>Customer information</h2>
        <?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
        <div class="form-grid">
          <div class="field"><label>Shipping name</label><input name="shipping_name" required value="<?= e(current_user()['full_name']) ?>"></div>
          <div class="field"><label>Phone</label><input name="shipping_phone" required></div>
          <div class="field full"><label>Shipping address</label><textarea name="shipping_address" required></textarea></div>
          <div class="field full"><label>Payment method</label><select name="payment_method" required><option>Credit / Debit Card</option><option>Online Banking</option><option>Cash on Delivery</option></select></div>
        </div>
        <button class="btn" type="submit">Place Order</button>
      </form>
      <aside class="panel checkout-summary">
        <h2>Order summary</h2>
        <?php foreach ($items as $item): ?>
          <?php $selectedOptions = trim(($item['selected_spec'] ?? '') . ' ' . ($item['selected_color'] ?? '')); ?>
          <div class="summary-row"><span><?= e($item['product_name']) ?><?php if ($selectedOptions !== ''): ?> <small><?= e($selectedOptions) ?></small><?php endif; ?> x <?= (int) $item['quantity'] ?></span><strong><?= money((float) ($item['unit_price'] ?? $item['price']) * (int) $item['quantity']) ?></strong></div>
        <?php endforeach; ?>
        <div class="summary-row total"><span>Total</span><span><?= money($total) ?></span></div>
      </aside>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
