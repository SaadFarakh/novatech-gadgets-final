<article class="product-card">
  <a class="product-image" href="product-detail.php?id=<?= (int) $product['product_id'] ?>">
    <?= product_visual($product) ?>
  </a>
  <div class="product-body">
    <span class="badge"><?= e($product['brand']) ?></span>
    <h3><?= e($product['product_name']) ?></h3>
    <p><?= e($product['description']) ?></p>
    <div class="product-meta">
      <span><?= money($product['price']) ?></span>
      <span class="rating"><?= e((string) $product['rating']) ?> / 5</span>
    </div>
    <div class="card-actions">
      <form action="cart.php" method="post">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= (int) $product['product_id'] ?>">
        <button class="btn" type="submit">Add to Cart</button>
      </form>
      <a class="btn secondary" href="product-detail.php?id=<?= (int) $product['product_id'] ?>">View Details</a>
    </div>
  </div>
</article>
