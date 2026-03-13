<?php
$pageTitle = 'Blotter Report Details';
require_once __DIR__ . '/../includes/functions.php';
requireResident();
$user = currentUser();
$pdo  = getDB();

$id   = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM blotter_reports WHERE id=? AND user_id=?");
$stmt->execute([$id, $user['id']]);
$req  = $stmt->fetch();
if (!$req) { setFlash('error', 'Report not found.'); header('Location: /resident/dashboard.php'); exit; }

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Blotter Report #<?= $req['id'] ?></h1>
        <p class="page-header__breadcrumb"><a href="/resident/dashboard.php">Dashboard</a> → Blotter Report</p>
    </div>
</div>
<section class="section--sm">
<div class="container" style="max-width:760px;">
    <div class="card">
        <div class="card__header">
            <h2 class="card__title">
                <?= $req['case_number'] ? 'Case No. ' . sanitize($req['case_number']) : 'Blotter Report #' . $req['id'] ?>
            </h2>
            <?= statusBadge($req['status']) ?>
        </div>
        <div class="card__body">
            <div class="grid-2 mb-3">
                <div><span class="form-label">Incident Type</span><p><?= sanitize($req['incident_type']) ?></p></div>
                <div><span class="form-label">Date of Incident</span><p><?= formatDate($req['incident_date']) ?></p></div>
                <div><span class="form-label">Complainant</span><p><?= sanitize($req['complainant_name']) ?></p></div>
                <div><span class="form-label">Respondent</span><p><?= sanitize($req['respondent_name']) ?></p></div>
                <div style="grid-column:1/-1"><span class="form-label">Location</span><p><?= sanitize($req['incident_location']) ?></p></div>
                <div style="grid-column:1/-1"><span class="form-label">Narrative Description</span><p style="white-space:pre-wrap;"><?= sanitize($req['description']) ?></p></div>
                <div><span class="form-label">Date Filed</span><p><?= formatDateTime($req['filed_at']) ?></p></div>
                <?php if ($req['admin_remarks']): ?>
                <div style="grid-column:1/-1">
                    <span class="form-label">Barangay Remarks</span>
                    <div class="info-box"><?= sanitize($req['admin_remarks']) ?></div>
                </div>
                <?php endif; ?>
            </div>
            <a href="/resident/dashboard.php" class="btn btn--outline">← Back to Dashboard</a>
        </div>
    </div>
</div>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
