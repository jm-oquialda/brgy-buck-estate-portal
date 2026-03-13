<?php
$pageTitle = 'File a Blotter Report';
require_once __DIR__ . '/../includes/functions.php';
requireResident();
$user = currentUser();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $vals = [
        'incident_type'      => trim($_POST['incident_type'] ?? ''),
        'respondent_name'    => trim($_POST['respondent_name'] ?? ''),
        'incident_date'      => trim($_POST['incident_date'] ?? ''),
        'incident_location'  => trim($_POST['incident_location'] ?? ''),
        'description'        => trim($_POST['description'] ?? ''),
    ];

    if (empty($vals['incident_type']))     $errors['incident_type']     = 'Please select an incident type.';
    if (empty($vals['respondent_name']))   $errors['respondent_name']   = 'Respondent name is required.';
    if (empty($vals['incident_date']))     $errors['incident_date']     = 'Incident date is required.';
    if (empty($vals['incident_location'])) $errors['incident_location'] = 'Incident location is required.';
    if (empty($vals['description']))       $errors['description']       = 'Please provide a description.';

    if (empty($errors)) {
        $pdo = getDB();
        $stmt = $pdo->prepare(
            "INSERT INTO blotter_reports (user_id, incident_type, complainant_name, respondent_name, incident_date, incident_location, description)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $user['id'], $vals['incident_type'],
            $user['fname'] . ' ' . $user['lname'],
            $vals['respondent_name'], $vals['incident_date'],
            $vals['incident_location'], $vals['description'],
        ]);
        setFlash('success', 'Blotter report filed successfully. The barangay will review your report.');
        header('Location: /resident/dashboard.php');
        exit;
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">File a Blotter Report</h1>
        <p class="page-header__breadcrumb"><a href="/resident/dashboard.php">Dashboard</a> → File Blotter</p>
    </div>
</div>

<section class="section--sm">
    <div class="container">
        <div class="grid-sidebar">
            <div>
                <div class="card">
                    <div class="card__header"><h2 class="card__title">Blotter Report Form</h2></div>
                    <div class="card__body">
                        <div class="warning-box">⚠ Filing a false blotter report is a punishable offense under Philippine law. Please ensure all information is accurate.</div>

                        <form method="POST" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                            <div class="form-group">
                                <label class="form-label">Incident Type <span class="required">*</span></label>
                                <select name="incident_type" class="form-control <?= isset($errors['incident_type']) ? 'is-invalid' : '' ?>">
                                    <option value="">— Select Incident Type —</option>
                                    <?php foreach (['Physical Altercation', 'Theft / Robbery', 'Noise Complaint', 'Trespassing', 'Domestic Dispute', 'Vandalism', 'Threat / Intimidation', 'Other'] as $t): ?>
                                        <option value="<?= $t ?>" <?= (($_POST['incident_type'] ?? '') === $t) ? 'selected' : '' ?>><?= $t ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['incident_type'])): ?><div class="form-error">⚠ <?= $errors['incident_type'] ?></div><?php endif; ?>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Complainant Name</label>
                                    <input type="text" class="form-control" value="<?= sanitize($user['fname'] . ' ' . $user['lname']) ?>" readonly>
                                    <div class="form-note">Auto-filled from your profile.</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Respondent Name <span class="required">*</span></label>
                                    <input type="text" name="respondent_name" class="form-control <?= isset($errors['respondent_name']) ? 'is-invalid' : '' ?>"
                                           value="<?= sanitize($_POST['respondent_name'] ?? '') ?>" placeholder="Full name of respondent">
                                    <?php if (isset($errors['respondent_name'])): ?><div class="form-error">⚠ <?= $errors['respondent_name'] ?></div><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Date of Incident <span class="required">*</span></label>
                                    <input type="date" name="incident_date" class="form-control <?= isset($errors['incident_date']) ? 'is-invalid' : '' ?>"
                                           value="<?= sanitize($_POST['incident_date'] ?? '') ?>" max="<?= date('Y-m-d') ?>">
                                    <?php if (isset($errors['incident_date'])): ?><div class="form-error">⚠ <?= $errors['incident_date'] ?></div><?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Location of Incident <span class="required">*</span></label>
                                    <input type="text" name="incident_location" class="form-control <?= isset($errors['incident_location']) ? 'is-invalid' : '' ?>"
                                           value="<?= sanitize($_POST['incident_location'] ?? '') ?>" placeholder="e.g. Corner of Sample St., Buck Estate">
                                    <?php if (isset($errors['incident_location'])): ?><div class="form-error">⚠ <?= $errors['incident_location'] ?></div><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Narrative Description <span class="required">*</span></label>
                                <textarea name="description" class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>"
                                          placeholder="Describe the incident in detail. Include what happened, how it started, and any witnesses..."
                                          maxlength="2000"><?= sanitize($_POST['description'] ?? '') ?></textarea>
                                <?php if (isset($errors['description'])): ?><div class="form-error">⚠ <?= $errors['description'] ?></div><?php endif; ?>
                            </div>

                            <div style="display:flex; gap:10px;">
                                <button type="submit" class="btn btn--primary btn--lg">Submit Report</button>
                                <a href="/resident/dashboard.php" class="btn btn--outline btn--lg">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div>
                <div class="card">
                    <div class="card__header"><h3 class="card__title">What to expect</h3></div>
                    <div class="card__body" style="font-size:14px; line-height:1.8; color:var(--text-muted);">
                        <p>After filing your report:</p>
                        <ul style="margin-top:10px; padding-left:18px; list-style:disc;">
                            <li>Your report will be reviewed by the barangay within 1–3 business days.</li>
                            <li>A case number will be assigned once acknowledged.</li>
                            <li>You can track the status in your Dashboard.</li>
                            <li>The barangay may contact you for additional details.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
