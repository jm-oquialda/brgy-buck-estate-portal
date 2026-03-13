<?php
$pageTitle = 'Edit Profile';
require_once __DIR__ . '/../includes/functions.php';
requireResident();
$user   = currentUser();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? 'profile';

    if ($action === 'profile') {
        $fname   = trim($_POST['fname']   ?? '');
        $lname   = trim($_POST['lname']   ?? '');
        $address = trim($_POST['address'] ?? '');
        $contact = trim($_POST['contact'] ?? '');

        if (empty($fname))   $errors['fname']   = 'First name is required.';
        if (empty($lname))   $errors['lname']   = 'Last name is required.';
        if (empty($address)) $errors['address'] = 'Address is required.';
        if (empty($contact)) $errors['contact'] = 'Contact number is required.';

        if (empty($errors)) {
            $pdo  = getDB();
            $stmt = $pdo->prepare("UPDATE users SET fname=?, lname=?, address=?, contact=? WHERE id=?");
            $stmt->execute([$fname, $lname, $address, $contact, $user['id']]);
            setFlash('success', 'Profile updated successfully.');
            header('Location: /resident/profile.php'); exit;
        }
    }

    if ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password']     ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!password_verify($current, $user['password']))
            $errors['current_password'] = 'Current password is incorrect.';
        elseif (strlen($new) < 8)
            $errors['new_password'] = 'New password must be at least 8 characters.';
        elseif ($new !== $confirm)
            $errors['confirm_password'] = 'Passwords do not match.';

        if (empty($errors)) {
            $pdo  = getDB();
            $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->execute([password_hash($new, PASSWORD_BCRYPT), $user['id']]);
            setFlash('success', 'Password changed successfully.');
            header('Location: /resident/profile.php'); exit;
        }
    }
}

$user = currentUser(); // refresh
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">My Profile</h1>
        <p class="page-header__breadcrumb"><a href="/resident/dashboard.php">Dashboard</a> → Edit Profile</p>
    </div>
</div>

<section class="section--sm">
<div class="container" style="max-width:760px;">

    <!-- PROFILE INFO -->
    <div class="card mb-3" data-tabs>
        <div class="card__header">
            <div class="tabs" style="border-bottom:none; margin:0;">
                <button class="tab-btn active" data-tab="tab-info">👤 Personal Info</button>
                <button class="tab-btn" data-tab="tab-password">🔒 Change Password</button>
            </div>
        </div>
        <div class="card__body">

            <!-- TAB: PERSONAL INFO -->
            <div id="tab-info" class="tab-panel active">
                <form method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="profile">

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">First Name <span class="required">*</span></label>
                            <input type="text" name="fname" class="form-control <?= isset($errors['fname'])?'is-invalid':'' ?>"
                                   value="<?= sanitize($_POST['fname'] ?? $user['fname']) ?>">
                            <?php if (isset($errors['fname'])): ?><div class="form-error">⚠ <?= $errors['fname'] ?></div><?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Last Name <span class="required">*</span></label>
                            <input type="text" name="lname" class="form-control <?= isset($errors['lname'])?'is-invalid':'' ?>"
                                   value="<?= sanitize($_POST['lname'] ?? $user['lname']) ?>">
                            <?php if (isset($errors['lname'])): ?><div class="form-error">⚠ <?= $errors['lname'] ?></div><?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="<?= sanitize($user['email']) ?>" readonly>
                        <div class="form-note">Email cannot be changed. Contact the barangay admin if needed.</div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Home Address <span class="required">*</span></label>
                        <input type="text" name="address" class="form-control <?= isset($errors['address'])?'is-invalid':'' ?>"
                               value="<?= sanitize($_POST['address'] ?? $user['address']) ?>">
                        <?php if (isset($errors['address'])): ?><div class="form-error">⚠ <?= $errors['address'] ?></div><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contact Number <span class="required">*</span></label>
                        <input type="text" name="contact" class="form-control <?= isset($errors['contact'])?'is-invalid':'' ?>"
                               value="<?= sanitize($_POST['contact'] ?? $user['contact']) ?>">
                        <?php if (isset($errors['contact'])): ?><div class="form-error">⚠ <?= $errors['contact'] ?></div><?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn--primary">Save Changes</button>
                </form>
            </div>

            <!-- TAB: PASSWORD -->
            <div id="tab-password" class="tab-panel">
                <form method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="password">

                    <div class="form-group">
                        <label class="form-label">Current Password <span class="required">*</span></label>
                        <input type="password" name="current_password" class="form-control <?= isset($errors['current_password'])?'is-invalid':'' ?>"
                               placeholder="Enter current password">
                        <?php if (isset($errors['current_password'])): ?><div class="form-error">⚠ <?= $errors['current_password'] ?></div><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">New Password <span class="required">*</span></label>
                        <input type="password" name="new_password" class="form-control <?= isset($errors['new_password'])?'is-invalid':'' ?>"
                               placeholder="Minimum 8 characters">
                        <?php if (isset($errors['new_password'])): ?><div class="form-error">⚠ <?= $errors['new_password'] ?></div><?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirm New Password <span class="required">*</span></label>
                        <input type="password" name="confirm_password" class="form-control <?= isset($errors['confirm_password'])?'is-invalid':'' ?>"
                               placeholder="Re-enter new password">
                        <?php if (isset($errors['confirm_password'])): ?><div class="form-error">⚠ <?= $errors['confirm_password'] ?></div><?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn--primary">Change Password</button>
                </form>
            </div>

        </div>
    </div>

    <div class="text-center">
        <a href="/resident/dashboard.php" class="btn btn--outline">← Back to Dashboard</a>
    </div>

</div>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
