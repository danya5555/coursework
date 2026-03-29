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
$content = trim($input['content'] ?? '');

if (!$post_id) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID поста']);
    exit;
}
if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Комментарий не может быть пустым']);
    exit;
}

$post = getPostById($post_id);
if (!$post) {
    echo json_encode(['success' => false, 'message' => 'Пост не найден']);
    exit;
}

$result = addComment($post_id, $_SESSION['user_id'], $_SESSION['username'], $content);
echo json_encode(['success' => $result, 'message' => $result ? 'Комментарий добавлен' : 'Ошибка']);
?>