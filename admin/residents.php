<?php
$pageTitle = 'Manage Residents';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pdo = getDB();

// Toggle status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_id'])) {
    verifyCsrf();
    $id = (int)$_POST['toggle_id'];
    $pdo->prepare("UPDATE users SET status = CASE WHEN status='active' THEN 'inactive' ELSE 'active' END WHERE id=? AND role='resident'")->execute([$id]);
    setFlash('success', 'Resident status updated.');
    header('Location: /admin/residents.php'); exit;
}

$search  = trim($_GET['search'] ?? '');
$page    = max(1,(int)($_GET['page'] ?? 1));
$perPage = 20;

$where  = ["role = 'resident'"];
$params = [];
if ($search) {
    $where[] = "(fname LIKE ? OR lname LIKE ? OR email LIKE ?)";
    $params  = ["%$search%", "%$search%", "%$search%"];
}
$whereStr = implode(' AND ', $where);

$total = $pdo->prepare("SELECT COUNT(*) FROM users WHERE $whereStr");
$total->execute($params);
$pg = paginate($total->fetchColumn(), $perPage, $page);

$stmt = $pdo->prepare("SELECT * FROM users WHERE $whereStr ORDER BY created_at DESC LIMIT $perPage OFFSET {$pg['offset']}");
$stmt->execute($params);
$residents = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Manage Residents</h1>
        <p class="page-header__breadcrumb"><a href="/admin/dashboard.php">Dashboard</a> → Residents</p>
    </div>
</div>

<section class="section--sm">
<div class="container">
    <div class="card mb-2">
        <div class="card__body" style="padding:16px 24px;">
            <form method="GET" style="display:flex; gap:10px; align-items:flex-end;">
                <div class="form-group" style="margin:0; flex:1;">
                    <label class="form-label">Search Resident</label>
                    <input type="text" name="search" class="form-control" placeholder="Name or email..." value="<?= sanitize($search) ?>">
                </div>
                <button type="submit" class="btn btn--primary">Search</button>
                <a href="/admin/residents.php" class="btn btn--outline">Reset</a>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card__header">
            <h2 class="card__title">Registered Residents <span style="font-size:13px; color:var(--text-muted); font-weight:400;">(<?= $pg['total'] ?> total)</span></h2>
        </div>
        <div class="card__body" style="padding:0;">
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Contact</th><th>Date Registered</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php if (empty($residents)): ?>
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-state__title">No residents found</div></div></td></tr>
                        <?php else: ?>
                        <?php foreach ($residents as $i => $r): ?>
                        <tr>
                            <td><?= $pg['offset'] + $i + 1 ?></td>
                            <td class="fw-600"><?= sanitize($r['fname'] . ' ' . $r['lname']) ?></td>
                            <td><?= sanitize($r['email']) ?></td>
                            <td><?= sanitize($r['contact']) ?></td>
                            <td><?= formatDate($r['created_at']) ?></td>
                            <td><?= statusBadge($r['status']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                                    <input type="hidden" name="toggle_id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn--sm <?= $r['status']==='active'?'btn--danger':'btn--success' ?>"
                                            data-confirm="<?= $r['status']==='active'?'Deactivate':'Activate' ?> this account?">
                                        <?= $r['status']==='active' ? 'Deactivate' : 'Activate' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($pg['total_pages'] > 1): ?>
        <div class="card__footer">
            <div class="pagination">
                <?php if ($pg['has_prev']): ?><a href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">← Prev</a><?php endif; ?>
                <?php for ($i=1;$i<=$pg['total_pages'];$i++): ?><?php if($i===$page): ?><span class="current"><?= $i ?></span><?php else: ?><a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a><?php endif; ?><?php endfor; ?>
                <?php if ($pg['has_next']): ?><a href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Next →</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
