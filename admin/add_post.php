<?php
require_once '../includes/auth.php';
requireAdmin();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить запись</title>
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
        <h1>Добавить новую запись</h1>
        <form id="postForm">
            <label>Заголовок:</label>
            <input type="text" name="title" required>
            <label>Текст:</label>
            <textarea name="content" rows="10" required></textarea>
            <button type="submit">Опубликовать</button>
        </form>
        <div id="message"></div>
    </main>
    <script>
        document.getElementById('postForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const response = await fetch('../handlers/add_post.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            const msgDiv = document.getElementById('message');
            if (result.success) {
                msgDiv.innerHTML = '<div class="success">Запись добавлена!</div>';
                this.reset();
            } else {
                msgDiv.innerHTML = '<div class="error">Ошибка: ' + result.message + '</div>';
            }
        });
    </script>
</body>
</html>