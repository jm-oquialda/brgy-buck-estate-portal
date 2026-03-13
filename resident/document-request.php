<?php
$pageTitle = 'Document Request Details';
require_once __DIR__ . '/../includes/functions.php';
requireResident();
$user = currentUser();
$pdo  = getDB();

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM document_requests WHERE id=? AND user_id=?");
$stmt->execute([$id, $user['id']]);
$req  = $stmt->fetch();
if (!$req) { setFlash('error', 'Request not found.'); header('Location: /resident/dashboard.php'); exit; }

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Document Request #<?= $req['id'] ?></h1>
        <p class="page-header__breadcrumb"><a href="/resident/dashboard.php">Dashboard</a> → Document Request</p>
    </div>
</div>
<section class="section--sm">
<div class="container" style="max-width:760px;">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title"><?= sanitize($req['doc_type']) ?></h2>
            <?= statusBadge($req['status']) ?>
        </div>
        <div class="card__body">
            <div class="grid-2 mb-3">
                <div><span class="form-label">Date Requested</span><p><?= formatDateTime($req['requested_at']) ?></p></div>
                <div><span class="form-label">Status</span><p><?= statusBadge($req['status']) ?></p></div>
                <div style="grid-column:1/-1"><span class="form-label">Purpose</span><p><?= sanitize($req['purpose']) ?></p></div>
                <?php if ($req['admin_remarks']): ?>
                <div style="grid-column:1/-1">
                    <span class="form-label">Barangay Remarks</span>
                    <div class="info-box"><?= sanitize($req['admin_remarks']) ?></div>
                </div>
                <?php endif; ?>
                <?php if ($req['processed_at']): ?>
                <div><span class="form-label">Processed On</span><p><?= formatDateTime($req['processed_at']) ?></p></div>
                <?php endif; ?>
            </div>

            <?php if ($req['status'] === 'Approved'): ?>
            <div class="card" style="background:var(--success-bg); border-color:#a7dfc5; margin-bottom:16px;">
                <div class="card__body" style="padding:16px 20px; color:var(--success); font-size:14px;">
                    ✅ <strong>Your document is ready.</strong> Please visit the Barangay Hall during office hours to claim your document. Bring a valid ID.
                </div>
            </div>
            <?php elseif ($req['status'] === 'Denied'): ?>
            <div class="card" style="background:var(--danger-bg); border-color:#f5c6c6; margin-bottom:16px;">
                <div class="card__body" style="padding:16px 20px; color:var(--danger); font-size:14px;">
                    ❌ <strong>This request was denied.</strong> Please refer to the remarks above or visit the Barangay Hall for assistance.
                </div>
            </div>
            <?php elseif ($req['status'] === 'Processing'): ?>
            <div class="info-box">⏳ Your request is currently being processed. You will be notified once it is ready.</div>
            <?php else: ?>
            <div class="warning-box">⌛ Your request is pending review. Processing usually takes 1–3 business days.</div>
            <?php endif; ?>

            <a href="/resident/dashboard.php" class="btn btn--outline">← Back to Dashboard</a>
        </div>
    </div>
</div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
