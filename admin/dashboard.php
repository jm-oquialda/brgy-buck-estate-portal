<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/functions.php';
requireAdmin();
$user = currentUser();
$pdo  = getDB();

// Summary counts
$counts = [];
foreach ([
    'pending_docs'     => "SELECT COUNT(*) FROM document_requests WHERE status = 'Pending'",
    'pending_blotters' => "SELECT COUNT(*) FROM blotter_reports WHERE status IN ('Filed','Under Review')",
    'pending_fin'      => "SELECT COUNT(*) FROM financial_requests WHERE status = 'Pending'",
    'total_residents'  => "SELECT COUNT(*) FROM users WHERE role = 'resident'",
] as $key => $sql) {
    $counts[$key] = $pdo->query($sql)->fetchColumn();
}

// Recent activity
$recent = $pdo->query("
    SELECT 'Document Request' AS type, u.fname, u.lname, dr.status, dr.requested_at AS created_at, dr.id
    FROM document_requests dr JOIN users u ON dr.user_id = u.id
    UNION ALL
    SELECT 'Blotter Report', u.fname, u.lname, br.status, br.filed_at, br.id
    FROM blotter_reports br JOIN users u ON br.user_id = u.id
    UNION ALL
    SELECT 'Financial Assistance', u.fname, u.lname, fr.status, fr.filed_at, fr.id
    FROM financial_requests fr JOIN users u ON fr.user_id = u.id
    ORDER BY created_at DESC LIMIT 10
")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Admin Dashboard</h1>
        <p class="page-header__breadcrumb">Logged in as <?= sanitize($user['fname'] . ' ' . $user['lname']) ?> · Administrator</p>
    </div>
</div>

<section class="section--sm">
    <div class="container">
        <div class="stat-cards stagger-children" style="grid-template-columns: repeat(4,1fr);">
            <div class="stat-card stat-card--accent">
                <div class="stat-card__label">Pending Documents</div>
                <div class="stat-card__value"><?= $counts['pending_docs'] ?></div>
                <a href="/admin/document-requests.php" style="font-size:12px; color:var(--accent);">Manage →</a>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Pending Blotters</div>
                <div class="stat-card__value"><?= $counts['pending_blotters'] ?></div>
                <a href="/admin/blotter-reports.php" style="font-size:12px; color:var(--accent);">Manage →</a>
            </div>
            <div class="stat-card stat-card--accent">
                <div class="stat-card__label">Pending Financial</div>
                <div class="stat-card__value"><?= $counts['pending_fin'] ?></div>
                <a href="/admin/financial.php" style="font-size:12px; color:var(--accent);">Manage →</a>
            </div>
            <div class="stat-card">
                <div class="stat-card__label">Total Residents</div>
                <div class="stat-card__value"><?= $counts['total_residents'] ?></div>
                <a href="/admin/residents.php" style="font-size:12px; color:var(--accent);">View All →</a>
            </div>
        </div>

        <!-- QUICK LINKS -->
        <div class="grid-3 mb-3 stagger-children">
            <a href="/admin/announcements.php" class="card" style="padding:18px 20px; display:flex; align-items:center; gap:12px; text-decoration:none; transition:var(--transition);"
               onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--gray-200)'">
                <div style="font-size:28px;">📢</div>
                <div>
                    <div class="fw-600" style="color:var(--primary);">Announcements</div>
                    <div class="form-note">Post and manage barangay notices</div>
                </div>
            </a>
            <a href="/admin/document-requests.php" class="card" style="padding:18px 20px; display:flex; align-items:center; gap:12px; text-decoration:none; transition:var(--transition);"
               onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--gray-200)'">
                <div style="font-size:28px;">📄</div>
                <div>
                    <div class="fw-600" style="color:var(--primary);">Document Requests</div>
                    <div class="form-note">Approve or deny requests</div>
                </div>
            </a>
            <a href="/admin/blotter-reports.php" class="card" style="padding:18px 20px; display:flex; align-items:center; gap:12px; text-decoration:none; transition:var(--transition);"
               onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--gray-200)'">
                <div style="font-size:28px;">📋</div>
                <div>
                    <div class="fw-600" style="color:var(--primary);">Blotter Reports</div>
                    <div class="form-note">Review and assign case numbers</div>
                </div>
            </a>
        </div>

        <!-- RECENT ACTIVITY -->
        <div class="card fade-in">
            <div class="card__header">
                <h2 class="card__title">Recent Activity</h2>
            </div>
            <div class="card__body" style="padding:0;">
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                            <tr><th>Date</th><th>Resident</th><th>Type</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent)): ?>
                            <tr><td colspan="4"><div class="empty-state"><div class="empty-state__title">No activity yet</div></div></td></tr>
                            <?php else: ?>
                            <?php foreach ($recent as $r): ?>
                            <tr>
                                <td><?= timeAgo($r['created_at']) ?></td>
                                <td><?= sanitize($r['fname'] . ' ' . $r['lname']) ?></td>
                                <td><?= sanitize($r['type']) ?></td>
                                <td><?= statusBadge($r['status']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
