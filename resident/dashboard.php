<?php
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireResident();
$user = currentUser();
$pdo  = getDB();

// Counts
$docCount = $pdo->prepare("SELECT COUNT(*) FROM document_requests WHERE user_id = ?");
$docCount->execute([$user['id']]);
$totalDocs = $docCount->fetchColumn();

$blotterCount = $pdo->prepare("SELECT COUNT(*) FROM blotter_reports WHERE user_id = ?");
$blotterCount->execute([$user['id']]);
$totalBlotters = $blotterCount->fetchColumn();

$finCount = $pdo->prepare("SELECT COUNT(*) FROM financial_requests WHERE user_id = ?");
$finCount->execute([$user['id']]);
$totalFin = $finCount->fetchColumn();

// Fetch requests
$docs     = $pdo->prepare("SELECT * FROM document_requests WHERE user_id = ? ORDER BY requested_at DESC LIMIT 20");
$docs->execute([$user['id']]);
$docReqs  = $docs->fetchAll();

$blotters = $pdo->prepare("SELECT * FROM blotter_reports WHERE user_id = ? ORDER BY filed_at DESC LIMIT 20");
$blotters->execute([$user['id']]);
$blotterReqs = $blotters->fetchAll();

$fins     = $pdo->prepare("SELECT * FROM financial_requests WHERE user_id = ? ORDER BY filed_at DESC LIMIT 20");
$fins->execute([$user['id']]);
$finReqs  = $fins->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container flex-between">
        <div>
            <h1 class="page-header__title">My Dashboard</h1>
            <p class="page-header__breadcrumb">Welcome back, <?= sanitize($user['fname'] . ' ' . $user['lname']) ?></p>
        </div>
        <a href="/resident/profile.php" class="btn btn--outline-white btn--sm">Edit Profile</a>
    </div>
</div>

<section class="section--sm">
    <div class="container">
        <div class="stat-cards stagger-children">
            <div class="stat-card">
                <div class="stat-card__label">Document Requests</div>
                <div class="stat-card__value"><?= $totalDocs ?></div>
                <div class="stat-card__sub">Total submitted</div>
            </div>
            <div class="stat-card stat-card--accent">
                <div class="stat-card__label">Blotter Reports</div>
                <div class="stat-card__value"><?= $totalBlotters ?></div>
                <div class="stat-card__sub">Total filed</div>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Financial Assistance</div>
                <div class="stat-card__value"><?= $totalFin ?></div>
                <div class="stat-card__sub">Total applications</div>
            </div>
        </div>

        <!-- TABS -->
        <div class="card fade-in" data-tabs>
            <div class="card__header">
                <div class="tabs" style="border-bottom:none; margin-bottom:0;">
                    <button class="tab-btn active" data-tab="tab-docs">📄 Document Requests</button>
                    <button class="tab-btn" data-tab="tab-blotters">📋 Blotter Reports</button>
                    <button class="tab-btn" data-tab="tab-financial">💰 Financial Assistance</button>
                </div>
                <div style="display:flex; gap:8px;">
                    <a href="/resident/document-request-new.php" class="btn btn--primary btn--sm">+ New Request</a>
                </div>
            </div>
            <div class="card__body">

                <!-- DOCUMENT REQUESTS TAB -->
                <div id="tab-docs" class="tab-panel active">
                    <?php if (empty($docReqs)): ?>
                        <div class="empty-state">
                            <div class="empty-state__icon">📄</div>
                            <div class="empty-state__title">No document requests yet</div>
                            <div class="empty-state__desc">
                                <a href="/resident/document-request-new.php" class="btn btn--primary btn--sm mt-2">Submit a Request</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Date Requested</th>
                                        <th>Document Type</th>
                                        <th>Purpose</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($docReqs as $i => $r): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= formatDate($r['requested_at']) ?></td>
                                        <td><?= sanitize($r['doc_type']) ?></td>
                                        <td style="max-width:200px;"><?= sanitize(substr($r['purpose'], 0, 60)) . (strlen($r['purpose']) > 60 ? '…' : '') ?></td>
                                        <td><?= statusBadge($r['status']) ?></td>
                                        <td><a href="/resident/document-request.php?id=<?= $r['id'] ?>" class="btn btn--outline btn--sm">View</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- BLOTTER TAB -->
                <div id="tab-blotters" class="tab-panel">
                    <div class="text-right mb-2">
                        <a href="/resident/blotter-new.php" class="btn btn--primary btn--sm">+ File Report</a>
                    </div>
                    <?php if (empty($blotterReqs)): ?>
                        <div class="empty-state">
                            <div class="empty-state__icon">📋</div>
                            <div class="empty-state__title">No blotter reports yet</div>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr><th>#</th><th>Date Filed</th><th>Incident Type</th><th>Respondent</th><th>Status</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($blotterReqs as $i => $r): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= formatDate($r['filed_at']) ?></td>
                                        <td><?= sanitize($r['incident_type']) ?></td>
                                        <td><?= sanitize($r['respondent_name']) ?></td>
                                        <td><?= statusBadge($r['status']) ?></td>
                                        <td><a href="/resident/blotter.php?id=<?= $r['id'] ?>" class="btn btn--outline btn--sm">View</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- FINANCIAL TAB -->
                <div id="tab-financial" class="tab-panel">
                    <div class="text-right mb-2">
                        <a href="/resident/financial-new.php" class="btn btn--primary btn--sm">+ Apply</a>
                    </div>
                    <?php if (empty($finReqs)): ?>
                        <div class="empty-state">
                            <div class="empty-state__icon">💰</div>
                            <div class="empty-state__title">No financial assistance applications yet</div>
                        </div>
                    <?php else: ?>
                        <div class="table-wrap">
                            <table class="table">
                                <thead>
                                    <tr><th>#</th><th>Date Filed</th><th>Type</th><th>Amount</th><th>Status</th><th>Action</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($finReqs as $i => $r): ?>
                                    <tr>
                                        <td><?= $i + 1 ?></td>
                                        <td><?= formatDate($r['filed_at']) ?></td>
                                        <td><?= sanitize($r['assistance_type']) ?></td>
                                        <td>₱<?= number_format($r['amount_requested'], 2) ?></td>
                                        <td><?= statusBadge($r['status']) ?></td>
                                        <td><a href="/resident/financial.php?id=<?= $r['id'] ?>" class="btn btn--outline btn--sm">View</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
