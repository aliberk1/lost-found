<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$page_title = 'Add Listing';
require_once __DIR__ . '/includes/header.php';

/* ---------- CSRF ---------- */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

/* ---------- STATE ---------- */
$title       = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$location    = $_POST['location'] ?? '';
$category    = $_POST['category'] ?? '';

/* ---------- Ayarlar ---------- */
$allowedCats = ['Elektronik','Kimlik','Cüzdan','Anahtar','Giyim','Diğer'];

$catTranslations = [
  'Elektronik' => 'Electronics',
  'Kimlik' => 'ID',
  'Cüzdan' => 'Wallet',
  'Anahtar' => 'Keys',
  'Giyim' => 'Clothing',
  'Diğer' => 'Other',
];
$allowedExt  = ['jpg','jpeg','png','webp'];
$maxFiles    = 5;

$errors = [];
$ok = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
    $errors[] = 'Security (CSRF) error.';
  }

  if ($title === '' || mb_strlen($title) < 3)
    $errors[] = 'Title must be at least 3 characters.';

  if ($description === '' || mb_strlen($description) < 10)
    $errors[] = 'Description must be at least 10 characters.';

  if ($category === '' || !in_array($category, $allowedCats, true))
    $errors[] = 'Please select a valid category.';

  if ($location === '')
    $errors[] = 'Location is required.';

  if (!empty($_FILES['images']['name'][0])) {
    if (count($_FILES['images']['name']) > $maxFiles) {
      $errors[] = "You can upload a maximum of $maxFiles photos.";
    }

    foreach ($_FILES['images']['name'] as $name) {
      $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
      if (!in_array($ext, $allowedExt, true)) {
        $errors[] = "Invalid file type: $name";
      }
    }
  }

  if (!$errors) {
    $stmt = $pdo->prepare("
      INSERT INTO items (user_id, title, description, category, location, status, approved)
      VALUES (?, ?, ?, ?, ?, 'OPEN', 0)
    ");
    $stmt->execute([
      $_SESSION['uid'],
      $title,
      $description,
      $category,
      $location
    ]);

    $item_id = $pdo->lastInsertId();

    $uploadDir = __DIR__ . '/public/uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    if (!empty($_FILES['images']['name'][0])) {
      foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if (!is_uploaded_file($tmp)) continue;

        $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
        $fileName = uniqid('img_', true) . '.' . $ext;

        if (move_uploaded_file($tmp, $uploadDir.$fileName)) {
          $pdo->prepare("
            INSERT INTO item_images (item_id, image_path)
            VALUES (?, ?)
          ")->execute([
            $item_id,
            '/lost_found/public/uploads/'.$fileName
          ]);
        }
      }
    }

    $ok = true;
    $title = $description = $location = $category = '';
  }
}
?>

<style>
.post-wrapper {
  max-width: 820px;
  margin: auto;
}

.post-card {
  border-radius: 20px;
  border: none;
  box-shadow: 0 25px 50px rgba(0,0,0,.08);
}

.form-control, .form-select {
  padding: 14px 16px;
  border-radius: 12px;
}

.form-label {
  font-weight: 600;
}

.upload-box {
  border: 2px dashed #CBD5E1;
  border-radius: 16px;
  padding: 24px;
  text-align: center;
  color: #64748B;
}
</style>

<div class="post-wrapper">

  <?php if ($ok): ?>
    <div class="alert alert-success">
      ✅ Listing added successfully.  
      <a href="index.php" class="alert-link">Return to homepage</a>
    </div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e): ?>
        <div><?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="card post-card">
    <div class="card-body p-4">

      <h2 class="mb-1">📌 Add New Listing</h2>
      <p class="text-muted mb-4">
        Share lost or found items in a few steps
      </p>

      <form method="post" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

        <div class="mb-3">
          <label class="form-label">Title</label>
          <input class="form-control" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="E.g.: Black leather wallet">
        </div>

        <div class="mb-3">
          <label class="form-label">Category</label>
          <select class="form-select" name="category">
            <option value="" disabled <?= $category===''?'selected':'' ?>>Select category</option>
            <?php foreach ($allowedCats as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>" <?= $category===$c?'selected':'' ?>><?= htmlspecialchars($catTranslations[$c] ?? $c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">Location</label>
          <input class="form-control" name="location" value="<?= htmlspecialchars($location) ?>" placeholder="E.g.: Ankara/Kızılay">
        </div>

        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea class="form-control" rows="5" name="description"
            placeholder="Date, time and distinguishing features..."><?= htmlspecialchars($description) ?></textarea>
        </div>

        <div class="mb-4">
          <label class="form-label">Fotoğraflar (en fazla 5)</label>
          <div class="upload-box">
            <i class="bi bi-image fs-2"></i>
            <p class="mb-2">Choose photos</p>
            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
          </div>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-primary btn-lg">
            <i class="bi bi-save"></i> Publish Listing
          </button>
          <a href="index.php" class="btn btn-outline-secondary btn-lg">
            Cancel
          </a>
        </div>

      </form>

    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
