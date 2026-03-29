<?php
header('Content-Type: application/json');
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin(); // только администратор

$input = json_decode(file_get_contents('php://input'), true);
$postId = $input['post_id'] ?? 0;

if ($postId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID']);
    exit;
}

if (restorePost($postId)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка восстановления']);
}