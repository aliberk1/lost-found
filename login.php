<style>
  body {
    background: linear-gradient(180deg, #F8FAFC 0%, #EEF2FF 100%);
  }
</style>
<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Log In';
require_once __DIR__ . '/includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT id, name, password_hash, role FROM users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($pass, $user['password_hash'])) {
      $errors[] = "Incorrect email or password";
    } else {
        login_user($user['id'], $user['name'], $user['role']);
        header("Location: index.php");
        exit;
    }
}
?>

<style>
.login-wrapper {
  min-height: calc(100vh - 120px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-card {
  border-radius: 20px;
  box-shadow: 0 20px 50px rgba(0,0,0,.12);
  border: none;
}

.login-title {
  font-weight: 700;
  text-align: center;
}

.login-sub {
  text-align: center;
  font-size: 14px;
  color: #64748b;
}

.form-control {
  border-radius: 12px;
  padding: 12px 14px;
}

.form-control:focus {
  box-shadow: 0 0 0 .2rem rgba(37,99,235,.15);
}

.login-btn {
  border-radius: 12px;
  padding: 12px;
  font-weight: 600;
}

.login-links {
  text-align: center;
  font-size: 14px;
}
</style>

<div class="login-wrapper">
  <div class="col-md-5 col-lg-4">

    <?php if ($errors): ?>
      <div class="alert alert-danger text-center">
        <?php foreach($errors as $err): ?>
          <div><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="card login-card">
      <div class="card-body p-4">

        <h1 class="login-title mb-2">Welcome</h1>
        <p class="login-sub mb-4">
          Sign in to your Lost & Found account
        </p>
   
        <form method="post" novalidate>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input class="form-control" type="email" name="email" required placeholder="example@mail.com">
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input class="form-control" type="password" name="password" required placeholder="••••••••">
          </div>

          <button class="btn btn-primary w-100 login-btn mt-2" type="submit">
            🔐 Log In
          </button>

        </form>

        <div class="login-links mt-4">
          Don't have an account?
          <a href="register.php" class="fw-semibold">Register</a>
        </div>

      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
