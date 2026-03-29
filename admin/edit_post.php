<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
requireAdmin();

$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($postId <= 0) {
    header('Location: ../blog.php');
    exit;
}

$posts = loadData('posts.json');
$post = null;
foreach ($posts as $p) {
    if ($p['id'] == $postId) {
        $post = $p;
        break;
    }
}
if (!$post) {
    header('Location: ../blog.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать запись</title>
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
        <h1>Редактировать запись</h1>
        <form id="editForm">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            <label>Заголовок:</label>
            <input type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            <label>Текст:</label>
            <textarea name="content" rows="15" required><?= htmlspecialchars($post['content']) ?></textarea>
            <button type="submit">Сохранить изменения</button>
        </form>
        <div id="message"></div>
    </main>
    <script>
        document.getElementById('editForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const response = await fetch('../handlers/edit_post.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            const msgDiv = document.getElementById('message');
            if (result.success) {
                msgDiv.innerHTML = '<div class="success">Запись обновлена! <a href="../blog.php">Вернуться к блогу</a></div>';
            } else {
                msgDiv.innerHTML = '<div class="error">Ошибка: ' + result.message + '</div>';
            }
        });
    </script>
</body>
</html>