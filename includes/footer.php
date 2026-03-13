</main>

<!-- FOOTER -->
<footer class="footer">
    <div class="container footer__inner">
        <div class="footer__col">
            <div class="footer__brand">
                <img src="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/', 1)) ?>assets/img/logo.svg"
                     alt="Logo" class="footer__logo"
                     onerror="this.style.display='none'">
                <span class="footer__brand-name">Brgy. Buck Estate</span>
            </div>
            <p class="footer__desc">Serving the residents of Barangay Buck Estate, Alfonso, Cavite with transparency and efficiency.</p>
            <!-- Facebook Community -->
            <a href="https://www.facebook.com/groups/168372726641403"
               target="_blank" rel="noopener noreferrer"
               class="footer__fb-btn">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="18" height="18" style="flex-shrink:0;">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
                Join Our Facebook Community
            </a>
        </div>
        <div class="footer__col">
            <h4 class="footer__heading">Quick Links</h4>
            <ul class="footer__links">
                <li><a href="/index.php">Home</a></li>
                <li><a href="/about.php">About the Barangay</a></li>
                <li><a href="/announcements.php">Announcements</a></li>
                <li><a href="/contact.php">Contact Us</a></li>
            </ul>
        </div>
        <div class="footer__col">
            <h4 class="footer__heading">Services</h4>
            <ul class="footer__links">
                <li><a href="/auth/register.php">Create Account</a></li>
                <li><a href="/resident/document-request-new.php">Request Document</a></li>
                <li><a href="/resident/blotter-new.php">File Blotter Report</a></li>
                <li><a href="/resident/financial-new.php">Financial Assistance</a></li>
            </ul>
        </div>
        <div class="footer__col">
            <h4 class="footer__heading">Contact</h4>
            <ul class="footer__contact">
                <li>📍 <?= SITE_ADDRESS ?></li>
                <li>📞 <?= SITE_CONTACT ?></li>
                <li>✉️ <?= SITE_EMAIL ?></li>
            </ul>
            <div style="margin-top:16px; padding:12px; background:rgba(255,255,255,0.07); border-radius:8px; font-size:13px; color:rgba(255,255,255,0.7); line-height:1.7;">
                🕐 <strong style="color:#fff;">Office Hours</strong><br>
                Mon–Fri: 8:00 AM – 5:00 PM<br>
                Saturday: 8:00 AM – 12:00 PM
            </div>
        </div>
    </div>
    <div class="footer__bottom">
        <div class="container" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px;">
            <p style="margin:0;">© <?= date('Y') ?> Barangay Buck Estate, Alfonso, Cavite. All rights reserved.</p>
            <a href="https://www.facebook.com/groups/168372726641403" target="_blank" rel="noopener noreferrer"
               style="color:rgba(255,255,255,0.6); font-size:13px; text-decoration:none; display:flex; align-items:center; gap:6px;">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                Buck Estate Community Group
            </a>
        </div>
    </div>
</footer>

<script src="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/', 1)) ?>assets/js/main.js"></script>
<?= isset($extraScript) ? $extraScript : '' ?>
</body>
</html>
