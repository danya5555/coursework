<?php
/**
 * Функции для работы с JSON-файлами
 */

define('DATA_DIR', __DIR__ . '/../data/');

function loadData($filename) {
    $path = DATA_DIR . $filename;
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    $data = json_decode($content, true) ?? [];

    // Если загружаем посты, убедимся, что у каждого есть поля comments, likes и deleted
    if ($filename === 'posts.json') {
        foreach ($data as &$post) {
            if (!isset($post['comments'])) $post['comments'] = [];
            if (!isset($post['likes'])) $post['likes'] = [];
            if (!isset($post['deleted'])) $post['deleted'] = false;
        }
    }

    return $data;
}

function saveData($filename, $data) {
    $path = DATA_DIR . $filename;
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fp = fopen($path, 'w');
    if (flock($fp, LOCK_EX)) {
        fwrite($fp, $json);
        flock($fp, LOCK_UN);
        fclose($fp);
        return true;
    }
    fclose($fp);
    return false;
}

function getNextId($data) {
    if (empty($data)) return 1;
    $maxId = max(array_column($data, 'id'));
    return $maxId + 1;
}

// === Лайки и комментарии ===
function addLike($postId, $username) {
    $posts = loadData('posts.json');
    foreach ($posts as &$post) {
        if ($post['id'] == $postId && !$post['deleted']) {
            if (!in_array($username, $post['likes'])) {
                $post['likes'][] = $username;
                saveData('posts.json', $posts);
                return true;
            }
            return false;
        }
    }
    return false;
}

function addComment($postId, $username, $commentText) {
    $posts = loadData('posts.json');
    foreach ($posts as &$post) {
        if ($post['id'] == $postId && !$post['deleted']) {
            $post['comments'][] = [
                'user' => htmlspecialchars($username),
                'text' => htmlspecialchars($commentText),
                'date' => date('Y-m-d H:i:s')
            ];
            saveData('posts.json', $posts);
            return true;
        }
    }
    return false;
}

// === Новые функции для главной страницы ===
function getLastPosts($limit = 5) {
    $posts = loadData('posts.json');
    // Фильтруем только не удалённые
    $activePosts = array_filter($posts, function($post) {
        return !$post['deleted'];
    });
    usort($activePosts, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    return array_slice($activePosts, 0, $limit);
}

function getTotalPosts() {
    $posts = loadData('posts.json');
    $active = array_filter($posts, function($p) { return !$p['deleted']; });
    return count($active);
}

function getTotalUsers() {
    return count(loadData('users.json'));
}

function getTotalComments() {
    $posts = loadData('posts.json');
    $total = 0;
    foreach ($posts as $post) {
        if (!$post['deleted']) {
            $total += count($post['comments']);
        }
    }
    return $total;
}

function getLastComments($limit = 5) {
    $posts = loadData('posts.json');
    $allComments = [];
    foreach ($posts as $post) {
        if (!$post['deleted']) {
            foreach ($post['comments'] as $comment) {
                $allComments[] = [
                    'post_id' => $post['id'],
                    'post_title' => $post['title'],
                    'user' => $comment['user'],
                    'text' => $comment['text'],
                    'date' => $comment['date']
                ];
            }
        }
    }
    usort($allComments, function($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });
    return array_slice($allComments, 0, $limit);
}

function getUserStats($username) {
    $posts = loadData('posts.json');
    $likesCount = 0;
    $commentsCount = 0;
    foreach ($posts as $post) {
        if (!$post['deleted']) {
            if (in_array($username, $post['likes'])) {
                $likesCount++;
            }
            foreach ($post['comments'] as $comment) {
                if ($comment['user'] === $username) {
                    $commentsCount++;
                }
            }
        }
    }
    return ['likes' => $likesCount, 'comments' => $commentsCount];
}

// === Функции для корзины ===
function deletePost($postId) {
    $posts = loadData('posts.json');
    foreach ($posts as &$post) {
        if ($post['id'] == $postId) {
            $post['deleted'] = true;
            return saveData('posts.json', $posts);
        }
    }
    return false;
}

function restorePost($postId) {
    $posts = loadData('posts.json');
    foreach ($posts as &$post) {
        if ($post['id'] == $postId) {
            $post['deleted'] = false;
            return saveData('posts.json', $posts);
        }
    }
    return false;
}

function getDeletedPosts() {
    $posts = loadData('posts.json');
    return array_filter($posts, function($post) {
        return $post['deleted'];
    });
}