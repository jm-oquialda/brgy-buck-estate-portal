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

// ─── Notifications ──────────────────────────────────────────
function createNotification(int $userId, string $type, string $title, string $message, string $link = ''): void {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO notifications (user_id, type, title, message, link) VALUES (?,?,?,?,?)");
    $stmt->execute([$userId, $type, $title, $message, $link ?: null]);
}

function getUnreadNotifications(int $userId): array {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? AND is_read=0 ORDER BY created_at DESC LIMIT 10");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

function dismissNotification(int $notifId, int $userId): bool {
    $pdo = getDB();
    $stmt = $pdo->prepare("UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?");
    $stmt->execute([$notifId, $userId]);
    return $stmt->rowCount() > 0;
}

function dismissAllNotifications(int $userId): void {
    $pdo = getDB();
    $pdo->prepare("UPDATE notifications SET is_read=1 WHERE user_id=? AND is_read=0")->execute([$userId]);
}

// ─── File Uploads ───────────────────────────────────────────
function handleFileUploads(string $refType, int $refId, int $userId): array {
    $errors = [];
    if (empty($_FILES['attachments']['name'][0])) return $errors;

    $uploadDir = __DIR__ . '/../uploads/' . $refType . '/' . $refId;
    $maxSize   = 5 * 1024 * 1024; // 5MB
    $allowed   = ['jpg','jpeg','png','gif','pdf','doc','docx'];

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO attachments (ref_type, ref_id, file_name, file_path, file_size, uploaded_by) VALUES (?,?,?,?,?,?)");

    foreach ($_FILES['attachments']['name'] as $i => $name) {
        if ($_FILES['attachments']['error'][$i] !== UPLOAD_ERR_OK) continue;
        if (empty($name)) continue;

        $size = $_FILES['attachments']['size'][$i];
        $tmp  = $_FILES['attachments']['tmp_name'][$i];
        $ext  = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = sanitize($name) . ': File type not allowed.';
            continue;
        }
        if ($size > $maxSize) {
            $errors[] = sanitize($name) . ': File exceeds 5MB limit.';
            continue;
        }

        $safeName = time() . '_' . $i . '.' . $ext;
        $dest     = $uploadDir . '/' . $safeName;
        $webPath  = '/uploads/' . $refType . '/' . $refId . '/' . $safeName;

        if (move_uploaded_file($tmp, $dest)) {
            $stmt->execute([$refType, $refId, $name, $webPath, $size, $userId]);
        } else {
            $errors[] = sanitize($name) . ': Upload failed.';
        }
    }
    return $errors;
}

function getAttachments(string $refType, int $refId): array {
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM attachments WHERE ref_type=? AND ref_id=? ORDER BY uploaded_at ASC");
    $stmt->execute([$refType, $refId]);
    return $stmt->fetchAll();
}

function formatFileSize(int $bytes): string {
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
    return $bytes . ' B';
}

function isImageFile(string $path): bool {
    $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
    return in_array($ext, ['jpg','jpeg','png','gif']);
}

function renderAttachments(array $attachments): string {
    if (empty($attachments)) return '';
    $html = '<div class="attachments"><span class="form-label">Attachments (' . count($attachments) . ')</span><div class="attachment-list">';
    foreach ($attachments as $a) {
        $name = sanitize($a['file_name']);
        $path = sanitize($a['file_path']);
        $size = formatFileSize($a['file_size']);
        if (isImageFile($a['file_path'])) {
            $html .= '<a href="' . $path . '" target="_blank" class="attachment-item attachment-item--image">';
            $html .= '<img src="' . $path . '" alt="' . $name . '" loading="lazy">';
            $html .= '<span class="attachment-item__name">' . $name . '</span>';
            $html .= '<span class="attachment-item__size">' . $size . '</span></a>';
        } else {
            $html .= '<a href="' . $path . '" target="_blank" class="attachment-item">';
            $html .= '<span class="attachment-item__icon">📎</span>';
            $html .= '<span class="attachment-item__name">' . $name . '</span>';
            $html .= '<span class="attachment-item__size">' . $size . '</span></a>';
        }
    }
    $html .= '</div></div>';
    return $html;
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
