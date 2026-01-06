<style>
  body {
    background: linear-gradient(180deg, #F8FAFC 0%, #EEF2FF 100%);
  }
</style>
<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Register';
require_once __DIR__ . '/includes/header.php';

$errors = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    if ($name === '' || $email === '' || $pass === '' || $pass2 === '') {
      $errors[] = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Please enter a valid email address.";
    } elseif (strlen($pass) < 6) {
      $errors[] = "Password must be at least 6 characters.";
    } elseif ($pass !== $pass2) {
      $errors[] = "Passwords do not match.";
    } else {
        try {
            $stmt = $pdo->prepare("
              INSERT INTO users (name, email, password_hash, role)
              VALUES (?, ?, ?, 'USER')
            ");
            $stmt->execute([
              $name,
              $email,
              password_hash($pass, PASSWORD_DEFAULT)
            ]);
            $ok = true;
            $_POST = [];
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
            $errors[] = "This email address is already registered.";
          } else {
            $errors[] = "An error occurred.";
            }
        }
    }
}
?>

<style>
.register-wrapper {
  min-height: calc(100vh - 120px);
  display: flex;
  align-items: center;
  justify-content: center;
}

.register-card {
  border-radius: 20px;
  box-shadow: 0 20px 50px rgba(0,0,0,.12);
  border: none;
}

.register-title {
  font-weight: 700;
  text-align: center;
}

.register-sub {
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

.register-btn {
  border-radius: 12px;
  padding: 12px;
  font-weight: 600;
}

.register-links {
  text-align: center;
  font-size: 14px;
}
</style>

<div class="register-wrapper">
  <div class="col-md-5 col-lg-4">

    <?php if ($ok): ?>
      <div class="alert alert-success text-center">
        🎉 Registration successful!  
        <a href="login.php" class="alert-link fw-semibold">Log in</a>
      </div>
    <?php endif; ?>

    <?php if ($errors): ?>
      <div class="alert alert-danger text-center">
        <?php foreach($errors as $err): ?>
          <div><?= htmlspecialchars($err) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <div class="card register-card">
      <div class="card-body p-4">

        <h1 class="register-title mb-2">✨ Create Account</h1>
        <p class="register-sub mb-4">
          Join the Lost & Found platform
        </p>

        <form method="post" novalidate>

          <div class="mb-3">
                 <label class="form-label">Full Name</label>
                 <input class="form-control" type="text" name="name"
                   placeholder="Your full name"
                   required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
          </div>

          <div class="mb-3">
                 <label class="form-label">Email</label>
                 <input class="form-control" type="email" name="email"
                   placeholder="example@gmail.com"
                   required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>

          <div class="mb-3">
                 <label class="form-label">Password</label>
                 <input class="form-control" type="password" name="password"
                   placeholder="At least 6 characters"
                   required>
          </div>

          <div class="mb-3">
                 <label class="form-label">Confirm Password</label>
                 <input class="form-control" type="password" name="password2"
                   placeholder="Re-enter password"
                   required>
          </div>

          <button class="btn btn-primary w-100 register-btn mt-2" type="submit">
          Register
          </button>

        </form>

        <div class="register-links mt-4">
          Already have an account?
          <a href="login.php" class="fw-semibold">Log In</a>
        </div>

      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
