<?php
$pageTitle = 'Announcements';
require_once __DIR__ . '/includes/functions.php';
startSession();
$pdo  = getDB();
$page = max(1,(int)($_GET['page'] ?? 1));
$perPage = 9;

$total = $pdo->query("SELECT COUNT(*) FROM announcements WHERE status='published'")->fetchColumn();
$pg    = paginate($total, $perPage, $page);

$stmt  = $pdo->prepare("SELECT a.*, u.fname, u.lname FROM announcements a LEFT JOIN users u ON a.posted_by=u.id WHERE a.status='published' ORDER BY a.created_at DESC LIMIT $perPage OFFSET {$pg['offset']}");
$stmt->execute();
$announcements = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Announcements</h1>
        <p class="page-header__breadcrumb"><a href="/index.php">Home</a> → Announcements</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (empty($announcements)): ?>
            <div class="empty-state">
                <div class="empty-state__icon">📢</div>
                <div class="empty-state__title">No announcements yet</div>
                <div class="empty-state__desc">Check back later for updates from the barangay.</div>
            </div>
        <?php else: ?>
            <div class="announce-grid">
                <?php foreach ($announcements as $a): ?>
                <div class="announce-card">
                    <div class="announce-card__body">
                        <div class="announce-card__date">📅 <?= formatDate($a['created_at'], 'F j, Y') ?></div>
                        <h3 class="announce-card__title"><?= sanitize($a['title']) ?></h3>
                        <p class="announce-card__excerpt"><?= sanitize($a['content']) ?></p>
                    </div>
                    <div class="announce-card__footer">
                        <a href="/announcement.php?id=<?= $a['id'] ?>" class="announce-card__link">Read More →</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($pg['total_pages'] > 1): ?>
            <div class="pagination" style="margin-top:32px;">
                <?php if ($pg['has_prev']): ?><a href="?page=<?= $page-1 ?>">← Prev</a><?php endif; ?>
                <?php for ($i=1;$i<=$pg['total_pages'];$i++): ?>
                    <?php if($i===$page): ?><span class="current"><?= $i ?></span>
                    <?php else: ?><a href="?page=<?= $i ?>"><?= $i ?></a><?php endif; ?>
                <?php endfor; ?>
                <?php if ($pg['has_next']): ?><a href="?page=<?= $page+1 ?>">Next →</a><?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
