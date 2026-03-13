<?php
$pageTitle = 'Financial Assistance Request';
require_once __DIR__ . '/../includes/functions.php';
requireResident();
$user = currentUser();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $vals = [
        'assistance_type'    => trim($_POST['assistance_type'] ?? ''),
        'description'        => trim($_POST['description'] ?? ''),
        'amount_requested'   => trim($_POST['amount_requested'] ?? ''),
        'supporting_details' => trim($_POST['supporting_details'] ?? ''),
    ];

    $allowed = ['Medical', 'Burial', 'Calamity', 'Others'];
    if (!in_array($vals['assistance_type'], $allowed)) $errors['assistance_type'] = 'Please select a valid assistance type.';
    if (empty($vals['description'])) $errors['description'] = 'Please describe your situation.';
    if (!is_numeric($vals['amount_requested']) || $vals['amount_requested'] <= 0)
        $errors['amount_requested'] = 'Please enter a valid amount.';

    if (empty($errors)) {
        $pdo = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO financial_requests (user_id, assistance_type, description, amount_requested, supporting_details)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $user['id'], $vals['assistance_type'], $vals['description'],
            $vals['amount_requested'], $vals['supporting_details'],
        ]);
        $reqId = (int)$pdo->lastInsertId();

        // Handle file uploads
        $uploadErrors = handleFileUploads('financial', $reqId, $user['id']);

        $msg = 'Financial assistance application submitted. The barangay will review your application.';
        if (!empty($uploadErrors)) {
            $msg .= ' Some files could not be uploaded: ' . implode(' ', $uploadErrors);
        }
        setFlash('success', $msg);
        header('Location: /resident/dashboard.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Financial Assistance Request</h1>
        <p class="page-header__breadcrumb"><a href="/resident/dashboard.php">Dashboard</a> → Financial Assistance</p>
    </div>
</div>

<section class="section--sm">
    <div class="container">
        <div class="grid-sidebar">
            <div>
                <div class="card">
                    <div class="card__header"><h2 class="card__title">Application Form</h2></div>
                    <div class="card__body">
                        <form method="POST" enctype="multipart/form-data" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                            <div class="form-group">
                                <label class="form-label">Assistance Type <span class="required">*</span></label>
                                <select name="assistance_type" class="form-control <?= isset($errors['assistance_type']) ? 'is-invalid' : '' ?>">
                                    <option value="">— Select Assistance Type —</option>
                                    <?php foreach (['Medical', 'Burial', 'Calamity', 'Others'] as $t): ?>
                                        <option value="<?= $t ?>" <?= (($_POST['assistance_type'] ?? '') === $t) ? 'selected' : '' ?>><?= $t ?> Assistance</option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['assistance_type'])): ?><div class="form-error">⚠ <?= $errors['assistance_type'] ?></div><?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description / Nature of Request <span class="required">*</span></label>
                                <textarea name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                          placeholder="Describe your situation and why you need assistance..."
                                          maxlength="1000"><?= sanitize($_POST['description'] ?? '') ?></textarea>
                                <?php if (isset($errors['description'])): ?><div class="form-error">⚠ <?= $errors['description'] ?></div><?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Amount of Assistance Requested (₱) <span class="required">*</span></label>
                                <input type="number" name="amount_requested" class="form-control <?= isset($errors['amount_requested']) ? 'is-invalid' : '' ?>"
                                       value="<?= sanitize($_POST['amount_requested'] ?? '') ?>"
                                       placeholder="e.g. 5000" min="1" step="0.01">
                                <?php if (isset($errors['amount_requested'])): ?><div class="form-error">⚠ <?= $errors['amount_requested'] ?></div><?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Supporting Details <span style="color:var(--text-muted);font-weight:400;">(Optional)</span></label>
                                <textarea name="supporting_details" class="form-control"
                                          placeholder="Hospital name, patient name, case number, or any supporting information..."
                                          maxlength="1000"><?= sanitize($_POST['supporting_details'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Supporting Documents <span style="color:var(--text-muted);font-weight:400;">(Optional)</span></label>
                                <input type="file" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
                                <div class="form-note">Upload receipts, hospital bills, or other supporting documents. Max 5MB per file. Accepted: JPG, PNG, GIF, PDF, DOC, DOCX.</div>
                            </div>

                            <div class="info-box">📅 Date of filing will be automatically recorded upon submission.</div>

                            <div style="display:flex; gap:10px;">
                                <button type="submit" class="btn btn--primary btn--lg">Submit Application</button>
                                <a href="/resident/dashboard.php" class="btn btn--outline btn--lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div>
                <div class="card">
                    <div class="card__header"><h3 class="card__title">ℹ Assistance Types</h3></div>
                    <div class="card__body" style="font-size:14px; line-height:2; color:var(--text-muted);">
                        <div style="margin-bottom:12px;"><span class="fw-600" style="color:var(--primary);">Medical</span><br>Hospitalization, medicine, laboratory, or treatment expenses.</div>
                        <div style="margin-bottom:12px;"><span class="fw-600" style="color:var(--primary);">Burial</span><br>Funeral and burial expenses for a deceased family member.</div>
                        <div style="margin-bottom:12px;"><span class="fw-600" style="color:var(--primary);">Calamity</span><br>Disaster relief for typhoon, fire, flood, and other calamities.</div>
                        <div style="margin-bottom:12px;"><span class="fw-600" style="color:var(--primary);">Others</span><br>Other urgent assistance not covered by the above categories.</div>
                        <hr style="border-color:var(--gray-200); margin:12px 0;">
                        <div class="warning-box">Applications are subject to fund availability and barangay review.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
