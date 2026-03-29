<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: blog.php');
    exit;
}
require_once 'includes/auth.php';

$error = '';
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    if (empty($username) || empty($password)) {
        $error = 'Заполните все поля';
    } elseif ($password !== $confirm) {
        $error = 'Пароли не совпадают';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } else {
        if (registerUser($username, $password)) {
            $success = true;
        } else {
            $error = 'Логин уже занят';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Главная</a></li>
                <li><a href="about.php">Обо мне</a></li>
                <li><a href="blog.php">Блог</a></li>
                <li><a href="login.php">Вход</a></li>
                <li><a href="register.php">Регистрация</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form method="post">
            <h2>Регистрация</h2>
            <?php if ($error): ?>
                <div class="error"><?= $error ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success">Регистрация успешна! <a href="login.php">Войти</a></div>
            <?php else: ?>
                <input type="text" name="username" placeholder="Логин" required>
                <input type="password" name="password" placeholder="Пароль (мин. 6 символов)" required>
                <input type="password" name="confirm" placeholder="Подтвердите пароль" required>
                <button type="submit">Зарегистрироваться</button>
                <p>Уже есть аккаунт? <a href="login.php">Войти</a></p>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>