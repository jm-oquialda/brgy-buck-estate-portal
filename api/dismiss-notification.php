<?php
require_once __DIR__ . '/../includes/functions.php';
startSession();

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$userId = $_SESSION['user_id'];
$input  = json_decode(file_get_contents('php://input'), true);
$id     = (int)($input['id'] ?? 0);
$all    = !empty($input['all']);

if ($all) {
    dismissAllNotifications($userId);
    echo json_encode(['ok' => true]);
} elseif ($id) {
    dismissNotification($id, $userId);
    echo json_encode(['ok' => true]);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Missing notification id']);
}
