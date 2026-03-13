// Buck Estate Barangay Portal – Main JS

document.addEventListener('DOMContentLoaded', () => {

    // ── Mobile nav toggle ────────────────────────────────────
    const toggle = document.getElementById('navToggle');
    const menu   = document.getElementById('navMenu');
    if (toggle && menu) {
        toggle.addEventListener('click', () => {
            menu.classList.toggle('open');
        });
    }

    // ── Tab system ───────────────────────────────────────────
    document.querySelectorAll('[data-tabs]').forEach(container => {
        const buttons = container.querySelectorAll('.tab-btn');
        const panels  = container.querySelectorAll('.tab-panel');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                const target = btn.dataset.tab;
                buttons.forEach(b => b.classList.remove('active'));
                panels.forEach(p => p.classList.remove('active'));
                btn.classList.add('active');
                const panel = container.querySelector('#' + target);
                if (panel) panel.classList.add('active');
            });
        });
    });

    // ── Mobile dropdown nav ──────────────────────────────────
    document.querySelectorAll('.navbar__dropdown').forEach(el => {
        const link = el.querySelector('.navbar__link');
        if (link) {
            link.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    el.classList.toggle('open');
                }
            });
        }
    });

    // ── Auto-dismiss flash messages ───────────────────────────
    document.querySelectorAll('.flash').forEach(flash => {
        setTimeout(() => {
            flash.style.transition = 'opacity .4s';
            flash.style.opacity = '0';
            setTimeout(() => flash.remove(), 400);
        }, 4500);
    });

    // ── Confirm dialogs ───────────────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(el => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) e.preventDefault();
        });
    });

    // ── Password toggle visibility ────────────────────────────
    document.querySelectorAll('[data-toggle-password]').forEach(btn => {
        const targetId = btn.dataset.togglePassword;
        const input = document.getElementById(targetId);
        if (!input) return;
        btn.addEventListener('click', () => {
            const isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            btn.textContent = isText ? '👁' : '🙈';
        });
    });

    // ── Character counter for textareas ───────────────────────
    document.querySelectorAll('textarea[maxlength]').forEach(ta => {
        const max = ta.getAttribute('maxlength');
        const counter = document.createElement('div');
        counter.className = 'form-note';
        counter.textContent = `0 / ${max}`;
        ta.parentNode.appendChild(counter);
        ta.addEventListener('input', () => {
            counter.textContent = `${ta.value.length} / ${max}`;
        });
    });

    // ── Scroll Reveal Animations ────────────────────────────────
    const animatedEls = document.querySelectorAll('.fade-in, .fade-in-left, .fade-in-right, .scale-in, .stagger-children');
    if (animatedEls.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        animatedEls.forEach(el => observer.observe(el));
    }

    // ── Smooth parallax for hero section ────────────────────────
    const heroPhoto = document.querySelector('.hero__photo');
    if (heroPhoto) {
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            if (scrolled < 600) {
                heroPhoto.style.transform = `rotate(${1.5 - scrolled * 0.003}deg) translateY(${scrolled * 0.08}px)`;
            }
        }, { passive: true });
    }

});

// ── Notification dismiss (global scope) ─────────────────────
function dismissNotif(id, btn) {
    const item = btn.closest('.notif-item');
    fetch('/api/dismiss-notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    });
    item.style.transition = 'opacity .3s, max-height .3s, padding .3s, margin .3s';
    item.style.opacity = '0';
    item.style.maxHeight = '0';
    item.style.padding = '0';
    item.style.marginBottom = '0';
    item.style.overflow = 'hidden';
    setTimeout(() => {
        item.remove();
        const bar = document.getElementById('notifBar');
        if (bar && bar.querySelectorAll('.notif-item').length === 0) {
            bar.remove();
        }
    }, 300);
}

function dismissAllNotifs() {
    fetch('/api/dismiss-notification.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ all: true })
    });
    const bar = document.getElementById('notifBar');
    if (bar) {
        bar.style.transition = 'opacity .3s';
        bar.style.opacity = '0';
        setTimeout(() => bar.remove(), 300);
    }
}
