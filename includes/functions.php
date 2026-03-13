<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/db.php';

// ─── Session ────────────────────────────────────────────────
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    startSession();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: /auth/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: /resident/dashboard.php');
        exit;
    }
}

function requireResident(): void {
    requireLogin();
    if (isAdmin()) {
        header('Location: /admin/dashboard.php');
        exit;
    }
}

function currentUser(): ?array {
    if (!isLoggedIn()) return null;
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

// ─── Flash Messages ─────────────────────────────────────────
function setFlash(string $type, string $message): void {
    startSession();
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    startSession();
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function renderFlash(): string {
    $flash = getFlash();
    if (!$flash) return '';
    $icons = ['success' => '✓', 'error' => '✕', 'warning' => '⚠', 'info' => 'ℹ'];
    $icon = $icons[$flash['type']] ?? 'ℹ';
    return '<div class="flash flash--' . $flash['type'] . '"><span class="flash__icon">' . $icon . '</span>' . htmlspecialchars($flash['message']) . '</div>';
}

// ─── Security ───────────────────────────────────────────────
function sanitize(string $value): string {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string {
    startSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void {
    startSession();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        setFlash('error', 'Invalid form submission. Please try again.');
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

// ─── Pagination ─────────────────────────────────────────────
function paginate(int $total, int $perPage, int $current): array {
    $totalPages = (int)ceil($total / $perPage);
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $current,
        'total_pages' => $totalPages,
        'offset'      => ($current - 1) * $perPage,
        'has_prev'    => $current > 1,
        'has_next'    => $current < $totalPages,
    ];
}

// ─── Date helpers ───────────────────────────────────────────
function formatDate(string $date, string $format = 'F j, Y'): string {
    return date($format, strtotime($date));
}

function formatDateTime(string $date): string {
    return date('F j, Y g:i A', strtotime($date));
}

function timeAgo(string $datetime): string {
    $now  = time();
    $then = strtotime($datetime);
    $diff = $now - $then;
    if ($diff < 60)     return 'Just now';
    if ($diff < 3600)   return floor($diff / 60) . 'm ago';
    if ($diff < 86400)  return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';
    return formatDate($datetime);
}

// ─── Status badge ────────────────────────────────────────────
function statusBadge(string $status): string {
    $map = [
        'Pending'      => 'badge--warning',
        'Processing'   => 'badge--info',
        'Approved'     => 'badge--success',
        'Denied'       => 'badge--danger',
        'Filed'        => 'badge--warning',
        'Under Review' => 'badge--info',
        'Resolved'     => 'badge--success',
        'Dismissed'    => 'badge--danger',
        'published'    => 'badge--success',
        'draft'        => 'badge--warning',
        'archived'     => 'badge--secondary',
        'active'       => 'badge--success',
        'inactive'     => 'badge--danger',
    ];
    $class = $map[$status] ?? 'badge--secondary';
    return '<span class="badge ' . $class . '">' . htmlspecialchars($status) . '</span>';
}
