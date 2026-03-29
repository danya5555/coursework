<?php
session_start();
require_once 'includes/functions.php';

$lastPosts = getLastPosts(3);          // 3 последних поста
$totalPosts = getTotalPosts();
$totalUsers = getTotalUsers();
$totalComments = getTotalComments();
$lastComments = getLastComments(5);

$userStats = null;
if (isset($_SESSION['user'])) {
    $userStats = getUserStats($_SESSION['user']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Личная страница</title>
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
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main>
        <!-- 1. Приветствие -->
        <div class="welcome-block">
            <?php if (isset($_SESSION['user'])): ?>
                <h1>Добро пожаловать, <?= htmlspecialchars($_SESSION['user']) ?>!</h1>
            <?php else: ?>
                <h1>Добро пожаловать на мой сайт!</h1>
                <p>Здесь вы найдёте мои мысли, проекты и полезные заметки.</p>
            <?php endif; ?>
        </div>

        <div class="home-grid">
            <!-- 3. Счетчики -->
            <div class="stats-block">
                <h2>Статистика сайта</h2>
                <ul>
                    <li>📝 Записей в блоге: <strong><?= $totalPosts ?></strong></li>
                    <li>👥 Зарегистрированных пользователей: <strong><?= $totalUsers ?></strong></li>
                    <li>💬 Комментариев: <strong><?= $totalComments ?></strong></li>
                </ul>
            </div>

            <!-- 2. Последние записи -->
            <div class="recent-posts">
                <h2>Последние записи</h2>
                <?php if (empty($lastPosts)): ?>
                    <p>Пока нет записей. Загляните позже!</p>
                <?php else: ?>
                    <?php foreach ($lastPosts as $post): ?>
                        <div class="recent-post">
                            <h3><a href="blog.php#post-<?= $post['id'] ?>"><?= htmlspecialchars($post['title']) ?></a></h3>
                            <div class="meta"><?= $post['date'] ?></div>
                            <p><?= nl2br(htmlspecialchars(mb_substr($post['content'], 0, 150))) ?>...</p>
                            <a href="blog.php#post-<?= $post['id'] ?>" class="read-more">Читать далее →</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- 5. Последние комментарии -->
            <div class="recent-comments">
                <h2>Последние комментарии</h2>
                <?php if (empty($lastComments)): ?>
                    <p>Пока нет комментариев. Будьте первым!</p>
                <?php else: ?>
                    <?php foreach ($lastComments as $comment): ?>
                        <div class="comment-item">
                            <strong><?= htmlspecialchars($comment['user']) ?></strong>
                            <span class="comment-date"><?= $comment['date'] ?></span>
                            <p><?= htmlspecialchars(mb_substr($comment['text'], 0, 100)) ?>…</p>
                            <a href="blog.php#post-<?= $comment['post_id'] ?>" class="comment-link">К записи «<?= htmlspecialchars($comment['post_title']) ?>»</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- 4. Призыв к действию / ссылки для авторизованных -->
            <div class="action-block">
                <h2>Что дальше?</h2>
                <?php if (isset($_SESSION['user'])): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <p>Вы администратор. Вы можете создавать новые записи в блоге.</p>
                        <a href="admin/add_post.php" class="button">➕ Добавить запись</a>
                    <?php else: ?>
                        <p>Вы авторизованы. Ставьте лайки и оставляйте комментарии!</p>
                        <a href="blog.php" class="button">📖 Перейти в блог</a>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Присоединяйтесь! Зарегистрируйтесь, чтобы комментировать и оценивать записи.</p>
                    <a href="register.php" class="button">🔑 Зарегистрироваться</a>
                    <a href="login.php" class="button secondary">Войти</a>
                <?php endif; ?>
            </div>

            <!-- 6. Статистика пользователя (только для авторизованных) -->
            <?php if (isset($_SESSION['user']) && $userStats): ?>
                <div class="user-stats">
                    <h2>Ваша активность</h2>
                    <ul>
                        <li>❤️ Лайков поставлено: <strong><?= $userStats['likes'] ?></strong></li>
                        <li>💬 Комментариев оставлено: <strong><?= $userStats['comments'] ?></strong></li>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- 8. Блок "Интересное" (цитата дня) -->
            <div class="quote-block">
                <h2>Интересное</h2>
                <?php
                $quotes = [
                    "«Код пишется для людей, а не для машин.»",
                    "«Простота — это высшая сложность.»",
                    "«Не бойтесь ошибаться — бойтесь не пробовать.»",
                    "«Лучший прогноз — это создание будущего.»",
                    "«Учитесь всю жизнь, это окупается.»"
                ];
                $randomQuote = $quotes[array_rand($quotes)];
                ?>
                <blockquote><?= $randomQuote ?></blockquote>
                <p class="quote-author">— из архива мыслей</p>
            </div>
        </div>
    </main>
</body>
</html>