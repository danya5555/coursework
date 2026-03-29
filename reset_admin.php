<?php
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$message = '';
$error = '';

// Функция для создания администратора
function createAdmin($username, $password) {
    $users = loadData('users.json');
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return false; // уже существует
        }
    }
    $newId = getNextId($users);
    $users[] = [
        'id' => $newId,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'admin'
    ];
    saveData('users.json', $users);
    return true;
}

// Функция для обновления пароля администратора
function updateAdminPassword($username, $newPassword) {
    $users = loadData('users.json');
    $found = false;
    foreach ($users as &$user) {
        if ($user['username'] === $username && $user['role'] === 'admin') {
            $user['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            $found = true;
            break;
        }
    }
    if ($found) {
        saveData('users.json', $users);
        return true;
    }
    return false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $action = $_POST['action'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } elseif ($action === 'create') {
        if (createAdmin($username, $password)) {
            $message = "Администратор $username создан! Теперь войдите.";
        } else {
            $error = "Пользователь $username уже существует. Используйте 'Обновить пароль'.";
        }
    } elseif ($action === 'update') {
        if (updateAdminPassword($username, $password)) {
            $message = "Пароль для администратора $username обновлён.";
        } else {
            $error = "Администратор $username не найден. Сначала создайте его.";
        }
    }
}

// Получить список всех администраторов для справки
$users = loadData('users.json');
$admins = array_filter($users, function($user) {
    return $user['role'] === 'admin';
});
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сброс администратора</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container" style="max-width: 600px; margin: 50px auto;">
        <h1>Управление администратором</h1>
        
        <?php if ($message): ?>
            <div class="success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <h2>Создать нового администратора</h2>
            <input type="text" name="username" placeholder="Логин администратора" required>
            <input type="password" name="password" placeholder="Пароль" required>
            <button type="submit" name="action" value="create">Создать администратора</button>
        </form>

        <hr>

        <form method="post">
            <h2>Обновить пароль существующего администратора</h2>
            <input type="text" name="username" placeholder="Логин администратора" required>
            <input type="password" name="password" placeholder="Новый пароль" required>
            <button type="submit" name="action" value="update">Обновить пароль</button>
        </form>

        <hr>

        <h3>Существующие администраторы:</h3>
        <?php if (empty($admins)): ?>
            <p>Администраторов пока нет.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($admins as $admin): ?>
                    <li><?= htmlspecialchars($admin['username']) ?> (ID: <?= $admin['id'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <p><a href="index.php">На главную</a> | <a href="login.php">Войти</a></p>
    </div>
</body>
</html>