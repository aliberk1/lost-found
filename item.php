<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$page_title = 'Listing Details';

require_once __DIR__ . '/includes/header.php';

$catTranslations = [
  'Elektronik' => 'Electronics',
  'Kimlik' => 'ID',
  'Cüzdan' => 'Wallet',
  'Anahtar' => 'Keys',
  'Giyim' => 'Clothing',
  'Diğer' => 'Other',
];

$errors = [];
$okMsg = null;

if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

/* ---------- İlanı Veritabanından Çek ---------- */
$stmt = $pdo->prepare("
  SELECT i.*, u.name AS owner_name 
  FROM items i 
  JOIN users u ON u.id = i.user_id 
  WHERE i.id = ? AND i.approved = 1
  LIMIT 1
");
$stmt->execute([$id]);
$item = $stmt->fetch();
/* ---------- İlan Fotoğrafları ---------- */
$imgStmt = $pdo->prepare("
  SELECT image_path
  FROM item_images
  WHERE item_id = ?
");
$imgStmt->execute([$id]);
$images = $imgStmt->fetchAll();

if (!$item): ?>
  <div class="alert alert-danger mt-4">İlan bulunamadı veya onaylanmamış.</div>
<?php
  require_once __DIR__ . '/includes/footer.php';
  exit;
endif;

/* ---------- Rezervasyon Yapma ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reserve'])) {
  if (!is_logged_in()) {
    $errors[] = 'Rezervasyon yapmak için giriş yapmalısın.';
  } elseif (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
    $errors[] = 'Güvenlik (CSRF) hatası.';
  } else {
    try {
      $pdo->beginTransaction();
      $lock = $pdo->prepare("SELECT status FROM items WHERE id=? FOR UPDATE");
      $lock->execute([$id]);
      $row = $lock->fetch();
      if (!$row) throw new Exception('Listing not found.');

      if ($row['status'] !== 'OPEN') {
        throw new Exception('This listing is no longer available.');
      }

      $upd = $pdo->prepare("UPDATE items SET status='RESERVED', reserved_by=? WHERE id=?");
      $upd->execute([$_SESSION['uid'], $id]);
      $pdo->commit();

      $okMsg = "Reservation completed successfully ✅";
      $item['status'] = 'RESERVED';
      $item['reserved_by'] = $_SESSION['uid'];
    } catch (Exception $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $errors[] = $e->getMessage();
    }
  }
}

/* ---------- Rezervasyon İptali ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_reservation'])) {
  if (!is_logged_in()) {
    $errors[] = 'İptal için giriş yapmalısın.';
  } elseif (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
    $errors[] = 'Güvenlik (CSRF) hatası.';
  } else {
    try {
      $pdo->beginTransaction();
      $lock = $pdo->prepare("SELECT status, reserved_by FROM items WHERE id=? FOR UPDATE");
      $lock->execute([$id]);
      $row = $lock->fetch();
      if (!$row) throw new Exception('Listing not found.');

      if ($row['status'] !== 'RESERVED') {
        throw new Exception('This listing is not reserved.');
      }
      if ((int)$row['reserved_by'] !== (int)$_SESSION['uid']) {
        throw new Exception('Only the user who reserved may cancel.');
      }

      $upd = $pdo->prepare("UPDATE items SET status='OPEN', reserved_by=NULL WHERE id=?");
      $upd->execute([$id]);
      $pdo->commit();

      $okMsg = "Reservation cancelled ✅";
      $item['status'] = 'OPEN';
      $item['reserved_by'] = null;
    } catch (Exception $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      $errors[] = $e->getMessage();
    }
  }
}
?>

<?php if ($okMsg): ?>
  <div class="alert alert-success"><?= htmlspecialchars($okMsg) ?></div>
<?php endif; ?>
<?php if ($errors): ?>
  <div class="alert alert-danger">
    <?php foreach($errors as $err): ?>
      <div><?= htmlspecialchars($err) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="card shadow-sm mt-3">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-2">
      <h3 class="card-title mb-0"><?= htmlspecialchars($item['title']) ?></h3>
      <span class="badge text-bg-<?= $item['status']==='OPEN' ? 'success' : ($item['status']==='RESERVED' ? 'warning' : 'secondary') ?>">
        <?= htmlspecialchars($item['status']) ?>
      </span>
    </div>
    
   <?php $mapAddress = urlencode($item['location']); ?>
  
<p class="text-muted mb-1">
  <?= htmlspecialchars($catTranslations[$item['category']] ?? $item['category']) ?> •
  <a href="https://www.google.com/maps/search/?api=1&query=<?= $mapAddress ?>"
     target="_blank"
     class="text-decoration-none text-primary">
     <?= htmlspecialchars($item['location']) ?>
  </a>
</p>
<p><?= nl2br(htmlspecialchars($item['description'])) ?></p>

<?php if ($images): ?>
  <div class="row g-3 my-4">
    <?php foreach ($images as $img): ?>
      <div class="col-6 col-md-4">
        <img src="<?= htmlspecialchars($img['image_path']) ?>"
             class="img-fluid rounded"
             style="height:160px;object-fit:cover;">
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>


    <p class="text-muted small mb-0">Owner: <?= htmlspecialchars($item['owner_name']) ?></p>
    <p class="text-muted small">Date: <?= htmlspecialchars(date('d.m.Y H:i', strtotime($item['created_at']))) ?></p>

    <?php if (is_logged_in() && (int)$item['user_id'] !== (int)$_SESSION['uid']): ?>
    <a class="btn btn-outline-primary btn-sm mt-2"
      href="message_start.php?to=<?= (int)$item['user_id'] ?>&item_id=<?= (int)$item['id'] ?>">
      <i class="bi bi-chat-dots"></i> Message Owner
    </a>
<?php endif; ?>

  </div>
</div>

<?php if (is_logged_in()): ?>
  <div class="mt-3">
    <?php if ($item['status']==='OPEN'): ?>
      <form method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <button class="btn btn-primary" type="submit" name="reserve" value="1">
          <i class="bi bi-bookmark-plus"></i> Reserve
        </button>
      </form>
    <?php elseif ($item['status']==='RESERVED' && (int)($item['reserved_by'] ?? 0) === (int)$_SESSION['uid']): ?>
      <form method="post" class="d-inline">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
        <button class="btn btn-outline-danger" type="submit" name="cancel_reservation" value="1">
          <i class="bi bi-x-circle"></i> Cancel Reservation
        </button>
      </form>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="alert alert-warning mt-3">
    To reserve this listing, <a href="login.php" class="alert-link">log in</a>.
  </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>