<?php
session_start();
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Не авторизован']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$post_id = (int)($input['post_id'] ?? 0);

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID поста']);
    exit;
}

$result = toggleLike($post_id, $_SESSION['user_id']);
if ($result) {
    echo json_encode(['success' => true, 'liked' => $result['liked'], 'count' => $result['count']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Пост не найден']);
}
?>