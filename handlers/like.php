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
$username = $_SESSION['user'];

if ($postId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Неверный ID поста']);
    exit;
}

if (addLike($postId, $username)) {
    // Получаем обновлённое количество лайков
    $posts = loadData('posts.json');
    $likesCount = 0;
    foreach ($posts as $p) {
        if ($p['id'] == $postId) {
            $likesCount = count($p['likes']);
            break;
        }
    }
    echo json_encode(['success' => true, 'likes' => $likesCount]);
} else {
    echo json_encode(['success' => false, 'message' => 'Вы уже поставили лайк или пост не найден']);
}