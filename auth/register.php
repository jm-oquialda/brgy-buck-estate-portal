<?php
require_once __DIR__ . '/../includes/functions.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/dashboard.php' : '/resident/dashboard.php'));
    exit;
}

$errors = [];
$vals   = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $vals = [
        'fname'    => trim($_POST['fname'] ?? ''),
        'lname'    => trim($_POST['lname'] ?? ''),
        'email'    => trim($_POST['email'] ?? ''),
        'address'  => trim($_POST['address'] ?? ''),
        'contact'  => trim($_POST['contact'] ?? ''),
        'password' => $_POST['password'] ?? '',
        'confirm'  => $_POST['confirm'] ?? '',
    ];

    if (empty($vals['fname']))    $errors['fname']   = 'First name is required.';
    if (empty($vals['lname']))    $errors['lname']   = 'Last name is required.';
    if (empty($vals['address']))  $errors['address'] = 'Home address is required.';
    if (empty($vals['contact']))  $errors['contact'] = 'Contact number is required.';
    if (empty($vals['email']) || !filter_var($vals['email'], FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'A valid email address is required.';
    if (strlen($vals['password']) < 8)
        $errors['password'] = 'Password must be at least 8 characters.';
    if ($vals['password'] !== $vals['confirm'])
        $errors['confirm'] = 'Passwords do not match.';

    if (empty($errors)) {
        $pdo = getDB();
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$vals['email']]);
        if ($check->fetch()) {
            $errors['email'] = 'This email is already registered.';
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO users (fname, lname, email, password, address, contact, role)
                 VALUES (?, ?, ?, ?, ?, ?, 'resident')"
            );
            $stmt->execute([
                $vals['fname'], $vals['lname'], $vals['email'],
                password_hash($vals['password'], PASSWORD_BCRYPT),
                $vals['address'], $vals['contact'],
            ]);
            setFlash('success', 'Account created successfully! Please log in.');
            header('Location: /auth/login.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register – Brgy. Buck Estate</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card scale-in">
        <div class="auth-card__logo">
            <div class="auth-card__logo-circle">BE</div>
            <div class="auth-card__logo-name">Brgy. Buck Estate</div>
            <div class="auth-card__logo-sub">Alfonso, Cavite</div>
        </div>

        <h2 class="auth-card__title">Create an Account</h2>
        <p class="auth-card__subtitle">Register to access barangay services online.</p>

        <?= renderFlash() ?>

        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">First Name <span class="required">*</span></label>
                    <input type="text" name="fname" class="form-control <?= isset($errors['fname']) ? 'is-invalid' : '' ?>"
                           value="<?= sanitize($vals['fname'] ?? '') ?>" placeholder="Maria">
                    <?php if (isset($errors['fname'])): ?>
                        <div class="form-error">⚠ <?= $errors['fname'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Last Name <span class="required">*</span></label>
                    <input type="text" name="lname" class="form-control <?= isset($errors['lname']) ? 'is-invalid' : '' ?>"
                           value="<?= sanitize($vals['lname'] ?? '') ?>" placeholder="Santos">
                    <?php if (isset($errors['lname'])): ?>
                        <div class="form-error">⚠ <?= $errors['lname'] ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Home Address <span class="required">*</span></label>
                <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"
                       value="<?= sanitize($vals['address'] ?? '') ?>" placeholder="House No., Street, Brgy. Buck Estate, Alfonso, Cavite">
                <?php if (isset($errors['address'])): ?>
                    <div class="form-error">⚠ <?= $errors['address'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Contact Number <span class="required">*</span></label>
                <input type="text" name="contact" class="form-control <?= isset($errors['contact']) ? 'is-invalid' : '' ?>"
                       value="<?= sanitize($vals['contact'] ?? '') ?>" placeholder="09XX-XXX-XXXX">
                <?php if (isset($errors['contact'])): ?>
                    <div class="form-error">⚠ <?= $errors['contact'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address <span class="required">*</span></label>
                <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                       value="<?= sanitize($vals['email'] ?? '') ?>" placeholder="yourname@email.com">
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error">⚠ <?= $errors['email'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Password <span class="required">*</span></label>
                <input type="password" id="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                       placeholder="Minimum 8 characters">
                <?php if (isset($errors['password'])): ?>
                    <div class="form-error">⚠ <?= $errors['password'] ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password <span class="required">*</span></label>
                <input type="password" name="confirm" class="form-control <?= isset($errors['confirm']) ? 'is-invalid' : '' ?>"
                       placeholder="Re-enter your password">
                <?php if (isset($errors['confirm'])): ?>
                    <div class="form-error">⚠ <?= $errors['confirm'] ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn--primary btn--block btn--lg">Create Account</button>
        </form>

        <div class="auth-divider">Already have an account? <a href="/auth/login.php">Log in here</a></div>
        <div class="text-center"><a href="/index.php" style="font-size:13px; color: var(--text-muted);">← Back to Home</a></div>
    </div>
</div>
<script src="/assets/js/main.js"></script>
</body>
</html>
