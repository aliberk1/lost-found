



<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/messages.php';
require_login();

$convId = (int)($_GET['id'] ?? 0);
$page_title = 'Messages';
require_once __DIR__ . '/includes/header.php';

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

// Load conversation + permission check
$conv = $pdo->prepare("SELECT * FROM conversations WHERE id=? LIMIT 1");
$conv->execute([$convId]);
$conv = $conv->fetch();
if (!$conv) { echo '<div class="alert alert-danger mt-3">No conversation found.</div>'; require_once __DIR__.'/includes/footer.php'; exit; }

$me = (int)$_SESSION['uid'];
if ($me !== (int)$conv['user_a'] && $me !== (int)$conv['user_b']) {
  http_response_code(403); echo '<div class="alert alert-danger mt-3">You do not have permission to view this conversation.</div>'; require_once __DIR__.'/includes/footer.php'; exit;
}

// Get the other party and (if any) the item title
$otherId = other_party($conv, $me);
$other = $pdo->prepare("SELECT name FROM users WHERE id=?");
$other->execute([$otherId]); $other = $other->fetch();

$item = null;
if (!empty($conv['item_id'])) {
  $s = $pdo->prepare("SELECT id, title FROM items WHERE id=?");
  $s->execute([$conv['item_id']]);
  $item = $s->fetch();
}

// Send message
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
  if (($_POST['csrf'] ?? '') !== ($_SESSION['csrf'] ?? '')) {
    $errors[] = 'CSRF error.';
  } else {
    $body = trim($_POST['body'] ?? '');
    if (mb_strlen($body) < 1) {
      $errors[] = 'Message cannot be empty.';
    } else {
      $ins = $pdo->prepare("INSERT INTO messages (conversation_id, sender_id, body) VALUES (?, ?, ?)");
      $ins->execute([$convId, $me, $body]);
      // update conversations.updated_at
      $pdo->prepare("UPDATE conversations SET updated_at = NOW() WHERE id=?")->execute([$convId]);
      // Redirect after send so the form appears cleared
      header("Location: messages.php?id=".$convId);
      exit;
    }
  }
}

// Fetch messages (newest at bottom)
$msgs = $pdo->prepare("
  SELECT m.*, u.name AS sender_name
  FROM messages m JOIN users u ON u.id = m.sender_id
  WHERE m.conversation_id = ?
  ORDER BY m.created_at ASC
");
$msgs->execute([$convId]); $msgs = $msgs->fetchAll();

// Mark as read (for messages sent by the other party)
$mark = $pdo->prepare("UPDATE messages SET read_at=NOW() WHERE conversation_id=? AND sender_id<>? AND read_at IS NULL");
$mark->execute([$convId, $me]);
?>
<div class="chat-header mb-3">
  <div>
    <div class="fw-semibold">
      <?= htmlspecialchars($other['name']) ?>
    </div>

    <?php if ($item): ?>
      <div class="small text-muted">
        Listing:
        <a href="item.php?id=<?= (int)$item['id'] ?>" class="text-decoration-none">
          <?= htmlspecialchars($item['title']) ?>
        </a>
      </div>
    <?php endif; ?>
  </div>

  <a class="btn btn-outline-secondary btn-sm" href="messages_list.php">
    <i class="bi bi-arrow-left"></i>
  </a>
</div>



<?php if ($errors): ?>
  <div class="alert alert-danger"><?php foreach($errors as $e) echo '<div>'.htmlspecialchars($e).'</div>'; ?></div>
<?php endif; ?>

<div class="card shadow-sm mb-3 chat-card">
  <div class="card-body chat-body" id="chatBody">

    <?php if (!$msgs): ?>
      <div class="text-muted text-center mt-5">
        No messages yet. Send the first message.
      </div>
    <?php else: ?>
      <?php foreach ($msgs as $m):
        $mine = ((int)$m['sender_id'] === $me);
      ?>
        <div class="d-flex mb-2 <?= $mine ? 'justify-content-end' : 'justify-content-start' ?>">
          <div class="chat-bubble <?= $mine ? 'mine' : 'theirs' ?>">
            <div class="chat-text">
              <?= nl2br(htmlspecialchars($m['body'])) ?>
            </div>

            <div class="chat-time">
              <?= htmlspecialchars(date('d.m.Y H:i', strtotime($m['created_at']))) ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

  </div>
</div>



<form method="post" class="chat-input d-flex gap-2">
  <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf']) ?>">

  <textarea
    name="body"
    class="form-control chat-textarea"
    rows="1"
    placeholder="Write a message..."
    required></textarea>

  <button class="btn btn-primary" type="submit" name="send" value="1">
    <i class="bi bi-send"></i>
  </button>
</form>
<style> 
/* HEADER */
.chat-header{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding-bottom:6px;
}

/* CARD */
.chat-card{
  border-radius:16px;
}

/* BODY */
.chat-body{
  height:48vh;           /* 🔥 abartı yok */
  overflow-y:auto;
  background:#F8FAFC;
}

/* BUBBLE */
.chat-bubble{
  max-width:65%;
  padding:10px 14px;
  border-radius:14px;
  font-size:14px;
}

/* MY MESSAGE */
.chat-bubble.mine{
  background:#2563EB;
  color:#fff;
  border-bottom-right-radius:6px;
}

/* OTHER MESSAGE */
.chat-bubble.theirs{
  background:#fff;
  border:1px solid #E5E7EB;
  color:#0F172A;
  border-bottom-left-radius:6px;
}

/* TIME */
.chat-time{
  font-size:11px;
  opacity:.6;
  margin-top:4px;
  text-align:right;
}

/* INPUT */
.chat-input{
  align-items:flex-end;
}

.chat-textarea{
  resize:none;
  border-radius:12px;
}

<script>
  const chatBody = document.getElementById('chatBody');
  if (chatBody) {
    chatBody.scrollTop = chatBody.scrollHeight;
  }

  document.addEventListener("DOMContentLoaded", function () {
    const textarea = document.querySelector(".chat-textarea");
    const form = textarea?.closest("form");

    if (!textarea || !form) return;

    textarea.addEventListener("keydown", function (e) {
      // Enter → gönder
      if (e.key === "Enter" && !e.shiftKey) {
        e.preventDefault();

        // boş mesajı gönderme
        if (textarea.value.trim().length > 0) {
          form.submit();
        }
      }
    });
  });


</script>


