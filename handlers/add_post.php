<?php
header('Content-Type: application/json');
require_once '../includes/auth.php';
require_once '../includes/functions.php';

requireAdmin();

$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if (empty($title) || empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
    exit;
}

$posts = loadData('posts.json');
$newId = getNextId($posts);
$posts[] = [
    'id' => $newId,
    'title' => htmlspecialchars($title),
    'content' => htmlspecialchars($content),
    'date' => date('Y-m-d'),
    'comments' => [],
    'likes' => [],
    'deleted' => false
];

if (saveData('posts.json', $posts)) {
    echo json_encode(['success' => true, 'post_id' => $newId]);
} else {
    echo json_encode(['success' => false, 'message' => 'Ошибка сохранения']);
}