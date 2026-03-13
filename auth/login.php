<?php
require_once __DIR__ . '/../includes/functions.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/dashboard.php' : '/resident/dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter your email and password.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            $_SESSION['fname']   = $user['fname'];
            setFlash('success', 'Welcome back, ' . $user['fname'] . '!');
            $redirect = $_GET['redirect'] ?? ($user['role'] === 'admin' ? '/admin/dashboard.php' : '/resident/dashboard.php');
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log In – Brgy. Buck Estate</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-card__logo">
            <div class="auth-card__logo-circle">BE</div>
            <div class="auth-card__logo-name">Brgy. Buck Estate</div>
            <div class="auth-card__logo-sub">Alfonso, Cavite</div>
        </div>

        <h2 class="auth-card__title">Log In</h2>
        <p class="auth-card__subtitle">Access your barangay services account.</p>

        <?= renderFlash() ?>
        <?php if ($error): ?>
            <div class="flash flash--error"><span class="flash__icon">✕</span><?= sanitize($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

            <div class="form-group">
                <label class="form-label">Email Address <span class="required">*</span></label>
                <input type="email" name="email" class="form-control"
                       value="<?= sanitize($_POST['email'] ?? '') ?>" placeholder="yourname@email.com" autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Password <span class="required">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn--primary btn--block btn--lg">Log In</button>
        </form>

        <div class="auth-divider">Don't have an account? <a href="/auth/register.php">Register here</a></div>
        <div class="text-center"><a href="/index.php" style="font-size:13px; color: var(--text-muted);">← Back to Home</a></div>
    </div>
</div>
<script src="/assets/js/main.js"></script>
</body>
</html>
