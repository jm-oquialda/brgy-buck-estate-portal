<?php
$pageTitle = 'About';
require_once __DIR__ . '/includes/functions.php';
startSession();
$pdo = getDB();
$allOfficials = $pdo->query("SELECT * FROM officials WHERE is_active=TRUE ORDER BY order_num ASC")->fetchAll();
$officials = array_filter($allOfficials, fn($o) => strpos($o['position'], 'SK ') !== 0);
$skOfficials = array_filter($allOfficials, fn($o) => strpos($o['position'], 'SK ') === 0);
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">About Barangay Buck Estate</h1>
        <p class="page-header__breadcrumb"><a href="/index.php">Home</a> → About</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="grid-sidebar" style="gap:40px;">
            <div>
                <span class="section__eyebrow">Our Barangay</span>
                <h2 class="section__title" style="text-align:left; margin-bottom:20px;">Barangay Buck Estate, Alfonso, Cavite</h2>
                <p style="font-size:15px; line-height:1.9; color:var(--text); margin-bottom:16px;">
                    Barangay Buck Estate is one of the 32 barangays of the Municipality of Alfonso in the Province of Cavite, located within the CALABARZON Region (Region IV-A) of the Philippines. It is situated along the scenic Tagaytay-Nasugbu Highway at approximately Km. 69 of Emilio Aguinaldo Highway.
                </p>
                <p style="font-size:15px; line-height:1.9; color:var(--text); margin-bottom:16px;">
                    The barangay is known for its cool climate, lush landscapes, and proximity to popular destinations such as Sonya's Garden and the Royale Tagaytay Estates. According to the 2020 Census, Buck Estate has a population of approximately 3,638 residents, representing 6.13% of Alfonso's total population.
                </p>
                <p style="font-size:15px; line-height:1.9; color:var(--text);">
                    The Barangay Hall serves as the center of governance, providing essential services to all residents in the community. Through this portal, the barangay aims to make public services more accessible, transparent, and efficient for every resident.
                </p>
            </div>
            <div>
                <div class="card">
                    <div class="card__header"><h3 class="card__title">📍 Quick Facts</h3></div>
                    <div class="card__body">
                        <table style="width:100%; font-size:14px; border-collapse:collapse;">
                            <tr style="border-bottom:1px solid var(--gray-100);">
                                <td style="padding:10px 0; color:var(--text-muted); width:45%;">Municipality</td>
                                <td style="padding:10px 0; font-weight:600; color:var(--primary);">Alfonso, Cavite</td>
                            </tr>
                            <tr style="border-bottom:1px solid var(--gray-100);">
                                <td style="padding:10px 0; color:var(--text-muted);">Province</td>
                                <td style="padding:10px 0; font-weight:600; color:var(--primary);">Cavite</td>
                            </tr>
                            <tr style="border-bottom:1px solid var(--gray-100);">
                                <td style="padding:10px 0; color:var(--text-muted);">Region</td>
                                <td style="padding:10px 0; font-weight:600; color:var(--primary);">IV-A (CALABARZON)</td>
                            </tr>
                            <tr style="border-bottom:1px solid var(--gray-100);">
                                <td style="padding:10px 0; color:var(--text-muted);">ZIP Code</td>
                                <td style="padding:10px 0; font-weight:600; color:var(--primary);">4123</td>
                            </tr>
                            <tr style="border-bottom:1px solid var(--gray-100);">
                                <td style="padding:10px 0; color:var(--text-muted);">Population</td>
                                <td style="padding:10px 0; font-weight:600; color:var(--primary);">3,638 <span style="font-weight:400; font-size:12px;">(2020 Census)</span></td>
                            </tr>
                            <tr>
                                <td style="padding:10px 0; color:var(--text-muted);">Location</td>
                                <td style="padding:10px 0; font-weight:600; color:var(--primary);">14.0894° N, 120.8532° E</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card__header"><h3 class="card__title">🕐 Office Hours</h3></div>
                    <div class="card__body" style="font-size:14px; line-height:2;">
                        <div style="margin-bottom:8px;"><span style="font-weight:600; color:var(--primary);">Monday – Friday</span><br>8:00 AM – 5:00 PM</div>
                        <div><span style="font-weight:600; color:var(--primary);">Saturday</span><br>8:00 AM – 12:00 PM</div>
                        <div style="margin-top:12px;" class="info-box">🌐 Online document requests can be submitted 24/7 through this portal.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BARANGAY HALL PHOTO -->
<section class="section section--gray" style="padding-top:0;">
    <div class="container">
        <div class="about-banner">
            <img src="/assets/img/barangay-hall.jpg"
                 alt="Multi-Purpose Hall, Barangay Buck Estate, Alfonso, Cavite"
                 loading="lazy"
                 style="width:100%; height:auto; border-radius:12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
        </div>
    </div>
</section>

<!-- MISSION VISION -->
<section class="section">
    <div class="container">
        <div class="grid-2 stagger-children">
            <div class="card" style="border-top:4px solid var(--primary);">
                <div class="card__body" style="padding:32px;">
                    <div style="font-size:32px; margin-bottom:12px;">🎯</div>
                    <h3 style="font-family:var(--font-heading); font-size:20px; font-weight:700; color:var(--primary); margin-bottom:12px;">Mission</h3>
                    <p style="font-size:15px; line-height:1.9; color:var(--text);">
                        To serve the residents of Barangay Buck Estate with integrity, transparency, and efficiency — ensuring that every constituent has equal access to barangay services and benefits under the rule of law.
                    </p>
                </div>
            </div>
            <div class="card" style="border-top:4px solid var(--accent);">
                <div class="card__body" style="padding:32px;">
                    <div style="font-size:32px; margin-bottom:12px;">👁</div>
                    <h3 style="font-family:var(--font-heading); font-size:20px; font-weight:700; color:var(--primary); margin-bottom:12px;">Vision</h3>
                    <p style="font-size:15px; line-height:1.9; color:var(--text);">
                        A progressive, peaceful, and self-sustaining Barangay Buck Estate where residents thrive in a safe, clean, and digitally empowered community governed by accountable and servant leaders.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- LANDMARKS -->
<section class="section section--gray">
    <div class="container">
        <div class="section__header">
            <span class="section__eyebrow">Places of Interest</span>
            <h2 class="section__title">Landmarks in Barangay Buck Estate</h2>
            <p class="section__subtitle">Discover what makes Buck Estate a vibrant and well-known barangay in Alfonso, Cavite.</p>
        </div>
        <div class="landmark-grid stagger-children">
            <div class="landmark-card">
                <img src="/assets/img/landmark-sonyas-garden.jpg"
                     alt="Sonya's Garden, Buck Estate, Alfonso, Cavite"
                     class="landmark-card__img" loading="lazy">
                <div class="landmark-card__body">
                    <span class="landmark-card__tag">Dining &amp; Tourism</span>
                    <div class="landmark-card__title">Sonya's Garden</div>
                    <p class="landmark-card__desc">One of the Philippines' most iconic bed-and-breakfast destinations, known for its lush organic gardens, fresh farm-to-table cuisine, and peaceful countryside ambiance. Located right in the heart of Buck Estate.</p>
                </div>
            </div>
            <div class="landmark-card">
                <img src="/assets/img/landmark-buck-estate-elementary.jpg"
                     alt="Buck Estate Elementary School, Alfonso, Cavite"
                     class="landmark-card__img" loading="lazy">
                <div class="landmark-card__body">
                    <span class="landmark-card__tag">Education</span>
                    <div class="landmark-card__title">Buck Estate Elementary School</div>
                    <p class="landmark-card__desc">A public elementary school serving the young learners of Barangay Buck Estate and nearby communities, providing quality basic education and nurturing the next generation of leaders.</p>
                </div>
            </div>
            <div class="landmark-card">
                <img src="/assets/img/landmark-buck-estate-nhs.jpg"
                     alt="Buck Estate National High School, Alfonso, Cavite"
                     class="landmark-card__img" loading="lazy">
                <div class="landmark-card__body">
                    <span class="landmark-card__tag">Education</span>
                    <div class="landmark-card__title">Buck Estate National High School</div>
                    <p class="landmark-card__desc">A public secondary school in the heart of the barangay, offering quality junior and senior high school education to the youth of Buck Estate and surrounding communities in Alfonso, Cavite.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BARANGAY OFFICIALS -->
<?php if (!empty($officials)): ?>
<section class="section">
    <div class="container">
        <div class="section__header">
            <span class="section__eyebrow">Leadership</span>
            <h2 class="section__title">Elected Barangay Officials</h2>
            <p class="section__subtitle">Serving the constituents of Barangay Buck Estate, Alfonso, Cavite.</p>
        </div>
        <div class="officials-grid">
            <?php foreach ($officials as $o): ?>
            <div class="official-card">
                <?php if (!empty($o['photo_url'])): ?>
                    <img src="<?= sanitize($o['photo_url']) ?>" alt="<?= sanitize($o['name']) ?>" class="official-card__photo">
                <?php else: ?>
                    <div class="official-card__photo-placeholder">
                        <?= strtoupper(substr(trim($o['name']), 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="official-card__name"><?= sanitize($o['name']) ?></div>
                <div class="official-card__position"><?= sanitize($o['position']) ?></div>
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
        <div class="section__header">
            <span class="section__eyebrow">Youth Leadership</span>
            <h2 class="section__title">Sangguniang Kabataan Officials</h2>
            <p class="section__subtitle">The youth council serving Barangay Buck Estate.</p>
        </div>
        <div class="officials-grid">
            <?php foreach ($skOfficials as $o): ?>
            <div class="official-card">
                <?php if (!empty($o['photo_url'])): ?>
                    <img src="<?= sanitize($o['photo_url']) ?>" alt="<?= sanitize($o['name']) ?>" class="official-card__photo">
                <?php else: ?>
                    <div class="official-card__photo-placeholder">
                        <?= strtoupper(substr(trim($o['name']), 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="official-card__name"><?= sanitize($o['name']) ?></div>
                <div class="official-card__position"><?= sanitize($o['position']) ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
