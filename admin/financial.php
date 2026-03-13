<?php
$pageTitle = 'Manage Financial Assistance';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    $id      = (int)($_POST['request_id'] ?? 0);
    $action  = $_POST['action'];
    $remarks = trim($_POST['remarks'] ?? '');
    if ($id && in_array($action, ['Approved','Denied','Pending'])) {
        $stmt = $pdo->prepare("UPDATE financial_requests SET status=?, admin_remarks=?, processed_by=?, processed_at=NOW() WHERE id=?");
        $stmt->execute([$action, $remarks, $_SESSION['user_id'], $id]);
        setFlash('success', "Financial request #$id marked as $action.");
    }
    header('Location: /admin/financial.php');
    exit;
}

$status  = $_GET['status'] ?? '';
$type    = $_GET['type']   ?? '';
$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;

$where  = ["1=1"];
$params = [];
if ($status) { $where[] = "fr.status = ?"; $params[] = $status; }
if ($type)   { $where[] = "fr.assistance_type = ?"; $params[] = $type; }
if ($search) { $where[] = "(u.fname LIKE ? OR u.lname LIKE ?)"; $params = array_merge($params, ["%$search%", "%$search%"]); }
$whereStr = implode(' AND ', $where);

$total = $pdo->prepare("SELECT COUNT(*) FROM financial_requests fr JOIN users u ON fr.user_id = u.id WHERE $whereStr");
$total->execute($params);
$pg = paginate($total->fetchColumn(), $perPage, $page);

$stmt = $pdo->prepare("SELECT fr.*, u.fname, u.lname, u.email, u.contact, u.address
    FROM financial_requests fr JOIN users u ON fr.user_id = u.id
    WHERE $whereStr ORDER BY fr.filed_at DESC LIMIT $perPage OFFSET {$pg['offset']}");
$stmt->execute($params);
$requests = $stmt->fetchAll();

$viewItem = null;
if (isset($_GET['view'])) {
    $vs = $pdo->prepare("SELECT fr.*, u.fname, u.lname, u.email, u.contact, u.address FROM financial_requests fr JOIN users u ON fr.user_id = u.id WHERE fr.id=?");
    $vs->execute([(int)$_GET['view']]);
    $viewItem = $vs->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Manage Financial Assistance</h1>
        <p class="page-header__breadcrumb"><a href="/admin/dashboard.php">Dashboard</a> → Financial Assistance</p>
    </div>
</div>

<section class="section--sm">
<div class="container">

<?php if ($viewItem): ?>
<div class="card mb-3">
    <div class="card__header">
        <h2 class="card__title">Financial Request #<?= $viewItem['id'] ?> — <?= sanitize($viewItem['assistance_type']) ?></h2>
        <?= statusBadge($viewItem['status']) ?>
    </div>
    <div class="card__body">
        <div class="grid-2 mb-3">
            <div><span class="form-label">Resident Name</span><p><?= sanitize($viewItem['fname'] . ' ' . $viewItem['lname']) ?></p></div>
            <div><span class="form-label">Contact</span><p><?= sanitize($viewItem['contact']) ?></p></div>
            <div><span class="form-label">Email</span><p><?= sanitize($viewItem['email']) ?></p></div>
            <div><span class="form-label">Address</span><p><?= sanitize($viewItem['address']) ?></p></div>
            <div><span class="form-label">Assistance Type</span><p><?= sanitize($viewItem['assistance_type']) ?></p></div>
            <div><span class="form-label">Amount Requested</span><p class="fw-600" style="color:var(--accent); font-size:18px;">₱<?= number_format($viewItem['amount_requested'],2) ?></p></div>
            <div style="grid-column:1/-1"><span class="form-label">Description</span><p style="white-space:pre-wrap;"><?= sanitize($viewItem['description']) ?></p></div>
            <?php if ($viewItem['supporting_details']): ?>
            <div style="grid-column:1/-1"><span class="form-label">Supporting Details</span><p><?= sanitize($viewItem['supporting_details']) ?></p></div>
            <?php endif; ?>
            <div><span class="form-label">Date Filed</span><p><?= formatDateTime($viewItem['filed_at']) ?></p></div>
            <?php $attachments = getAttachments('financial', $viewItem['id']); if (!empty($attachments)): ?>
            <div style="grid-column:1/-1"><?= renderAttachments($attachments) ?></div>
            <?php endif; ?>
        </div>
        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="request_id" value="<?= $viewItem['id'] ?>">
            <div class="form-group">
                <label class="form-label">Admin Remarks (Optional)</label>
                <textarea name="remarks" class="form-control" placeholder="Add notes..."><?= sanitize($viewItem['admin_remarks'] ?? '') ?></textarea>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" name="action" value="Approved" class="btn btn--success">✓ Approve</button>
                <button type="submit" name="action" value="Pending" class="btn btn--outline">↩ Set Pending</button>
                <button type="submit" name="action" value="Denied" class="btn btn--danger" data-confirm="Deny this application?">✕ Deny</button>
                <a href="/admin/financial.php" class="btn btn--outline" style="margin-left:auto;">← Back</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card mb-2">
    <div class="card__body" style="padding:16px 24px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <div class="form-group" style="margin:0; flex:1; min-width:180px;">
                <label class="form-label">Search Resident</label>
                <input type="text" name="search" class="form-control" placeholder="Name..." value="<?= sanitize($search) ?>">
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach (['Medical','Burial','Calamity','Others'] as $t): ?>
                        <option value="<?= $t ?>" <?= $type===$t?'selected':'' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <?php foreach (['Pending','Approved','Denied'] as $s): ?>
                        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn--primary">Filter</button>
            <a href="/admin/financial.php" class="btn btn--outline">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card__header">
        <h2 class="card__title">Financial Assistance Applications <span style="font-size:13px; color:var(--text-muted); font-weight:400;">(<?= $pg['total'] ?> total)</span></h2>
    </div>
    <div class="card__body" style="padding:0;">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>#</th><th>Resident</th><th>Type</th><th>Amount</th><th>Date Filed</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-state__title">No applications found</div></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td>
                            <div class="fw-600"><?= sanitize($r['fname'] . ' ' . $r['lname']) ?></div>
                            <div style="font-size:12px;color:var(--text-muted);"><?= sanitize($r['email']) ?></div>
                        </td>
                        <td><?= sanitize($r['assistance_type']) ?></td>
                        <td class="fw-600">₱<?= number_format($r['amount_requested'],2) ?></td>
                        <td><?= formatDate($r['filed_at']) ?></td>
                        <td><?= statusBadge($r['status']) ?></td>
                        <td><a href="?view=<?= $r['id'] ?>" class="btn btn--outline btn--sm">View</a></td>
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
            <?php if ($pg['has_prev']): ?><a href="?page=<?= $page-1 ?>&status=<?= urlencode($status) ?>&type=<?= urlencode($type) ?>&search=<?= urlencode($search) ?>">← Prev</a><?php endif; ?>
            <?php for ($i=1;$i<=$pg['total_pages'];$i++): ?><?php if($i===$page): ?><span class="current"><?= $i ?></span><?php else: ?><a href="?page=<?= $i ?>&status=<?= urlencode($status) ?>&type=<?= urlencode($type) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a><?php endif; ?><?php endfor; ?>
            <?php if ($pg['has_next']): ?><a href="?page=<?= $page+1 ?>&status=<?= urlencode($status) ?>&type=<?= urlencode($type) ?>&search=<?= urlencode($search) ?>">Next →</a><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
