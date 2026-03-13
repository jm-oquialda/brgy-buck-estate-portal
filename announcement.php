<?php
$pageTitle = 'Announcement';
require_once __DIR__ . '/includes/functions.php';
startSession();
$pdo = getDB();
$id  = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: /announcements.php'); exit; }

$stmt = $pdo->prepare("SELECT a.*, u.fname, u.lname FROM announcements a LEFT JOIN users u ON a.posted_by=u.id WHERE a.id=? AND a.status='published'");
$stmt->execute([$id]);
$ann = $stmt->fetch();
if (!$ann) { header('Location: /announcements.php'); exit; }

$pageTitle = $ann['title'];
require_once __DIR__ . '/includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <p class="page-header__breadcrumb"><a href="/index.php">Home</a> → <a href="/announcements.php">Announcements</a> → <?= sanitize(substr($ann['title'],0,40)) ?></p>
    </div>
</div>
<section class="section--sm">
    <div class="container" style="max-width:800px;">
        <div class="announce-detail">
            <h1 class="announce-detail__title"><?= sanitize($ann['title']) ?></h1>
            <div class="announce-detail__meta">
                <span>📅 <?= formatDateTime($ann['created_at']) ?></span>
                <span>✍️ <?= sanitize($ann['fname'] . ' ' . $ann['lname']) ?></span>
            </div>
            <div class="announce-detail__content"><?= nl2br(sanitize($ann['content'])) ?></div>
        </div>
        <div class="mt-3">
            <a href="/announcements.php" class="btn btn--outline">← Back to Announcements</a>
        </div>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
