<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/messages.php';
require_login();

$page_title = 'My Conversations';
require_once __DIR__ . '/includes/header.php';

$me = (int)$_SESSION['uid'];

$list = $pdo->prepare("
  SELECT c.*,
         CASE WHEN c.user_a = ? THEN c.user_b ELSE c.user_a END AS other_id,
         (SELECT name FROM users WHERE id = CASE WHEN c.user_a = ? THEN c.user_b ELSE c.user_a END) AS other_name,
         (SELECT title FROM items WHERE id = c.item_id) AS item_title,
         (SELECT body FROM messages WHERE conversation_id=c.id ORDER BY created_at DESC LIMIT 1) AS last_body,
         (SELECT created_at FROM messages WHERE conversation_id=c.id ORDER BY created_at DESC LIMIT 1) AS last_time,
         (SELECT COUNT(*) FROM messages WHERE conversation_id=c.id AND sender_id<>? AND read_at IS NULL) AS unread_count
  FROM conversations c
  WHERE c.user_a = ? OR c.user_b = ?
  ORDER BY c.updated_at DESC
");
$list->execute([$me, $me, $me, $me, $me]);
$convs = $list->fetchAll();
?>

<style>
/* ===== PAGE BACKGROUND ===== */
body {
  background: #f4f6fb;
}

/* ===== CHAT CARD ===== */
.chat-card {
  display: flex;
  gap: 14px;
  padding: 14px 16px;
  border-radius: 14px;
  background: #ffffff;
  box-shadow: 0 6px 18px rgba(0,0,0,.05);
  transition: all .2s ease;
  text-decoration: none;
  color: inherit;
}

.chat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 12px 26px rgba(0,0,0,.08);
}

/* unread (same color – lighter) */
.chat-card.unread {
  background: #eef2ff;
}

/* ===== AVATAR ===== */
.chat-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: #2563eb;
  color: #fff;
  font-weight: 600;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

/* ===== CONTENT ===== */
.chat-content {
  flex: 1;
}

.chat-name {
  font-weight: 600;
  font-size: 15px;
  color: #0f172a;
}

.chat-item {
  font-size: 13px;
  color: #64748b;
}

.chat-last {
  font-size: 14px;
  color: #334155;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 60ch;
}

/* ===== META ===== */
.chat-meta {
  text-align: right;
  min-width: 70px;
}

.chat-time {
  font-size: 12px;
  color: #94a3b8;
}

.chat-unread {
  margin-top: 6px;
  background: #2563eb;
  color: #fff;
  font-size: 11px;
  padding: 4px 8px;
  border-radius: 999px;
  display: inline-block;
}
</style>

<div class="d-flex align-items-center justify-content-between mb-4">
  <h1 class="h5 mb-0">💬 Conversations</h1>
  <a class="btn btn-outline-secondary btn-sm" href="index.php">
    <i class="bi bi-house"></i> Home
  </a>
</div>

<?php if (!$convs): ?>
  <div class="d-flex justify-content-center mt-5">
    <div class="text-center p-5 rounded-4"
         style="
           background:#ffffff;
           box-shadow:0 10px 30px rgba(0,0,0,.06);
           max-width:420px;
         ">
      <div style="font-size:46px;">💬</div>
      <h5 class="mt-3 mb-1">No conversations yet</h5>
      <p class="text-muted mb-3">
        When you start messaging listing owners, your conversations will appear here.
      </p>
      <a href="index.php" class="btn btn-primary btn-sm">
        Browse Listings
      </a>
    </div>
  </div>
<?php else: ?>

<div class="d-flex flex-column gap-3">
  <?php foreach ($convs as $c): 
    $initial = strtoupper(mb_substr($c['other_name'] ?? 'U', 0, 1));
    $unread = (int)$c['unread_count'] > 0;
  ?>
    <a href="messages.php?id=<?= (int)$c['id'] ?>"
       class="chat-card <?= $unread ? 'unread' : '' ?>">

      <div class="chat-avatar">
        <?= htmlspecialchars($initial) ?>
      </div>

      <div class="chat-content">
        <div class="chat-name">
          <?= htmlspecialchars($c['other_name'] ?? ('User #'.$c['other_id'])) ?>
        </div>

        <?php if (!empty($c['item_title'])): ?>
          <div class="chat-item">
            📦 <?= htmlspecialchars($c['item_title']) ?>
          </div>
        <?php endif; ?>

        <div class="chat-last">
          <?= htmlspecialchars($c['last_body'] ?? '') ?>
        </div>
      </div>

      <div class="chat-meta">
        <?php if (!empty($c['last_time'])): ?>
          <div class="chat-time">
            <?= htmlspecialchars(date('d.m H:i', strtotime($c['last_time']))) ?>
          </div>
        <?php endif; ?>

        <?php if ($unread): ?>
          <div class="chat-unread">
            <?= (int)$c['unread_count'] ?> new
          </div>
        <?php endif; ?>
      </div>

    </a>
  <?php endforeach; ?>
</div>

<?php endif; ?>
