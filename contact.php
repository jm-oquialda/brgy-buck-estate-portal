<?php
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/functions.php';
startSession();

$sent   = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $name    = trim($_POST['name']    ?? '');
    $email   = trim($_POST['email']   ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name))    $errors['name']    = 'Your name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'A valid email address is required.';
    if (empty($subject)) $errors['subject'] = 'Subject is required.';
    if (empty($message)) $errors['message'] = 'Message is required.';

    if (empty($errors)) {
        // In production: send via mail() or a mail service
        // mail(SITE_EMAIL, "Contact Form: $subject", "From: $name <$email>\n\n$message");
        $sent = true;
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1 class="page-header__title">Contact Us</h1>
        <p class="page-header__breadcrumb"><a href="/index.php">Home</a> → Contact</p>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="grid-sidebar" style="gap:40px;">

            <div class="fade-in-left">
                <?php if ($sent): ?>
                    <div class="card" style="text-align:center; padding:48px 32px;">
                        <div style="font-size:56px; margin-bottom:16px;">✅</div>
                        <h2 style="font-family:var(--font-heading); color:var(--primary); margin-bottom:10px;">Message Sent!</h2>
                        <p style="color:var(--text-muted); margin-bottom:24px;">Thank you for reaching out. We will get back to you as soon as possible during office hours.</p>
                        <a href="/contact.php" class="btn btn--primary">Send Another Message</a>
                    </div>
                <?php else: ?>
                    <div class="card">
                        <div class="card__header"><h2 class="card__title">Send a Message</h2></div>
                        <div class="card__body">
                            <form method="POST" novalidate>
                                <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

                                <div class="form-row">
                                    <div class="form-group">
                                        <label class="form-label">Full Name <span class="required">*</span></label>
                                        <input type="text" name="name" class="form-control <?= isset($errors['name'])?'is-invalid':'' ?>"
                                               value="<?= sanitize($_POST['name'] ?? '') ?>" placeholder="Your full name">
                                        <?php if (isset($errors['name'])): ?><div class="form-error">⚠ <?= $errors['name'] ?></div><?php endif; ?>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Email Address <span class="required">*</span></label>
                                        <input type="email" name="email" class="form-control <?= isset($errors['email'])?'is-invalid':'' ?>"
                                               value="<?= sanitize($_POST['email'] ?? '') ?>" placeholder="you@email.com">
                                        <?php if (isset($errors['email'])): ?><div class="form-error">⚠ <?= $errors['email'] ?></div><?php endif; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Subject <span class="required">*</span></label>
                                    <input type="text" name="subject" class="form-control <?= isset($errors['subject'])?'is-invalid':'' ?>"
                                           value="<?= sanitize($_POST['subject'] ?? '') ?>" placeholder="What is your inquiry about?">
                                    <?php if (isset($errors['subject'])): ?><div class="form-error">⚠ <?= $errors['subject'] ?></div><?php endif; ?>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Message <span class="required">*</span></label>
                                    <textarea name="message" class="form-control <?= isset($errors['message'])?'is-invalid':'' ?>"
                                              style="min-height:140px;"
                                              placeholder="Write your message here..."><?= sanitize($_POST['message'] ?? '') ?></textarea>
                                    <?php if (isset($errors['message'])): ?><div class="form-error">⚠ <?= $errors['message'] ?></div><?php endif; ?>
                                </div>

                                <button type="submit" class="btn btn--primary btn--lg btn--block">Send Message</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="fade-in-right">
                <div class="card">
                    <div class="card__header"><h3 class="card__title">📞 Get in Touch</h3></div>
                    <div class="card__body">
                        <ul style="list-style:none; padding:0;">
                            <li style="display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--gray-100);">
                                <span style="font-size:22px; width:28px; text-align:center;">📍</span>
                                <div>
                                    <div style="font-weight:600; font-size:13px; color:var(--primary); margin-bottom:2px;">Address</div>
                                    <div style="font-size:14px; color:var(--text-muted);">Barangay Hall, Buck Estate,<br>Alfonso, Cavite 4123</div>
                                </div>
                            </li>
                            <li style="display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--gray-100);">
                                <span style="font-size:22px; width:28px; text-align:center;">📞</span>
                                <div>
                                    <div style="font-weight:600; font-size:13px; color:var(--primary); margin-bottom:2px;">Contact Number</div>
                                    <div style="font-size:14px; color:var(--text-muted);"><?= SITE_CONTACT ?></div>
                                </div>
                            </li>
                            <li style="display:flex; gap:14px; padding:14px 0; border-bottom:1px solid var(--gray-100);">
                                <span style="font-size:22px; width:28px; text-align:center;">✉️</span>
                                <div>
                                    <div style="font-weight:600; font-size:13px; color:var(--primary); margin-bottom:2px;">Email Address</div>
                                    <div style="font-size:14px; color:var(--text-muted);"><?= SITE_EMAIL ?></div>
                                </div>
                            </li>
                            <li style="display:flex; gap:14px; padding:14px 0;">
                                <span style="font-size:22px; width:28px; text-align:center;">🕐</span>
                                <div>
                                    <div style="font-weight:600; font-size:13px; color:var(--primary); margin-bottom:2px;">Office Hours</div>
                                    <div style="font-size:14px; color:var(--text-muted);">Mon–Fri: 8:00 AM – 5:00 PM<br>Saturday: 8:00 AM – 12:00 PM</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-2">
                    <div class="card__header"><h3 class="card__title">🗺 Location</h3></div>
                    <div class="card__body" style="padding:0; overflow:hidden; border-radius:0 0 var(--radius-lg) var(--radius-lg);">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3871.2!2d120.8532!3d14.0894!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMTTCsDA1JzIyLjEiTiAxMjDCsDUxJzExLjUiRQ!5e0!3m2!1sen!2sph!4v1600000000000!5m2!1sen!2sph"
                            width="100%" height="220" style="border:0; display:block;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
