<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$deletedPosts = getDeletedPosts();
usort($deletedPosts, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Корзина</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="../index.php">Главная</a></li>
                <li><a href="../about.php">Обо мне</a></li>
                <li><a href="../blog.php">Блог</a></li>
                <li><a href="add_post.php">Добавить запись</a></li>
                <li><a href="trash.php">Корзина</a></li>
                <li><a href="../logout.php">Выйти</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Корзина</h1>
        <?php if (empty($deletedPosts)): ?>
            <p>Корзина пуста.</p>
        <?php else: ?>
            <?php foreach ($deletedPosts as $post): ?>
                <div class="post" data-id="<?= $post['id'] ?>">
                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                    <div class="content">
                        <?php
                        $allowedTags = '<b><strong><i><em><p><br>';
                        echo strip_tags(nl2br($post['content']), $allowedTags);
                        ?>
                    </div>
                    <div class="meta">Дата: <?= $post['date'] ?></div>
                    <div class="like-count">👍 Лайков: <?= count($post['likes']) ?></div>
                    <div class="comments">
                        <h4>Комментарии</h4>
                        <?php if (empty($post['comments'])): ?>
                            <p>Комментариев нет.</p>
                        <?php else: ?>
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="comment">
                                    <strong><?= htmlspecialchars($comment['user']) ?></strong>
                                    <span class="comment-date"><?= $comment['date'] ?></span>
                                    <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button class="restore-post" data-id="<?= $post['id'] ?>">♻️ Восстановить</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <script>
        document.querySelectorAll('.restore-post').forEach(btn => {
            btn.addEventListener('click', async function() {
                const postId = this.dataset.id;
                const response = await fetch('../handlers/restore_post.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ post_id: postId })
                });
                const result = await response.json();
                if (result.success) {
                    this.closest('.post').remove();
                } else {
                    alert(result.message);
                }
            });
        });
    </script>
</body>
</html>