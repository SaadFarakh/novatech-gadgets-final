<?php
$page_title = 'Login | NovaTech Gadgets';
require_once __DIR__ . '/includes/db.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        require_once __DIR__ . '/includes/auth.php';
        login_user($user);
        redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
    }
    $error = 'Invalid email or password.';
}

require_once __DIR__ . '/includes/header.php';
?>
<main class="section">
  <div class="container auth-layout">
    <form class="panel auth-card" method="post">
      <h1>Login</h1>
      <p>Access your NovaTech account.</p>
      <?php if ($error): ?><div class="notice error"><?= e($error) ?></div><?php endif; ?>
      <div class="field"><label>Email</label><input name="email" type="email" required></div>
      <div class="field"><label>Password</label><input name="password" type="password" required></div>
      <button class="btn" type="submit">Login</button>
      <a class="btn ghost" href="register.php">Create account</a>
    </form>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>

