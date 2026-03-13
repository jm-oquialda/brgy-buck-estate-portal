<?php
$pageTitle = 'Manage Blotter Reports';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    verifyCsrf();
    $id          = (int)($_POST['report_id'] ?? 0);
    $action      = $_POST['action'];
    $remarks     = trim($_POST['remarks'] ?? '');
    $case_number = trim($_POST['case_number'] ?? '');
    $allowed     = ['Under Review', 'Resolved', 'Dismissed', 'Filed'];
    if ($id && in_array($action, $allowed)) {
        $stmt = $pdo->prepare("UPDATE blotter_reports SET status=?, admin_remarks=?, case_number=?, processed_by=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$action, $remarks, $case_number ?: null, $_SESSION['user_id'], $id]);

        // Notify resident
        $req = $pdo->prepare("SELECT user_id, incident_type FROM blotter_reports WHERE id=?");
        $req->execute([$id]);
        $reqData = $req->fetch();
        if ($reqData) {
            $msgs = [
                'Under Review' => 'Your blotter report (' . $reqData['incident_type'] . ') is now under review by the barangay.',
                'Resolved'     => 'Your blotter report (' . $reqData['incident_type'] . ') has been resolved.' . ($remarks ? ' Remarks: ' . $remarks : ''),
                'Dismissed'    => 'Your blotter report (' . $reqData['incident_type'] . ') has been dismissed.' . ($remarks ? ' Remarks: ' . $remarks : ''),
                'Filed'        => 'Your blotter report (' . $reqData['incident_type'] . ') status has been updated.',
            ];
            createNotification(
                $reqData['user_id'],
                $action === 'Resolved' ? 'success' : ($action === 'Dismissed' ? 'error' : 'info'),
                'Blotter Report #' . $id . ' — ' . $action,
                $msgs[$action] ?? 'Your blotter report status has been updated.',
                '/resident/blotter.php?id=' . $id
            );
        }

        setFlash('success', "Blotter Report #$id updated to $action.");
    }
    header('Location: /admin/blotter-reports.php');
    exit;
}

$status  = $_GET['status'] ?? '';
$search  = trim($_GET['search'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;

$where  = ["1=1"];
$params = [];
if ($status) { $where[] = "br.status = ?"; $params[] = $status; }
if ($search) { $where[] = "(u.fname LIKE ? OR u.lname LIKE ? OR br.respondent_name LIKE ?)"; $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]); }
$whereStr = implode(' AND ', $where);

$total = $pdo->prepare("SELECT COUNT(*) FROM blotter_reports br JOIN users u ON br.user_id = u.id WHERE $whereStr");
$total->execute($params);
$pg = paginate($total->fetchColumn(), $perPage, $page);

$stmt = $pdo->prepare("SELECT br.*, u.fname, u.lname, u.email, u.contact, u.address
    FROM blotter_reports br JOIN users u ON br.user_id = u.id
    WHERE $whereStr ORDER BY br.filed_at DESC LIMIT $perPage OFFSET {$pg['offset']}");
$stmt->execute($params);
$reports = $stmt->fetchAll();

$viewItem = null;
if (isset($_GET['view'])) {
    $vs = $pdo->prepare("SELECT br.*, u.fname, u.lname, u.email, u.contact FROM blotter_reports br JOIN users u ON br.user_id = u.id WHERE br.id = ?");
    $vs->execute([(int)$_GET['view']]);
    $viewItem = $vs->fetch();
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Manage Blotter Reports</h1>
        <p class="page-header__breadcrumb"><a href="/admin/dashboard.php">Dashboard</a> → Blotter Reports</p>
    </div>
</div>

<section class="section--sm">
<div class="container">

<?php if ($viewItem): ?>
<div class="card mb-3">
    <div class="card__header">
        <h2 class="card__title">Blotter Report #<?= $viewItem['id'] ?><?= $viewItem['case_number'] ? ' — Case No. ' . sanitize($viewItem['case_number']) : '' ?></h2>
        <?= statusBadge($viewItem['status']) ?>
    </div>
    <div class="card__body">
        <div class="grid-2 mb-3">
            <div><span class="form-label">Complainant</span><p><?= sanitize($viewItem['complainant_name']) ?></p></div>
            <div><span class="form-label">Respondent</span><p><?= sanitize($viewItem['respondent_name']) ?></p></div>
            <div><span class="form-label">Incident Type</span><p><?= sanitize($viewItem['incident_type']) ?></p></div>
            <div><span class="form-label">Date of Incident</span><p><?= formatDate($viewItem['incident_date']) ?></p></div>
            <div style="grid-column:1/-1"><span class="form-label">Location</span><p><?= sanitize($viewItem['incident_location']) ?></p></div>
            <div style="grid-column:1/-1"><span class="form-label">Narrative Description</span><p style="white-space:pre-wrap;"><?= sanitize($viewItem['description']) ?></p></div>
            <div><span class="form-label">Date Filed</span><p><?= formatDateTime($viewItem['filed_at']) ?></p></div>
            <div><span class="form-label">Filed By</span><p><?= sanitize($viewItem['fname'] . ' ' . $viewItem['lname']) ?> (<?= sanitize($viewItem['email']) ?>)</p></div>
            <?php $attachments = getAttachments('blotter', $viewItem['id']); if (!empty($attachments)): ?>
            <div style="grid-column:1/-1"><?= renderAttachments($attachments) ?></div>
            <?php endif; ?>
        </div>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
            <input type="hidden" name="report_id" value="<?= $viewItem['id'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Case Number</label>
                    <input type="text" name="case_number" class="form-control" placeholder="e.g. BLT-2025-001" value="<?= sanitize($viewItem['case_number'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Admin Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Optional notes..." value="<?= sanitize($viewItem['admin_remarks'] ?? '') ?>">
                </div>
            </div>
            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="submit" name="action" value="Under Review" class="btn btn--secondary">🔍 Under Review</button>
                <button type="submit" name="action" value="Resolved" class="btn btn--success">✓ Resolved</button>
                <button type="submit" name="action" value="Dismissed" class="btn btn--danger" data-confirm="Dismiss this report?">✕ Dismiss</button>
                <a href="/admin/blotter-reports.php" class="btn btn--outline" style="margin-left:auto;">← Back</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="card mb-2">
    <div class="card__body" style="padding:16px 24px;">
        <form method="GET" style="display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end;">
            <div class="form-group" style="margin:0; flex:1; min-width:200px;">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Resident or respondent name..." value="<?= sanitize($search) ?>">
            </div>
            <div class="form-group" style="margin:0; min-width:160px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <?php foreach (['Filed','Under Review','Resolved','Dismissed'] as $s): ?>
                        <option value="<?= $s ?>" <?= $status===$s?'selected':'' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn--primary">Filter</button>
            <a href="/admin/blotter-reports.php" class="btn btn--outline">Reset</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card__header">
        <h2 class="card__title">Blotter Reports <span style="font-size:13px; color:var(--text-muted); font-weight:400;">(<?= $pg['total'] ?> total)</span></h2>
    </div>
    <div class="card__body" style="padding:0;">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>#</th><th>Case No.</th><th>Complainant</th><th>Respondent</th><th>Incident Type</th><th>Date Filed</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($reports)): ?>
                    <tr><td colspan="8"><div class="empty-state"><div class="empty-state__title">No reports found</div></div></td></tr>
                    <?php else: ?>
                    <?php foreach ($reports as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= $r['case_number'] ? sanitize($r['case_number']) : '<span style="color:var(--text-muted)">—</span>' ?></td>
                        <td><?= sanitize($r['complainant_name']) ?></td>
                        <td><?= sanitize($r['respondent_name']) ?></td>
                        <td><?= sanitize($r['incident_type']) ?></td>
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
            <?php if ($pg['has_prev']): ?><a href="?page=<?= $page-1 ?>&status=<?= urlencode($status) ?>&search=<?= urlencode($search) ?>">← Prev</a><?php endif; ?>
            <?php for ($i=1;$i<=$pg['total_pages'];$i++): ?>
                <?php if($i===$page): ?><span class="current"><?= $i ?></span>
                <?php else: ?><a href="?page=<?= $i ?>&status=<?= urlencode($status) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a><?php endif; ?>
            <?php endfor; ?>
            <?php if ($pg['has_next']): ?><a href="?page=<?= $page+1 ?>&status=<?= urlencode($status) ?>&search=<?= urlencode($search) ?>">Next →</a><?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

</div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
