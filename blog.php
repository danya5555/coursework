<?php
session_start();
require_once 'includes/functions.php';
$allPosts = loadData('posts.json');
$posts = array_filter($allPosts, function($post) {
    return !$post['deleted'];
});
usort($posts, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Блог</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="about.php">Обо мне</a></li>
                <li><a href="blog.php">Блог</a></li>
                <?php if (isset($_SESSION['user'])): ?>
                    <li><a href="logout.php">Выйти (<?= htmlspecialchars($_SESSION['user']) ?>)</a></li>
                <?php else: ?>
                    <li><a href="login.php">Вход</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin/add_post.php">Добавить запись</a></li>
                    <li><a href="admin/trash.php">Корзина</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Блог</h1>
        <?php if (empty($posts)): ?>
            <p>Пока нет записей.</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post" data-id="<?= $post['id'] ?>">
                    <h2><?= htmlspecialchars($post['title']) ?></h2>
                    <div class="content"><?= nl2br(htmlspecialchars($post['content'])) ?></div>
                    <div class="meta">Дата: <?= $post['date'] ?></div>

                    <!-- Лайки -->
                    <?php if (isset($_SESSION['user'])): ?>
                        <div class="like-section">
                            <button class="like-btn" data-id="<?= $post['id'] ?>">
                                👍 Лайк (<span class="likes-count"><?= count($post['likes']) ?></span>)
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="like-count">👍 Лайков: <?= count($post['likes']) ?></div>
                    <?php endif; ?>

                    <!-- Комментарии -->
                    <div class="comments">
                        <h4>Комментарии</h4>
                        <?php if (empty($post['comments'])): ?>
                            <p>Комментариев пока нет.</p>
                        <?php else: ?>
                            <?php foreach ($post['comments'] as $comment): ?>
                                <div class="comment">
                                    <strong><?= htmlspecialchars($comment['user']) ?></strong>
                                    <span class="comment-date"><?= $comment['date'] ?></span>
                                    <p><?= nl2br(htmlspecialchars($comment['text'])) ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['user'])): ?>
                            <form class="comment-form" data-id="<?= $post['id'] ?>">
                                <textarea name="comment" placeholder="Ваш комментарий" required></textarea>
                                <button type="submit">Отправить</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Административные кнопки -->
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="admin/edit_post.php?id=<?= $post['id'] ?>" class="edit-post-link">✏️ Редактировать</a>
                        <button class="delete-post" data-id="<?= $post['id'] ?>">🗑 Удалить пост</button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
    <script src="js/blog.js"></script>
</body>
</html>