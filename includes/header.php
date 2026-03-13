<?php
require_once __DIR__ . '/../includes/functions.php';
startSession();
$user = currentUser();
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' – ' : '' ?><?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/', 1)) ?>assets/css/style.css">
    <?= isset($extraHead) ? $extraHead : '' ?>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="container topbar__inner">
        <span>📍 Buck Estate, Alfonso, Cavite 4123</span>
        <span>📞 <?= SITE_CONTACT ?></span>
    </div>
</div>

<!-- NAVBAR -->
<header class="navbar">
    <div class="container navbar__inner">
        <a href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/', 1)) ?>index.php" class="navbar__brand">
            <img src="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/', 1)) ?>assets/img/logo.svg"
                 alt="Brgy. Buck Estate Logo" class="navbar__logo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
            <div class="navbar__logo-fallback">BE</div>
            <div class="navbar__brand-text">
                <span class="navbar__site-name">Brgy. Buck Estate</span>
                <span class="navbar__site-sub">Alfonso, Cavite</span>
            </div>
        </a>

        <button class="navbar__toggle" id="navToggle" aria-label="Toggle navigation">
            <span></span><span></span><span></span>
        </button>

        <nav class="navbar__nav" id="navMenu">
            <?php if ($user && $user['role'] === 'admin'): ?>
                <!-- ADMIN NAV -->
                <a href="/admin/dashboard.php" class="navbar__link <?= $currentDir === 'admin' && $currentPage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
                <a href="/admin/announcements.php" class="navbar__link <?= $currentDir === 'admin' && $currentPage === 'announcements' ? 'active' : '' ?>">Announcements</a>
                <a href="/admin/document-requests.php" class="navbar__link <?= $currentDir === 'admin' && $currentPage === 'document-requests' ? 'active' : '' ?>">Documents</a>
                <a href="/admin/blotter-reports.php" class="navbar__link <?= $currentDir === 'admin' && $currentPage === 'blotter-reports' ? 'active' : '' ?>">Blotter</a>
                <a href="/admin/financial.php" class="navbar__link <?= $currentDir === 'admin' && $currentPage === 'financial' ? 'active' : '' ?>">Financial</a>
                <a href="/admin/residents.php" class="navbar__link <?= $currentDir === 'admin' && $currentPage === 'residents' ? 'active' : '' ?>">Residents</a>
                <div class="navbar__user">
                    <span class="navbar__user-name">👤 <?= sanitize($user['fname']) ?></span>
                    <a href="/auth/logout.php" class="btn btn--outline btn--sm">Log Out</a>
                </div>
            <?php elseif ($user): ?>
                <!-- RESIDENT NAV -->
                <a href="/index.php" class="navbar__link <?= $currentPage === 'index' ? 'active' : '' ?>">Home</a>
                <a href="/announcements.php" class="navbar__link <?= $currentPage === 'announcements' ? 'active' : '' ?>">Announcements</a>
                <div class="navbar__dropdown">
                    <a href="#" class="navbar__link">Services ▾</a>
                    <div class="navbar__dropdown-menu">
                        <a href="/resident/document-request-new.php">Request Document</a>
                        <a href="/resident/blotter-new.php">File Blotter</a>
                        <a href="/resident/financial-new.php">Financial Assistance</a>
                    </div>
                </div>
                <a href="/about.php" class="navbar__link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
                <a href="/contact.php" class="navbar__link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a>
                <div class="navbar__user">
                    <a href="/resident/dashboard.php" class="navbar__user-name">👤 <?= sanitize($user['fname']) ?></a>
                    <a href="/auth/logout.php" class="btn btn--outline btn--sm">Log Out</a>
                </div>
            <?php else: ?>
                <!-- PUBLIC NAV -->
                <a href="/index.php" class="navbar__link <?= $currentPage === 'index' ? 'active' : '' ?>">Home</a>
                <a href="/about.php" class="navbar__link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
                <a href="/announcements.php" class="navbar__link <?= $currentPage === 'announcements' ? 'active' : '' ?>">Announcements</a>
                <a href="/contact.php" class="navbar__link <?= $currentPage === 'contact' ? 'active' : '' ?>">Contact Us</a>
                <div class="navbar__actions">
                    <a href="/auth/login.php" class="btn btn--outline btn--sm">Log In</a>
                    <a href="/auth/register.php" class="btn btn--primary btn--sm">Register</a>
                </div>
            <?php endif; ?>
        </nav>
    </div>
</header>

<main class="main-content">
<?= renderFlash() ?>
