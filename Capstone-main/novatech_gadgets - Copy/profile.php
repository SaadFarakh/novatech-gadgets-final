<?php
$page_title = 'Profile | NovaTech Gadgets';
$active = 'profile';
require_once __DIR__ . '/includes/auth.php';
require_login();

$userId = (int) $_SESSION['user']['user_id'];
$stmt = $pdo->prepare('SELECT * FROM users WHERE user_id = ?');
$stmt->execute([$userId]);
$profile = $stmt->fetch();

$orders = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$orders->execute([$userId]);
$orders = $orders->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="container">
    <div class="page-title"><h1>User profile</h1><p>View your account details and order history.</p></div>
    <?php if (isset($_GET['ordered'])): ?><div class="notice">Order placed successfully. Your payment status is pending for this demo.</div><?php endif; ?>
    <div class="profile-layout">
      <aside class="panel profile-side">
        <h2><?= e($profile['full_name']) ?></h2>
        <p><?= e($profile['email']) ?></p>
        <p><?= e($profile['phone'] ?? 'No phone saved') ?></p>
        <span class="badge delivered"><?= e($profile['role']) ?></span>
      </aside>
      <section class="panel profile-main">
        <h2>Order history</h2>
        <table>
          <thead><tr><th>Order</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach ($orders as $order): ?>
              <tr>
                <td>#<?= (int) $order['order_id'] ?></td>
                <td><?= money($order['total_amount']) ?></td>
                <td><span class="badge <?= strtolower($order['order_status']) ?>"><?= e($order['order_status']) ?></span></td>
                <td><?= e($order['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$orders): ?><tr><td colspan="4">No orders yet.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </section>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

