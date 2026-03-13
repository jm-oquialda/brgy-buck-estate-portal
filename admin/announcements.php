<?php
$pageTitle = 'Manage Announcements';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pdo = getDB();

// Delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verifyCsrf();
    $id = (int)$_POST['ann_id'];
    if ($id) { $pdo->prepare("DELETE FROM announcements WHERE id=?")->execute([$id]); }
    setFlash('success', 'Announcement deleted.');
    header('Location: /admin/announcements.php'); exit;
}

// Create / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action'] ?? '', ['create','update'])) {
    verifyCsrf();
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status  = in_array($_POST['status'] ?? '', ['published','draft']) ? $_POST['status'] : 'published';
    $errors  = [];
    if (empty($title))   $errors[] = 'Title is required.';
    if (empty($content)) $errors[] = 'Content is required.';

    if (empty($errors)) {
        if ($_POST['action'] === 'update' && isset($_POST['ann_id'])) {
            $stmt = $pdo->prepare("UPDATE announcements SET title=?, content=?, status=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$title, $content, $status, (int)$_POST['ann_id']]);
            setFlash('success', 'Announcement updated.');
        } else {
            $stmt = $pdo->prepare("INSERT INTO announcements (title, content, status, posted_by) VALUES (?,?,?,?)");
            $stmt->execute([$title, $content, $status, $_SESSION['user_id']]);
            setFlash('success', 'Announcement published.');
        }
        header('Location: /admin/announcements.php'); exit;
    }
}

// Edit fetch
$editItem = null;
if (isset($_GET['edit'])) {
    $es = $pdo->prepare("SELECT * FROM announcements WHERE id=?");
    $es->execute([(int)$_GET['edit']]);
    $editItem = $es->fetch();
}

$page    = max(1,(int)($_GET['page'] ?? 1));
$perPage = 10;
$total   = $pdo->query("SELECT COUNT(*) FROM announcements")->fetchColumn();
$pg      = paginate($total, $perPage, $page);

$anns = $pdo->prepare("SELECT a.*, u.fname, u.lname FROM announcements a LEFT JOIN users u ON a.posted_by=u.id ORDER BY a.created_at DESC LIMIT $perPage OFFSET {$pg['offset']}");
$anns->execute();
$announcements = $anns->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Manage Announcements</h1>
        <p class="page-header__breadcrumb"><a href="/admin/dashboard.php">Dashboard</a> → Announcements</p>
    </div>
</div>

<section class="section--sm">
<div class="container">
    <div class="grid-sidebar">
        <div>
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">All Announcements</h2>
                    <a href="/admin/announcements.php" class="btn btn--primary btn--sm">+ New</a>
                </div>
                <div class="card__body" style="padding:0;">
                    <div class="table-wrap">
                        <table class="table">
                            <thead><tr><th>Title</th><th>Posted By</th><th>Date</th><th>Status</th><th>Actions</th></tr></thead>
                            <tbody>
                                <?php if (empty($announcements)): ?>
                                <tr><td colspan="5"><div class="empty-state"><div class="empty-state__title">No announcements yet</div></div></td></tr>
                                <?php else: ?>
                                <?php foreach ($announcements as $a): ?>
                                <tr>
                                    <td class="fw-600"><?= sanitize(substr($a['title'],0,50)) . (strlen($a['title'])>50?'…':'') ?></td>
                                    <td><?= sanitize($a['fname'] . ' ' . $a['lname']) ?></td>
                                    <td><?= formatDate($a['created_at']) ?></td>
                                    <td><?= statusBadge($a['status']) ?></td>
                                    <td>
                                        <a href="?edit=<?= $a['id'] ?>" class="btn btn--outline btn--sm">Edit</a>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="ann_id" value="<?= $a['id'] ?>">
                                            <button type="submit" class="btn btn--danger btn--sm" data-confirm="Delete this announcement?">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CREATE / EDIT FORM -->
        <div>
            <div class="card">
                <div class="card__header">
                    <h3 class="card__title"><?= $editItem ? 'Edit Announcement' : 'New Announcement' ?></h3>
                </div>
                <div class="card__body">
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="<?= $editItem ? 'update' : 'create' ?>">
                        <?php if ($editItem): ?>
                            <input type="hidden" name="ann_id" value="<?= $editItem['id'] ?>">
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="form-label">Title <span class="required">*</span></label>
                            <input type="text" name="title" class="form-control" value="<?= sanitize($editItem['title'] ?? '') ?>" placeholder="Announcement title">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Content <span class="required">*</span></label>
                            <textarea name="content" class="form-control" style="min-height:160px;" placeholder="Write the full announcement here..."><?= sanitize($editItem['content'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="published" <?= ($editItem['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
                                <option value="draft" <?= ($editItem['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                            </select>
                        </div>
                        <div style="display:flex; gap:8px;">
                            <button type="submit" class="btn btn--primary btn--block"><?= $editItem ? 'Update Announcement' : 'Publish Announcement' ?></button>
                            <?php if ($editItem): ?><a href="/admin/announcements.php" class="btn btn--outline">Cancel</a><?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
