<?php
$page_title = 'Add Product | NovaTech Gadgets';
$active = 'admin';
$base = '../';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/product-image-admin.php';
require_admin();

$categories = $pdo->query('SELECT * FROM categories ORDER BY category_name')->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['product_name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = (int) ($_POST['category_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock_quantity'] ?? 0);
    $rating = (float) ($_POST['rating'] ?? 4.5);
    $image = trim($_POST['image_url'] ?? '');
    $product = [
        'product_name' => $name,
        'brand' => $brand,
        'description' => $description,
        'category_id' => $category,
        'price' => $price,
        'stock_quantity' => $stock,
        'image' => $image,
        'rating' => $rating,
        'status' => 'active',
    ];

    if ($name === '' || $brand === '' || $category <= 0 || $price <= 0) {
        $error = 'Please complete all required product fields.';
    } else {
        try {
            $uploadedImage = upload_product_image($_FILES['product_image_file'] ?? [], $name);
            if ($uploadedImage !== '') {
                $image = $uploadedImage;
            }

            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO products (category_id, product_name, brand, description, price, stock_quantity, image, rating, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active')");
            $stmt->execute([$category, $name, $brand, $description, $price, $stock, $image, $rating]);
            $productId = (int) $pdo->lastInsertId();
            save_product_color_images($pdo, $productId, $_POST, $_FILES);
            $pdo->commit();
            redirect('products.php');
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = $exception->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<main class="section">
  <div class="container">
    <div class="page-title"><h1>Add product</h1><p>Create a new product record.</p></div>
    <div class="admin-shell">
      <?php include __DIR__ . '/nav.php'; ?>
      <form class="panel admin-main" method="post" enctype="multipart/form-data">
        <?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
        <?php include __DIR__ . '/product-form.php'; ?>
        <button class="btn" type="submit">Save Product</button>
      </form>
    </div>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
