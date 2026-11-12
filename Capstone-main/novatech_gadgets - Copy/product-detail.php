<?php
$page_title = 'Product Detail | NovaTech Gadgets';
$active = 'products';
require_once __DIR__ . '/includes/header.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p JOIN categories c ON c.category_id = p.category_id WHERE p.product_id = ? AND p.status = 'active'");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    redirect('products.php');
}

$relatedStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND product_id <> ? AND status = 'active' LIMIT 4");
$relatedStmt->execute([$product['category_id'], $product['product_id']]);
$related = $relatedStmt->fetchAll();
$uploadedColorRows = product_uploaded_color_rows($pdo, (int) $product['product_id']);
$colors = $uploadedColorRows
    ? array_map(fn($row) => [$row['color_name'], $row['color_hex']], $uploadedColorRows)
    : product_colors($product['product_name']);
$specs = product_specs($product['product_name']);
$variants = product_variants($product['product_name'], (float) $product['price']);
$colorImages = $uploadedColorRows
    ? array_column($uploadedColorRows, 'image', 'color_name')
    : product_color_images($product['product_name'], product_image_url($product));
$displayProduct = $product;
if ($colors && !empty($colorImages[$colors[0][0]])) {
    $displayProduct['image'] = $colorImages[$colors[0][0]];
}
?>
<main>
  <section class="section">
    <div class="container detail-layout">
      <div class="detail-media"><?= product_visual($displayProduct) ?></div>
      <div class="detail-info">
        <span class="badge"><?= e($product['category_name']) ?></span>
        <h1><?= e($product['product_name']) ?></h1>
        <p class="rating"><?= e((string) $product['rating']) ?> / 5 rating</p>
        <h2 data-product-price><?= money($variants[0][1]) ?></h2>
        <p><?= e($product['description']) ?></p>
        <?php if ($variants): ?>
          <h3>Storage / spec</h3>
          <div class="option-row variant-options" data-variant-options>
            <?php foreach ($variants as $index => $variant): ?>
              <button class="option variant-option <?= $index === 0 ? 'selected' : '' ?>" type="button" data-variant-name="<?= e($variant[0]) ?>" data-variant-price="<?= e((string) $variant[1]) ?>">
                <span><?= e($variant[0]) ?></span><strong><?= money($variant[1]) ?></strong>
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <?php if ($colors): ?>
          <h3>Available market colors</h3>
          <div class="option-row color-options" data-color-options>
            <?php foreach ($colors as $index => $color): ?>
              <button class="option color-option <?= $index === 0 ? 'selected' : '' ?>" type="button" data-color-name="<?= e($color[0]) ?>" data-color-image="<?= e(($colorImages[$color[0]] ?? '') ?: product_image_url($product)) ?>">
                <span class="color-swatch" style="background: <?= e($color[1]) ?>"></span><?= e($color[0]) ?>
              </button>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <form class="option-row" action="cart.php" method="post" data-product-purchase>
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>">
          <input type="hidden" name="selected_color" value="<?= e($colors[0][0] ?? '') ?>" data-selected-color>
          <input type="hidden" name="selected_spec" value="<?= e($variants[0][0]) ?>" data-selected-variant>
          <input type="hidden" name="selected_price" value="<?= e((string) $variants[0][1]) ?>" data-selected-price>
          <input name="quantity" type="number" min="1" max="<?= (int) $product['stock_quantity'] ?>" value="1" style="max-width:100px;">
          <button class="btn" type="submit">Add to Cart</button>
        </form>
        <div class="panel">
          <h3>Product specifications</h3>
          <table>
            <tr><th>Brand</th><td><?= e($product['brand']) ?></td></tr>
            <tr><th>Category</th><td><?= e($product['category_name']) ?></td></tr>
            <tr><th>Stock</th><td><?= (int) $product['stock_quantity'] ?> units</td></tr>
            <?php foreach ($specs as $index => $spec): ?>
              <tr><th>Spec <?= $index + 1 ?></th><td><?= e($spec) ?></td></tr>
            <?php endforeach; ?>
            <tr><th>Warranty</th><td>1 year limited service demo</td></tr>
          </table>
        </div>
      </div>
    </div>
  </section>
  <section class="section soft">
    <div class="container">
      <div class="section-head"><div><h2>Related products</h2><p>More products from the same setup family.</p></div></div>
      <div class="grid">
        <?php foreach ($related as $product): ?>
          <?php include __DIR__ . '/includes/product-card.php'; ?>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
