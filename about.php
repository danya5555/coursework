<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Обо мне</title>
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
        <div class="content-page">
            <h1>Обо мне</h1>
            <p>Меня зовут Даниил. Я увлекаюсь веб-разработкой и создаю интересные проекты.</p>
            <p>Этот сайт — моя курсовая работа, демонстрирующая навыки работы с PHP, JavaScript и AJAX.</p>
        </div>
    </main>
</body>
</html>