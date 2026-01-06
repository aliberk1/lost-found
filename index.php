<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

$page_title = 'Listings';
require_once __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');
$category = $_GET['category'] ?? '';
$allowedCats = ['Elektronik','Kimlik','Cüzdan','Anahtar','Giyim','Diğer'];

$catTranslations = [
  'Elektronik' => 'Electronics',
  'Kimlik' => 'ID',
  'Cüzdan' => 'Wallet',
  'Anahtar' => 'Keys',
  'Giyim' => 'Clothing',
  'Diğer' => 'Other',
];

$sql = "
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
WHERE i.approved = 1
";


$params = [];

if ($q !== '') {
  $sql .= " AND (i.title LIKE ? OR i.description LIKE ? OR i.location LIKE ?)";
  $like = "%$q%";
  $params[] = $like;
  $params[] = $like;
  $params[] = $like;
}

if ($category !== '' && in_array($category, $allowedCats, true)) {
  $sql .= " AND i.category = ?";
  $params[] = $category;
}

$sql .= " ORDER BY i.created_at DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>

<style>
body {
  font-family: 'Inter', sans-serif;
  background-color: #F8FAFC;
}

/* HERO */
.hero-section {
  background: linear-gradient(135deg, #F8FAFC, #EEF2FF);
  border-radius: 24px;
  padding: 70px 0;
  margin-bottom: 50px;
}
/* HERO IMAGE WRAPPER */
.hero-image-wrapper {
  width: 100%;
  max-width: 560px;              /* masaüstü sınır */
  aspect-ratio: 16 / 10;         /* 🔥 ORAN SABİT */
  background: linear-gradient(135deg, #E0E7FF, #F8FAFC);
  border-radius: 24px;
  padding: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 30px 60px rgba(0,0,0,.12);
  animation: float 6s ease-in-out infinite;
}
@keyframes heroFloat {
  0%   { transform: translateY(0); }
  50%  { transform: translateY(-8px); }
  100% { transform: translateY(0); }
}

/* HERO IMAGE */
.hero-image {
  width: 100%;
  height: 100%;
  object-fit: contain;      /* 🔥 kırpma yok */
  border-radius: 25px;

  animation: heroFloat 4.5s ease-in-out infinite;
}


.hero-title {
  font-size: 44px;
  font-weight: 700;
  line-height: 1.2;
  color: #0F172A;
}

.hero-text {
  font-size: 18px;
  color: #475569;
  max-width: 520px;
}



/* SEARCH */
.search-card {
  border-radius: 16px;
  border: none;
  box-shadow: 0 10px 25px rgba(0,0,0,.05);
}

.search-card input,
.search-card select {
  padding: 14px 16px;
  border-radius: 12px;
  border: 1px solid #E2E8F0;
}

/* ITEM CARD */
.item-card {
  border: none;
  border-radius: 18px;
   background: linear-gradient(135deg, #ffffffff, #F8FAFC);
  box-shadow: 0 8px 20px rgba(0,0,0,.06);
  transition: all .25s ease;
}

.item-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 25px 45px rgba(255, 0, 0, 0.12);
}

.item-date {
  font-size: 12px;
  color: #000000ff;
}

.item-title {
  font-weight: 600;
  font-size: 18px;
  color: #0F172A;
}

.item-meta {
  font-size: 14px;
  color: #475569;
}

.status-badge {
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
}

.badge-open {
  background: rgba(22,163,74,.15);
  color: #166534;
}

.badge-reserved {
  background: rgba(245,158,11,.2);
  color: #92400E;
}

.badge-resolved {
  background: rgba(100,116,139,.2);
  color: #334155;
}

.detail-btn {
  border-radius: 12px;
  font-weight: 500;
  padding: 10px;
}


.btn {
  transition: all 0.25s ease;
}

.btn:hover {
  transform: translateY(-2px);
}
/* THUMBNAIL (Kart üst görsel alanı) */
.item-thumb{
  width: 100%;
  height: 160px;
  background: #f1f1f1
  border-radius: 18px 18px 0 0;
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.item-thumb img{
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;   /* 🔥 KRİTİK */
}


.thumb-placeholder{
  width: 100%;
  height: 100%;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#64748B;
  font-size: 34px;
}
.item-card{
  max-width: 400px;     /* 🔥 kart sınırı */
  margin: auto;
 
}


</style>

<!-- HERO SECTION -->
<section class="hero-section">
  <div class="container">
    <div class="row align-items-center">

      <div class="col-lg-6">
        <span class="bg-primary-subtle ">
        
        </span>

        <h1 class="hero-title mt-3 fade-up">
          Let lost items <br>
          <span class="text-primary">reach their rightful owner</span>
        </h1>

        <p class="hero-text mt-3 fade-up">
          Post lost or found items and easily reach the owner via secure messaging.
        </p>

        <div class="d-flex gap-3 mt-4 fade-up">
          <a href="/lost_found/post.php" class="btn btn-primary btn-lg">
            ➕ Add Listing
          </a>
          <a href="#ilanlar" class="btn btn-outline-secondary btn-lg">
            🔍 Browse Listings
          </a>
        </div>
      </div>
<div class="col-lg-6 d-none d-lg-block">
  <div class="hero-image-wrapper">
    <img src="images/her55.png"
         alt="Lost and Found"
         class="hero-image">
  </div>
</div>
</section>

<!-- LIST TITLE -->
<div id="ilanlar" class="mb-4">
  <h3 class="fw-semibold">📦 Recent Listings</h3>
  <p class="text-muted">
    Discover the latest lost & found listings
  </p>
</div>

<!-- SEARCH / FILTER -->
<div class="card search-card mb-4">
  <div class="card-body">
    <form class="row g-3 align-items-center" method="get">

      <div class="col-md-6">
        <input type="text" name="q" class="form-control"
               placeholder="🔍 Search title, description or location"
               value="<?= htmlspecialchars($q) ?>">
      </div>

      <div class="col-md-4">
        <select class="form-select" name="category">
          <option value="">All Categories</option>
          <?php foreach ($allowedCats as $c): ?>
            <?php $label = $catTranslations[$c] ?? $c; ?>
            <option value="<?= htmlspecialchars($c) ?>" <?= $category === $c ? 'selected' : '' ?>>
              <?= htmlspecialchars($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-md-2 d-grid">
        <button class="btn btn-primary btn-lg" type="submit">
          <i class="bi bi-search"></i> Filter
        </button>
      </div>

    </form>
  </div>
</div>

<?php if (!$rows): ?>
  <div class="alert alert-secondary text-center">
    No listings match the filter.
  </div>
<?php else: ?>

<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
  <?php foreach ($rows as $r): ?>
    <div class="col">
      <div class="card item-card h-100">
       <div class="item-thumb">
  <?php if (!empty($r['image_path'])): ?>
    <img src="<?= htmlspecialchars($r['image_path']) ?>" alt="İlan görseli">
  <?php else: ?>
    <div class="thumb-placeholder">
      <i class="bi bi-image"></i>
    </div>
  <?php endif; ?>
</div>


        <div class="card-body">
          <div class="item-date">
            <?= htmlspecialchars(date('d.m.Y H:i', strtotime($r['created_at']))) ?>
          </div>

          <div class="item-title mt-1">
            <?= htmlspecialchars($r['title']) ?>
          </div>

          <div class="item-meta mt-1">
            🏷️ <?= htmlspecialchars($catTranslations[$r['category']] ?? $r['category']) ?>
            &nbsp; • &nbsp;
            📍 <?= htmlspecialchars($r['location']) ?>
          </div>

          <div class="mt-3">
            <?php if ($r['status'] === 'OPEN'): ?>
              <span class="status-badge badge-open">OPEN</span>
            <?php elseif ($r['status'] === 'RESERVED'): ?>
              <span class="status-badge badge-reserved">RESERVED</span>
            <?php else: ?>
              <span class="status-badge badge-resolved">RESOLVED</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="card-footer bg-white border-0">
          <a href="item.php?id=<?= (int)$r['id'] ?>"
             class="btn btn-primary w-100 detail-btn">
            View Details →
          </a>
        </div>

      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php endif; ?>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const items = document.querySelectorAll('.fade-up');

  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('show');
      }
    });
  }, { threshold: 0.2 });

  items.forEach(item => observer.observe(item));
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
