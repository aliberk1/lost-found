<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

$page_title = 'My Profile';
require_once __DIR__ . '/includes/header.php';

$catTranslations = [
  'Elektronik' => 'Electronics',
  'Kimlik' => 'ID',
  'Cüzdan' => 'Wallet',
  'Anahtar' => 'Keys',
  'Giyim' => 'Clothing',
  'Diğer' => 'Other',
];

/* CSRF */
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

$uid    = (int)$_SESSION['uid'];
$errors = [];
$okMsg  = null;

/* ---------- İşlemler ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
    $errors[] = 'Security (CSRF) error.';
  } else {
    $itemId = (int)($_POST['item_id'] ?? 0);

if ($_POST['action'] === 'mark_resolved' || $_POST['action'] === 'cancel_resolved') {

  $get = $pdo->prepare("
    SELECT status 
    FROM items 
    WHERE id=? AND user_id=?
  ");
  $get->execute([$itemId, $uid]);
  $row = $get->fetch();

  if (!$row) {
    $errors[] = 'Listing not found or you do not have permission.';
  } else {

    if ($_POST['action'] === 'mark_resolved' && $row['status'] !== 'RESOLVED') {

      $upd = $pdo->prepare("
        UPDATE items 
        SET status='RESOLVED' 
        WHERE id=? AND user_id=?
      ");
      $upd->execute([$itemId, $uid]);
      $okMsg = 'Listing marked as RESOLVED ✅';

    }

    if ($_POST['action'] === 'cancel_resolved' && $row['status'] === 'RESOLVED') {

      $upd = $pdo->prepare("
        UPDATE items 
        SET status='OPEN' 
        WHERE id=? AND user_id=?
      ");
      $upd->execute([$itemId, $uid]);
      $okMsg = 'Resolved status cancelled ↩️';

    }
  }
}


    if ($_POST['action'] === 'delete_item') {
      $del = $pdo->prepare("DELETE FROM items WHERE id=? AND user_id=?");
      $del->execute([$itemId, $uid]);
      $okMsg = "Listing deleted.";
    }
  }
}

/* ---------- Benim ilanlarım ---------- */
$stmt = $pdo->prepare("
 SELECT 
  i.id, i.title, i.category, i.location, i.status, i.created_at,
  i.reserved_by,
  u.name AS reserver_name,
  img.image_path
FROM items i
LEFT JOIN users u ON u.id = i.reserved_by
LEFT JOIN (
  SELECT item_id, MIN(image_path) AS image_path
  FROM item_images
  GROUP BY item_id
) img ON img.item_id = i.id
WHERE i.user_id=?
ORDER BY i.created_at DESC

");
$stmt->execute([$uid]);
$myItems = $stmt->fetchAll(); 
/* ---------- Benim reserve ettiğim ilanlar ---------- */
$reservedStmt = $pdo->prepare("
  SELECT 
    i.id, i.title, i.category, i.location, i.status, i.created_at,
    u.name AS owner_name,
    img.image_path
  FROM items i
  JOIN users u ON u.id = i.user_id
  LEFT JOIN (
    SELECT item_id, MIN(image_path) AS image_path
    FROM item_images
    GROUP BY item_id
  ) img ON img.item_id = i.id
  WHERE i.reserved_by = ?
    AND i.status = 'RESERVED'
  ORDER BY i.created_at DESC
");
$reservedStmt->execute([$uid]);
$reservedItems = $reservedStmt->fetchAll();

?>



<style>
/* ===== PROFILE CARD ===== */


.profile-card {
  background: #ffffff;
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 14px 35px rgba(0,0,0,.08);
  transition: all .3s ease;
  display: flex;
  flex-direction: column;
}

.profile-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 26px 60px rgba(0,0,0,.14);
}

/* ===== IMAGE AREA ===== */
.profile-thumb {
  height: 160px;
  background: linear-gradient(135deg, #F1F5FF, #F8FAFC);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.profile-thumb img {
  max-width: 90%;
  max-height: 90%;
  object-fit: contain;
  transition: transform .3s ease;
}

.profile-card:hover .profile-thumb img {
  transform: scale(1.05);
}

/* ===== BODY ===== */
.profile-card .card-body {
  padding: 18px 20px;
}

.profile-card .card-title {
  font-size: 18px;
  font-weight: 600;
  color: #0F172A;
  margin-bottom: 4px;
}

.profile-meta {
  font-size: 14px;
  color: #64748B;
}

/* ===== STATUS ===== */
.profile-status {
  display: inline-block;
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  margin-top: 8px;
}

.profile-status.open {
  background: rgba(22,163,74,.15);
  color: #166534;
}

.profile-status.reserved {
  background: rgba(245,158,11,.18);
  color: #92400E;
}

.profile-status.resolved {
  background: rgba(100,116,139,.2);
  color: #334155;
}

/* ===== FOOTER ===== */
.profile-card .card-footer {
  background: transparent;
  border-top: 1px solid 
  #000000ff;
  padding: 14px 16px;
  display: flex;
  gap: 8px;
}

.profile-card .btn {
  border-radius: 12px;
  font-size: 13px;
}
.profile-hero {
  background: linear-gradient(135deg, #F8FAFC, #EEF2FF);
  border-radius: 20px;
  padding: 22px 26px;   
  margin-bottom: 28px;
}

.profile-title {
  font-size: 26px;     
  font-weight: 700;
  color: #0F172A;
  line-height: 1.25;
  margin-bottom: 4px;
}

.profile-subtitle {
  font-size: 14px;      
  color: #64748B;
  max-width: 520px;
}


</style>

<div class="profile-page">
  <!-- TÜM PROFİL İÇERİĞİ BURADA -->


<div class="profile-hero mb-5">
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

    <div>
      <span class="badge bg-primary-subtle text-primary mb-2">
        👤 My Profile
      </span>

      <h1 class="profile-title">
        Welcome back,
        <span class="text-primary"><?= htmlspecialchars($_SESSION['name']) ?></span>
      </h1>

      <p class="profile-subtitle">
        Manage your lost & found listings, update their status or edit details.
      </p>
    </div>

    <a href="post.php" class="btn btn-primary">
      <i class="bi bi-plus-circle"></i> Add New Listing
    </a>

  </div>
</div>


<?php if ($okMsg): ?>
  <div class="alert alert-success"><?= htmlspecialchars($okMsg) ?></div>
<?php endif; ?>

<?php if ($errors): ?>
  <div class="alert alert-danger">
    <?php foreach($errors as $e): ?>
      <div><?= htmlspecialchars($e) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<h2 class="h5 mt-5 mb-3">Reserved Listings</h2>

<?php if (!$reservedItems): ?>
  <div class="alert alert-secondary">
    You haven't reserved any listings.
  </div>
<?php else: ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
  <?php foreach ($reservedItems as $it): ?>
    <div class="col">
      <div class="card profile-card h-100 border-warning">

        <!-- IMAGE -->
        <div class="profile-thumb">
          <?php if (!empty($it['image_path'])): ?>
            <img src="<?= htmlspecialchars($it['image_path']) ?>" alt="Listing image">
          <?php else: ?>
            <i class="bi bi-image text-secondary fs-2"></i>
          <?php endif; ?>
        </div>

        <!-- BODY -->
        <div class="card-body">
          <div class="text-muted small">
            <?= htmlspecialchars(date('d.m.Y H:i', strtotime($it['created_at']))) ?>
          </div>

          <h5 class="card-title mt-1">
            <?= htmlspecialchars($it['title']) ?>
          </h5>

          <div class="profile-meta mb-2">
            <?= htmlspecialchars($catTranslations[$it['category']] ?? $it['category']) ?>
            • <?= htmlspecialchars($it['location']) ?>
          </div>
            
          <span class="profile-status reserved">
            RESERVED
          </span>
        

          <div class="small text-muted mt-2">
          Listing Owner: <?= htmlspecialchars($it['owner_name']) ?>
          </div>
        </div>

        <!-- FOOTER -->
        <div class="card-footer">
          <a href="item.php?id=<?= (int)$it['id'] ?>"
             class="btn btn-outline-primary btn-sm">
            Details
          </a>
        </div>

      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php endif; ?>


<h2 class="h5 mt-4 mb-3">My Listings</h2>

<?php if (!$myItems): ?>
  <div class="alert alert-secondary">
    You haven't added any listings yet.
    <a href="post.php" class="alert-link">Add a listing</a>
  </div>
<?php else: ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
  <?php foreach ($myItems as $it): ?>
    <div class="col">
      <div class="card profile-card h-100">

        <!-- IMAGE -->
        <div class="profile-thumb">
          <?php if (!empty($it['image_path'])): ?>
            <img src="<?= htmlspecialchars($it['image_path']) ?>" alt="Listing image">
          <?php else: ?>
            <i class="bi bi-image text-secondary fs-2"></i>
          <?php endif; ?>
        </div>

        <!-- BODY -->
        <div class="card-body">
          <div class="text-muted small">
            <?= htmlspecialchars(date('d.m.Y H:i', strtotime($it['created_at']))) ?>
          </div>

          <h5 class="card-title mt-1">
            <?= htmlspecialchars($it['title']) ?>
          </h5>

          <div class="profile-meta mb-2">
            <?= htmlspecialchars($catTranslations[$it['category']] ?? $it['category']) ?>
            • <?= htmlspecialchars($it['location']) ?>
          </div>

          <span class="profile-status <?= strtolower($it['status']) ?>">
            <?= htmlspecialchars($it['status']) ?>
          </span>
        </div>
        <?php if ($it['status'] === 'RESERVED' && !empty($it['reserver_name'])): ?>
  <div class="mt-4 small text-warning ">
    <i class="bi bi-person-check"></i>
    Reserved by <?= htmlspecialchars($it['reserver_name']) ?>
  </div>
<?php endif; ?> 

        <!-- FOOTER -->
        <div class="card-footer">
          <a href="item.php?id=<?= (int)$it['id'] ?>" class="btn btn-outline-primary btn-sm">
            Details
          </a>
          <a href="edit_item.php?id=<?= (int)$it['id'] ?>"
       class="btn btn-outline-warning btn-sm">
      <i class="bi bi-pencil-square"></i> Edit
       </a>
            

          <form method="post" class="d-inline">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
            <input type="hidden" name="action" value="toggle_status">
            <?php if ($it['status'] !== 'RESOLVED'): ?>

  <form method="post" class="d-inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
    <input type="hidden" name="action" value="mark_resolved">

    <button class="btn btn-success btn-sm">
      <i class="bi bi-check-circle"></i> Resolved
    </button>
  </form>

<?php else: ?>

  <form method="post" class="d-inline">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
    <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
    <input type="hidden" name="action" value="cancel_resolved">

    <button class="btn btn-outline-warning btn-sm">
      <i class="bi bi-arrow-counterclockwise"></i> Cancel Resolved
    </button>
  </form>

<?php endif; ?>


          </form>

          <form method="post" class="d-inline ms-auto"
                onsubmit="return confirm('Are you sure you want to delete this listing?');">
            <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">
            <input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>">
            <input type="hidden" name="action" value="delete_item">
            <button class="btn btn-outline-danger btn-sm">Delete</button>
          </form>
        </div>

      </div>
    </div>
  <?php endforeach; ?>
</div>
</div>
<?php endif; ?>


<?php require_once __DIR__ . '/includes/footer.php'; ?>
