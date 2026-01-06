<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$uid = (int)$_SESSION['uid'];
$id  = (int)($_GET['id'] ?? 0);

/* CSRF */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

/* İlanı getir (SADECE sahibiyse) */
$stmt = $pdo->prepare("SELECT * FROM items WHERE id=? AND user_id=?");
$stmt->execute([$id, $uid]);
$item = $stmt->fetch();

if (!$item) {
  http_response_code(403);
  exit('You do not have permission to edit this listing.');
}

$errors = [];
$okMsg  = null;

/* GÜNCELLEME */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
    $errors[] = 'CSRF error';
  }

  $title       = trim($_POST['title']);
  $description = trim($_POST['description']);
  $category    = $_POST['category'];
  $location    = trim($_POST['location']);

  if (mb_strlen($title) < 3) {
    $errors[] = 'Title must be at least 3 characters.';
  }

  if (mb_strlen($description) < 10) {
    $errors[] = 'Description must be at least 10 characters.';
  }

  if (!$errors) {

    /* METİN BİLGİLERİ GÜNCELLE */
    $upd = $pdo->prepare("
  UPDATE items
  SET
    title=?,
    description=?,
    category=?,
    location=?,
    approved=0
  WHERE id=? AND user_id=?
");

    $upd->execute([
      $title, $description, $category, $location, $id, $uid
    ]);
/* 🔥 ESKİ FOTOĞRAFLARI SİL */
$oldImgs = $pdo->prepare("
  SELECT image_path FROM item_images WHERE item_id=?
");
$oldImgs->execute([$id]);

foreach ($oldImgs->fetchAll() as $img) {
  $filePath = __DIR__ . $img['image_path'];
  if (file_exists($filePath)) {
    unlink($filePath); // dosyayı sil
  }
}

$pdo->prepare("
  DELETE FROM item_images WHERE item_id=?
")->execute([$id]);

    /* ===============================
       FOTO GÜNCELLEME (SADECE EKLEME)
       =============================== */
    if (!empty($_FILES['images']['name'][0])) {

      $allowedExt = ['jpg','jpeg','png','webp'];
      $maxFiles   = 5;

      // mevcut foto sayısı
      $countStmt = $pdo->prepare("SELECT COUNT(*) FROM item_images WHERE item_id=?");
      $countStmt->execute([$id]);
      $currentCount = (int)$countStmt->fetchColumn();

      $uploadDir = __DIR__ . '/public/uploads/';
      if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

      foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
        if ($currentCount >= $maxFiles) break;
        if (!is_uploaded_file($tmp)) continue;

        $ext = strtolower(pathinfo($_FILES['images']['name'][$i], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt, true)) continue;

        $fileName = uniqid('img_', true) . '.' . $ext;

        if (move_uploaded_file($tmp, $uploadDir.$fileName)) {
          $pdo->prepare("
            INSERT INTO item_images (item_id, image_path)
            VALUES (?, ?)
          ")->execute([
            $id,
            '/lost_found/public/uploads/'.$fileName
          ]);
          $currentCount++;
        }
      }
    }

    $okMsg = 'Listing updated ✅ Waiting for admin approval.';


    // güncel veriyi tekrar çek
    $stmt->execute([$id, $uid]);
    $item = $stmt->fetch();
  }
}

$page_title = 'Edit Listing';
require_once __DIR__ . '/includes/header.php';

$catTranslations = [
  'Elektronik' => 'Electronics',
  'Kimlik' => 'ID',
  'Cüzdan' => 'Wallet',
  'Anahtar' => 'Keys',
  'Giyim' => 'Clothing',
  'Diğer' => 'Other',
];
?>

<div class="container" style="max-width:720px">

  <h2 class="mb-3">✏️ Edit Listing</h2>

  <?php if ($okMsg): ?>
    <div class="alert alert-success"><?= htmlspecialchars($okMsg) ?></div>
  <?php endif; ?>

  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <?php foreach ($errors as $e): ?>
        <div><?= htmlspecialchars($e) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <!-- 🔥 enctype EKLENDİ -->
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">

    <div class="mb-3">
      <label class="form-label">Title</label>
      <input class="form-control" name="title"
             value="<?= htmlspecialchars($item['title']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Category</label>
      <select name="category" class="form-select">
        <?php
        $cats = ['Elektronik','Kimlik','Cüzdan','Anahtar','Giyim','Diğer'];
        foreach ($cats as $c):
        ?>
          <option value="<?= htmlspecialchars($c) ?>" <?= $item['category']===$c ? 'selected' : '' ?>>
            <?= htmlspecialchars($catTranslations[$c] ?? $c) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Location</label>
      <input class="form-control" name="location"
             value="<?= htmlspecialchars($item['location']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Description</label>
      <textarea class="form-control" name="description" rows="5"><?= htmlspecialchars($item['description']) ?></textarea>
    </div>

    <!-- 🔥 FOTO EKLEME -->
    <div class="mb-3">
      <label class="form-label">
        Add Photos (max 5 total)
      </label>
      <input type="file"
             name="images[]"
             class="form-control"
             multiple
             accept="image/*">
      <div class="form-text">
        Existing photos are kept. New photos will be added.
      </div>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary">
        <i class="bi bi-save"></i> Save
      </button>

      <a href="profile.php" class="btn btn-outline-secondary">
        Cancel
      </a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
