<?php
require_once __DIR__ . '/db.php';

$page_title = $page_title ?? 'NovaTech Gadgets';
$active = $active ?? '';
$base = $base ?? '';
$user = current_user();
$count = cart_count($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($page_title) ?></title>
  <link rel="stylesheet" href="<?= $base ?>css/style.css?v=20260619">
</head>
<body>
  <header class="nav">
    <div class="container nav-inner">
      <a class="brand" href="<?= $base ?>index.php"><span class="brand-mark">N</span><span>NovaTech Gadgets</span></a>
      <nav class="nav-links">
        <a class="<?= $active === 'home' ? 'active' : '' ?>" href="<?= $base ?>index.php">Home</a>
        <a class="<?= $active === 'products' ? 'active' : '' ?>" href="<?= $base ?>products.php">Products</a>
        <a class="<?= $active === 'profile' ? 'active' : '' ?>" href="<?= $base ?>profile.php">Profile</a>
        <a class="<?= $active === 'admin' ? 'active' : '' ?>" href="<?= $base ?>admin/dashboard.php">Admin</a>
      </nav>
      <div class="nav-actions">
        <?php if ($user): ?>
          <span class="icon-link"><?= e($user['full_name']) ?></span>
          <a class="icon-link" href="<?= $base ?>logout.php">Logout</a>
        <?php else: ?>
          <a class="icon-link" href="<?= $base ?>login.php">Login</a>
        <?php endif; ?>
        <a class="icon-link" href="<?= $base ?>cart.php">Cart <span class="cart-count"><?= $count ?></span></a>
      </div>
    </div>
  </header>
