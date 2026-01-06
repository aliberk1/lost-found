
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/header.php';
logout_user();
header("Location: login.php");
exit;
require_once __DIR__.'/includes/footer.php';
