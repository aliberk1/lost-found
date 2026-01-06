<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/messages.php';
require_login();

$to = (int)($_GET['to'] ?? 0);       // mesaj atılacak kullanıcı
$itemId = isset($_GET['item_id']) ? (int)$_GET['item_id'] : null;

if ($to <= 0 || $to === (int)$_SESSION['uid']) {
  http_response_code(400); exit('Invalid target.');
}

// Kullanıcı var mı?
$chk = $pdo->prepare("SELECT id FROM users WHERE id=? LIMIT 1");
$chk->execute([$to]);
if (!$chk->fetch()) { http_response_code(404); exit('User not found'); }

$convId = get_or_create_conversation($pdo, (int)$_SESSION['uid'], $to, $itemId);
header("Location: messages.php?id=".$convId);
exit;
