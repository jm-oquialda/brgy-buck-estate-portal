<?php
$pageTitle = 'Manage Document Requests';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pdo = getDB();

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    $id      = (int)($_POST['request_id'] ?? 0);
    $action  = $_POST['action'];
    $remarks = trim($_POST['remarks'] ?? '');
    $allowed = ['Approved', 'Denied', 'Pending', 'Processing'];
    if ($id && in_array($action, $allowed)) {
        $stmt = $pdo->prepare("UPDATE document_requests SET status=?, admin_remarks=?, processed_by=?, processed_at=NOW() WHERE id=?");
        $stmt->execute([$action, $remarks, $_SESSION['user_id'], $id]);

        // Notify resident
        $req = $pdo->prepare("SELECT user_id, doc_type FROM document_requests WHERE id=?");
        $req->execute([$id]);
        $reqData = $req->fetch();
        if ($reqData) {
            $msgs = [
                'Approved'   => 'Your ' . $reqData['doc_type'] . ' request has been approved. Please visit the Barangay Hall to claim your document.',
                'Denied'     => 'Your ' . $reqData['doc_type'] . ' request has been denied.' . ($remarks ? ' Remarks: ' . $remarks : ''),
                'Processing' => 'Your ' . $reqData['doc_type'] . ' request is now being processed.',
                'Pending'    => 'Your ' . $reqData['doc_type'] . ' request has been set back to pending.',
            ];
            createNotification(
                $reqData['user_id'],
                $action === 'Approved' ? 'success' : ($action === 'Denied' ? 'error' : 'info'),
                'Document Request #' . $id . ' — ' . $action,
                $msgs[$action] ?? 'Your document request status has been updated.',
                '/resident/document-request.php?id=' . $id
            );
        }

        setFlash('success', "Request #$id has been marked as $action.");
    }
    header('Location: /admin/document-requests.php');
    exit;
}

// Filters
$status   = $_GET['status']   ?? '';
$type     = $_GET['type']     ?? '';
$search   = trim($_GET['search'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 15;

$where = ["1=1"];
$params = [];
if ($status) { $where[] = "dr.status = ?"; $params[] = $status; }
if ($type)   { $where[] = "dr.doc_type = ?"; $params[] = $type; }
if ($search) { $where[] = "(u.fname LIKE ? OR u.lname LIKE ? OR u.email LIKE ?)"; $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]); }
$whereStr = implode(' AND ', $where);

$total = $pdo->prepare("SELECT COUNT(*) FROM document_requests dr JOIN users u ON dr.user_id = u.id WHERE $whereStr");
$total->execute($params);
$pg = paginate($total->fetchColumn(), $perPage, $page);

$stmt = $pdo->prepare("SELECT dr.*, u.fname, u.lname, u.email, u.contact, u.address
    FROM document_requests dr JOIN users u ON dr.user_id = u.id
    WHERE $whereStr ORDER BY dr.requested_at DESC LIMIT $perPage OFFSET {$pg['offset']}");
$stmt->execute($params);
$requests = $stmt->fetchAll();

// Single view
$viewItem = null;
if (isset($_GET['view'])) {
    $vs = $pdo->prepare("SELECT dr.*, u.fname, u.lname, u.email, u.contact, u.address FROM document_requests dr JOIN users u ON dr.user_id = u.id WHERE dr.id = ?");
    $vs->execute([(int)$_GET['view']]);
    $viewItem = $vs->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Manage Document Requests</h1>
        <p class="page-header__breadcrumb"><a href="/admin/dashboard.php">Dashboard</a> → Document Requests</p>
    </div>
</div>

<section class="section--sm">
<div class="container">

<?php if ($viewItem): ?>
<!-- ── DETAIL VIEW ─────────────────────────────────────── -->
<div class="card mb-3">
    <div class="card__header">
        <h2 class="card__title">Request #<?= $viewItem['id'] ?> — <?= sanitize($viewItem['doc_type']) ?></h2>
        <?= statusBadge($viewItem['status']) ?>
    </div>
    <div class="card__body">
        <div class="grid-2 mb-3">
            <div><span class="form-label">Resident Name</span><p><?= sanitize($viewItem['fname'] . ' ' . $viewItem['lname']) ?></p></div>
            <div><span class="form-label">Email</span><p><?= sanitize($viewItem['email']) ?></p></div>
            <div><span class="form-label">Contact</span><p><?= sanitize($viewItem['contact']) ?></p></div>
            <div><span class="form-label">Address</span><p><?= sanitize($viewItem['address']) ?></p></div>
            <div><span class="form-label">Document Type</span><p><?= sanitize($viewItem['doc_type']) ?></p></div>
            <div><span class="form-label">Date Requested</span><p><?= formatDateTime($viewItem['requested_at']) ?></p></div>
            <div style="grid-column:1/-1"><span class="form-label">Purpose</span><p><?= sanitize($viewItem['purpose']) ?></p></div>
            <?php if ($viewItem['admin_remarks']): ?>
            <div style="grid-column:1/-1"><span class="form-label">Previous Remarks</span><p><?= sanitize($viewItem['admin_remarks']) ?></p></div>
            <?php endif; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="request_id" value="<?= $viewItem['id'] ?>">
            <div class="form-group">
                <label class="form-label">Admin Remarks (Optional)</label>
                <textarea name="remarks" class="form-control" placeholder="Add notes or remarks..."><?= sanitize($viewItem['admin_remarks'] ?? '') ?></textarea>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" name="action" value="Approved" class="btn btn--success">✓ Approve</button>
                <button type="submit" name="action" value="Processing" class="btn btn--secondary">⏳ Set Processing</button>
                <button type="submit" name="action" value="Pending" class="btn btn--outline">↩ Set Pending</button>
                <button type="submit" name="action" value="Denied" class="btn btn--danger" data-confirm="Deny this request?">✕ Deny</button>
                <a href="/admin/document-requests.php" class="btn btn--outline" style="margin-left:auto;">← Back to List</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- ── FILTERS ────────────────────────────────────────── -->
<div class="card mb-2">
    <div class="card__body" style="padding:16px 24px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <div class="form-group" style="margin:0; flex:1; min-width:180px;">
                <label class="form-label">Search Resident</label>
                <input type="text" name="search" class="form-control" placeholder="Name or email..." value="<?= sanitize($search) ?>">
            </div>
            <div class="form-group" style="margin:0; min-width:180px;">
                <label class="form-label">Document Type</label>
                <select name="type" class="form-control">
                    <option value="">All Types</option>
                    <?php foreach (['Barangay Clearance','Certificate of Residency','Certificate of Indigency'] as $t): ?>
                        <option value="<?= $t ?>" <?= $type === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="margin:0; min-width:140px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach (['Pending','Processing','Approved','Denied'] as $s): ?>
                        <option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn--primary">Filter</button>
            <a href="/admin/document-requests.php" class="btn btn--outline">Reset</a>
        </form>
    </div>
</div>

<!-- ── TABLE ──────────────────────────────────────────── -->
<div class="card">
    <div class="card__header">
        <h2 class="card__title">Document Requests <span style="font-size:13px; color:var(--text-muted); font-weight:400;">(<?= $pg['total'] ?> total)</span></h2>
    </div>
    <div class="card__body" style="padding:0;">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>#</th><th>Resident</th><th>Document Type</th><th>Date Requested</th><th>Purpose</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($requests)): ?>
                    <tr><td colspan="7"><div class="empty-state"><div class="empty-state__title">No requests found</div></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td>
                            <div class="fw-600"><?= sanitize($r['fname'] . ' ' . $r['lname']) ?></div>
                            <div style="font-size:12px; color:var(--text-muted);"><?= sanitize($r['email']) ?></div>
                        </td>
                        <td><?= sanitize($r['doc_type']) ?></td>
                        <td><?= formatDate($r['requested_at']) ?></td>
                        <td style="max-width:160px;"><?= sanitize(substr($r['purpose'],0,50)) . (strlen($r['purpose'])>50?'…':'') ?></td>
                        <td><?= statusBadge($r['status']) ?></td>
                        <td><a href="?view=<?= $r['id'] ?><?= $status?"&status=$status":'' ?><?= $type?"&type=$type":'' ?><?= $search?"&search=".urlencode($search):'' ?>" class="btn btn--outline btn--sm">View</a></td>
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
            <?php for ($i=1; $i<=$pg['total_pages']; $i++): ?>
                <?php if ($i===$page): ?><span class="current"><?= $i ?></span>
                <?php else: ?><a href="?page=<?= $i ?>&status=<?= urlencode($status) ?>&type=<?= urlencode($type) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if ($pg['has_next']): ?><a href="?page=<?= $page+1 ?>&status=<?= urlencode($status) ?>&type=<?= urlencode($type) ?>&search=<?= urlencode($search) ?>">Next →</a><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
