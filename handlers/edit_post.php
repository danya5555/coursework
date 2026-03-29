<?php
header('Content-Type: application/json');
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if ($id <= 0 || empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}

$posts = loadData('posts.json');
$updated = false;
foreach ($posts as &$post) {
    if ($post['id'] == $id) {
        $post['title'] = htmlspecialchars($title);
        $post['content'] = htmlspecialchars($content);
        // $post['updated_at'] = date('Y-m-d H:i:s');
        $updated = true;
        break;
    }
}

if ($updated && saveData('posts.json', $posts)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка сохранения']);
}