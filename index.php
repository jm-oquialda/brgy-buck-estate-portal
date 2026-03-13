<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/functions.php';
startSession();

$pdo = getDB();

// Fetch latest announcements
$annStmt = $pdo->query("SELECT * FROM announcements WHERE status = 'published' ORDER BY created_at DESC LIMIT 3");
$announcements = $annStmt->fetchAll();

// Fetch officials
$offStmt = $pdo->query("SELECT * FROM officials WHERE is_active = TRUE ORDER BY order_num ASC");
$allOfficials = $offStmt->fetchAll();
$officials = array_filter($allOfficials, fn($o) => strpos($o['position'], 'SK ') !== 0);
$skOfficials = array_filter($allOfficials, fn($o) => strpos($o['position'], 'SK ') === 0);

require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO -->
<section class="hero">
    <div class="container hero__inner">
        <div class="hero__content">
            <span class="hero__eyebrow">Official Barangay Portal</span>
            <h1 class="hero__title">Welcome to<br><span>Barangay Buck Estate</span></h1>
            <p class="hero__subtitle">
                Serving the residents of Buck Estate, Alfonso, Cavite with accessible and efficient digital barangay services.
            </p>
            <div class="hero__actions">
                <?php if (!isLoggedIn()): ?>
                    <a href="/auth/register.php" class="btn btn--primary btn--lg">Register Now</a>
                    <a href="/auth/login.php" class="btn btn--outline-white btn--lg">Log In</a>
                <?php else: ?>
                    <a href="<?= isAdmin() ? '/admin/dashboard.php' : '/resident/dashboard.php' ?>" class="btn btn--primary btn--lg">Go to Dashboard</a>
                <?php endif; ?>
                <a href="/about.php" class="btn btn--outline-white btn--lg">Learn More</a>
            </div>
            <div class="hero__stats">
                <div>
                    <span class="hero__stat-num">3,638+</span>
                    <span class="hero__stat-label">Residents (2020 Census)</span>
                </div>
                <div>
                    <span class="hero__stat-num">4</span>
                    <span class="hero__stat-label">Digital Services</span>
                </div>
                <div>
                    <span class="hero__stat-num">24/7</span>
                    <span class="hero__stat-label">Online Access</span>
                </div>
            </div>
        </div>
        <div class="hero__photo">
            <img src="/assets/img/officials-award.jpg"
                 alt="Barangay Buck Estate - 2025 Most Outstanding Barangay Awardees"
                 loading="eager"
                 style="border-radius: 12px;">
            <div class="hero__photo-caption">2025 Most Outstanding Barangay</div>
        </div>
    </div>
</section>

<!-- SERVICES + EMERGENCY HOTLINES SIDEBAR -->
<section class="section">
    <div class="container">
        <div class="home-with-sidebar">
            <div>
                <!-- SERVICES -->
                <div class="section__header fade-in" style="margin-bottom:32px;">
                    <span class="section__eyebrow">What We Offer</span>
                    <h2 class="section__title">Barangay Services Online</h2>
                    <p class="section__subtitle">Access barangay services anytime, from the comfort of your home.</p>
                </div>
                <div class="services-grid stagger-children">
                    <div class="service-card">
                        <div class="service-card__icon">📄</div>
                        <h3 class="service-card__title">Document Requests</h3>
                        <p class="service-card__desc">Request your Barangay Clearance, Certificate of Residency, or Certificate of Indigency online.</p>
                        <a href="<?= isLoggedIn() ? '/resident/document-request-new.php' : '/auth/login.php' ?>" class="service-card__link">Request Now →</a>
                    </div>
                    <div class="service-card">
                        <div class="service-card__icon">📋</div>
                        <h3 class="service-card__title">Blotter Reports</h3>
                        <p class="service-card__desc">File an incident report online. Describe the incident and let the barangay respond.</p>
                        <a href="<?= isLoggedIn() ? '/resident/blotter-new.php' : '/auth/login.php' ?>" class="service-card__link">File Report →</a>
                    </div>
                    <div class="service-card">
                        <div class="service-card__icon">💰</div>
                        <h3 class="service-card__title">Financial Assistance</h3>
                        <p class="service-card__desc">Apply for Medical, Burial, or Calamity assistance through the portal.</p>
                        <a href="<?= isLoggedIn() ? '/resident/financial-new.php' : '/auth/login.php' ?>" class="service-card__link">Apply Now →</a>
                    </div>
                    <div class="service-card">
                        <div class="service-card__icon">📢</div>
                        <h3 class="service-card__title">Announcements</h3>
                        <p class="service-card__desc">Stay informed with the latest news, updates, and notices from the barangay.</p>
                        <a href="/announcements.php" class="service-card__link">View All →</a>
                    </div>
                </div>
            </div>

            <!-- EMERGENCY HOTLINES SIDEBAR -->
            <aside class="fade-in-right">
                <div class="hotlines-banner">
                    <div class="hotlines-banner__header">
                        <div class="hotlines-banner__title">BUCK ESTATE</div>
                        <div class="hotlines-banner__subtitle">Emergency Hotlines</div>
                    </div>
                    <div class="hotlines-banner__body">
                        <div class="hotline-item">
                            <div class="hotline-item__icon">📞</div>
                            <div class="hotline-item__info">
                                <span class="hotline-item__label">Brgy. Buck Estate Hotline</span>
                                <span class="hotline-item__number"><a href="tel:09637890011">0963-789-0011</a></span>
                            </div>
                        </div>
                        <div class="hotline-item">
                            <div class="hotline-item__icon">🏥</div>
                            <div class="hotline-item__info">
                                <span class="hotline-item__label">Municipal Health Office</span>
                                <span class="hotline-item__number"><a href="tel:09166191088">0916-619-1088</a></span>
                            </div>
                        </div>
                        <div class="hotline-item">
                            <div class="hotline-item__icon">🤝</div>
                            <div class="hotline-item__info">
                                <span class="hotline-item__label">Municipal Social Welfare &amp; Dev't</span>
                                <span class="hotline-item__number"><a href="tel:09178031253">0917-803-1253</a></span>
                            </div>
                        </div>
                        <div class="hotline-item">
                            <div class="hotline-item__icon">🚔</div>
                            <div class="hotline-item__info">
                                <span class="hotline-item__label">Municipal Police Station</span>
                                <span class="hotline-item__number">
                                    <a href="tel:09065366135">0906-536-6135</a><br>
                                    <a href="tel:09985985614">0998-598-5614</a>
                                </span>
                            </div>
                        </div>
                        <div class="hotline-item">
                            <div class="hotline-item__icon">🚒</div>
                            <div class="hotline-item__info">
                                <span class="hotline-item__label">Bureau of Fire Protection</span>
                                <span class="hotline-item__number">
                                    <a href="tel:09296632424">0929-663-2424</a><br>
                                    <a href="tel:09156022113">0915-602-2113</a>
                                </span>
                            </div>
                        </div>
                        <div class="hotline-item">
                            <div class="hotline-item__icon">🛟</div>
                            <div class="hotline-item__info">
                                <span class="hotline-item__label">MDRRMO</span>
                                <span class="hotline-item__number">
                                    <a href="tel:09178330206">0917-833-0206</a><br>
                                    <a href="tel:09618667777">0961-866-7777</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="hotlines-banner__footer">KEEP SAFE, EVERYONE!</div>
                </div>
            </aside>
        </div>
    </div>
</section>

<!-- ANNOUNCEMENTS -->
<?php if (!empty($announcements)): ?>
<section class="section section--gray">
    <div class="container">
        <div class="section__header fade-in">
            <span class="section__eyebrow">Stay Updated</span>
            <h2 class="section__title">Latest Announcements</h2>
        </div>
        <div class="announce-grid stagger-children">
            <?php foreach ($announcements as $ann): ?>
            <div class="announce-card">
                <?php if (!empty($ann['image_url'])): ?>
                    <img src="<?= sanitize($ann['image_url']) ?>" alt="<?= sanitize($ann['title']) ?>" class="announce-card__img" loading="lazy">
                <?php endif; ?>
                <div class="announce-card__body">
                    <div class="announce-card__date">📅 <?= formatDateTime($ann['created_at']) ?></div>
                    <h3 class="announce-card__title"><?= sanitize($ann['title']) ?></h3>
                    <p class="announce-card__excerpt"><?= sanitize(substr($ann['content'], 0, 150)) . '...' ?></p>
                </div>
                <div class="announce-card__footer">
                    <a href="/announcement.php?id=<?= $ann['id'] ?>" class="announce-card__link">Read More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-3">
            <a href="/announcements.php" class="btn btn--outline">View All Announcements</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- BARANGAY OFFICIALS -->
<?php if (!empty($officials)): ?>
<section class="section">
    <div class="container">
        <div class="section__header fade-in">
            <span class="section__eyebrow">Leadership</span>
            <h2 class="section__title">Barangay Officials</h2>
            <p class="section__subtitle">Meet the elected leaders serving Barangay Buck Estate.</p>
        </div>
        <div class="officials-grid stagger-children">
            <?php foreach ($officials as $official): ?>
            <div class="official-card">
                <?php if (!empty($official['photo_url'])): ?>
                    <img src="<?= sanitize($official['photo_url']) ?>" alt="<?= sanitize($official['name']) ?>" class="official-card__photo">
                <?php else: ?>
                    <div class="official-card__photo-placeholder">
                        <?= strtoupper(substr($official['name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="official-card__name"><?= sanitize($official['name']) ?></div>
                <div class="official-card__position"><?= sanitize($official['position']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- SK OFFICIALS -->
<?php if (!empty($skOfficials)): ?>
<section class="section section--gray">
    <div class="container">
        <div class="section__header fade-in">
            <span class="section__eyebrow">Youth Leadership</span>
            <h2 class="section__title">Sangguniang Kabataan Officials</h2>
            <p class="section__subtitle">The youth council serving Barangay Buck Estate.</p>
        </div>
        <div class="officials-grid stagger-children">
            <?php foreach ($skOfficials as $official): ?>
            <div class="official-card">
                <?php if (!empty($official['photo_url'])): ?>
                    <img src="<?= sanitize($official['photo_url']) ?>" alt="<?= sanitize($official['name']) ?>" class="official-card__photo">
                <?php else: ?>
                    <div class="official-card__photo-placeholder">
                        <?= strtoupper(substr($official['name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="official-card__name"><?= sanitize($official['name']) ?></div>
                <div class="official-card__position"><?= sanitize($official['position']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA BANNER -->
<?php if (!isLoggedIn()): ?>
<section class="section" style="background: linear-gradient(135deg, var(--primary) 0%, var(--accent-dark) 100%); color: white;">
    <div class="container text-center">
        <h2 style="font-family: var(--font-heading); font-size: 28px; font-weight: 700; margin-bottom: 12px;">Ready to Access Barangay Services?</h2>
        <p style="color: rgba(255,255,255,.8); margin-bottom: 28px; font-size: 16px;">Create a free account and start requesting documents, filing reports, and more — all online.</p>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="/auth/register.php" class="btn btn--primary btn--lg" style="background: white; color: var(--accent); border-color: white;">Create Account</a>
            <a href="/auth/login.php" class="btn btn--outline-white btn--lg">Log In</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
