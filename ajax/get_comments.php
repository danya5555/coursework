<?php
require_once dirname(__DIR__) . '/includes/functions.php';

header('Content-Type: application/json');

$post_id = (int)($_GET['post_id'] ?? 0);
if (!$post_id) {
    echo json_encode([]);
    exit;
}

$comments = getCommentsByPost($post_id);
foreach ($comments as &$c) {
    $c['date'] = date('d.m.Y H:i', strtotime($c['date']));
}
echo json_encode($comments);
?>