<?php
header('Content-Type: application/json');
require_once '../includes/auth.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Необходимо войти']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$postId = $input['post_id'] ?? 0;
$commentText = trim($input['comment'] ?? '');

if ($postId <= 0 || empty($commentText)) {
    echo json_encode(['success' => false, 'message' => 'Заполните комментарий']);
    exit;
}

if (addComment($postId, $_SESSION['user'], $commentText)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка добавления комментария']);
}