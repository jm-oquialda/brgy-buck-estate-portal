<?php
$pageTitle = 'Request a Document';
require_once __DIR__ . '/../includes/functions.php';
requireResident();
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $docType = trim($_POST['doc_type'] ?? '');
    $purpose = trim($_POST['purpose'] ?? '');
    $errors  = [];

    $allowed = ['Barangay Clearance', 'Certificate of Residency', 'Certificate of Indigency'];
    if (!in_array($docType, $allowed)) $errors['doc_type'] = 'Please select a valid document type.';
    if (empty($purpose))              $errors['purpose']  = 'Please state the purpose of your request.';

    if (empty($errors)) {
        $pdo  = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO document_requests (user_id, doc_type, purpose) VALUES (?, ?, ?)"
        );
        $stmt->execute([$user['id'], $docType, $purpose]);
        setFlash('success', 'Document request submitted successfully! We will notify you once it is processed.');
        header('Location: /resident/dashboard.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Request a Document</h1>
        <p class="page-header__breadcrumb">
            <a href="/resident/dashboard.php">Dashboard</a> → Request Document
        </p>
    </div>
</div>

<section class="section--sm">
    <div class="container">
        <div class="grid-sidebar">
            <div>
                <div class="card">
                    <div class="card__header">
                        <h2 class="card__title">Document Request Form</h2>
                    </div>
                    <div class="card__body">
                        <form method="POST" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                            <div class="form-group">
                                <label class="form-label">Document Type <span class="required">*</span></label>
                                <select name="doc_type" class="form-control <?= isset($errors['doc_type']) ? 'is-invalid' : '' ?>">
                                    <option value="">— Select Document Type —</option>
                                    <?php foreach (['Barangay Clearance', 'Certificate of Residency', 'Certificate of Indigency'] as $type): ?>
                                        <option value="<?= $type ?>" <?= (($_POST['doc_type'] ?? '') === $type) ? 'selected' : '' ?>><?= $type ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['doc_type'])): ?><div class="form-error">⚠ <?= $errors['doc_type'] ?></div><?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Purpose of Request <span class="required">*</span></label>
                                <textarea name="purpose" class="form-control <?= isset($errors['purpose']) ? 'is-invalid' : '' ?>"
                                          placeholder="e.g. For employment purposes, school enrollment, etc."
                                          maxlength="500"><?= sanitize($_POST['purpose'] ?? '') ?></textarea>
                                <?php if (isset($errors['purpose'])): ?><div class="form-error">⚠ <?= $errors['purpose'] ?></div><?php endif; ?>
                            </div>

                            <hr style="margin: 20px 0; border-color: var(--gray-200);">
                            <p class="form-label" style="margin-bottom:14px;">Resident Information (Auto-filled from your profile)</p>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">First Name</label>
                                    <input type="text" class="form-control" value="<?= sanitize($user['fname']) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" class="form-control" value="<?= sanitize($user['lname']) ?>" readonly>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" value="<?= sanitize($user['address']) ?>" readonly>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" value="<?= sanitize($user['contact']) ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" value="<?= sanitize($user['email']) ?>" readonly>
                                </div>
                            </div>
                            <div class="info-box">ℹ To update your information, go to <a href="/resident/profile.php">Edit Profile</a>.</div>

                            <div style="display:flex; gap:10px;">
                                <button type="submit" class="btn btn--primary btn--lg">Submit Request</button>
                                <a href="/resident/dashboard.php" class="btn btn--outline btn--lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div>
                <div class="card">
                    <div class="card__header"><h3 class="card__title">ℹ Document Guide</h3></div>
                    <div class="card__body">
                        <div style="margin-bottom:16px;">
                            <div class="fw-600" style="color:var(--primary); margin-bottom:4px;">Barangay Clearance</div>
                            <div class="form-note">Required for employment, business permits, and other official transactions.</div>
                        </div>
                        <div style="margin-bottom:16px;">
                            <div class="fw-600" style="color:var(--primary); margin-bottom:4px;">Certificate of Residency</div>
                            <div class="form-note">Proof that you are an official resident of Brgy. Buck Estate.</div>
                        </div>
                        <div style="margin-bottom:16px;">
                            <div class="fw-600" style="color:var(--primary); margin-bottom:4px;">Certificate of Indigency</div>
                            <div class="form-note">Used for hospital bills, financial aid, and other assistance programs.</div>
                        </div>
                        <hr style="border-color:var(--gray-200); margin:16px 0;">
                        <div class="warning-box">⏱ Processing time: 1–3 business days. You will be notified once ready for pickup.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
