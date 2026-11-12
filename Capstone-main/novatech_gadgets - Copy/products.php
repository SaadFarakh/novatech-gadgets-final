<?php
$page_title = 'Products | NovaTech Gadgets';
$active = 'products';
require_once __DIR__ . '/includes/header.php';

$search = trim($_GET['search'] ?? '');
$category = (int) ($_GET['category'] ?? 0);
$sort = $_GET['sort'] ?? '';
$categories = $pdo->query('SELECT * FROM categories ORDER BY category_name')->fetchAll();

$sql = "SELECT p.*, c.category_name FROM products p JOIN categories c ON c.category_id = p.category_id WHERE p.status = 'active'";
$params = [];
if ($search !== '') {
    $sql .= ' AND (p.product_name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)';
    $like = "%{$search}%";
    array_push($params, $like, $like, $like);
}
if ($category > 0) {
    $sql .= ' AND p.category_id = ?';
    $params[] = $category;
}
$sql .= match ($sort) {
    'price_low' => ' ORDER BY p.price ASC',
    'price_high' => ' ORDER BY p.price DESC',
    'popular' => ' ORDER BY p.rating DESC',
    'newest' => ' ORDER BY p.created_at DESC, p.product_id DESC',
    default => ' ORDER BY p.product_name ASC',
};
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>
<main class="section">
  <div class="container">
    <div class="page-title"><h1>Shop gadgets</h1><p>Search, filter, and sort products from the database.</p></div>
    <form class="filters" method="get">
      <input name="search" value="<?= e($search) ?>" placeholder="Search products">
      <select name="category">
        <option value="0">All categories</option>
        <?php foreach ($categories as $item): ?>
          <option value="<?= (int) $item['category_id'] ?>" <?= $category === (int) $item['category_id'] ? 'selected' : '' ?>><?= e($item['category_name']) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="sort">
        <option value="">Sort by</option>
        <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price low</option>
        <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price high</option>
        <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Popularity</option>
        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
      </select>
      <button class="btn" type="submit">Apply</button>
      <a class="btn secondary" href="products.php">Reset</a>
    </form>
    <p><span class="badge"><?= count($products) ?> products</span></p>
    <div class="grid">
      <?php foreach ($products as $product): ?>
        <?php include __DIR__ . '/includes/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

