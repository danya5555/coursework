<?php
/**
 * Функции аутентификации и управления сессиями
 */
require_once __DIR__ . '/functions.php';

/**
 * Регистрация нового пользователя (всегда с ролью 'user')
 * @param string $username
 * @param string $password
 * @return bool true при успехе, false если логин уже существует
 */
function registerUser($username, $password) {
    $users = loadData('users.json');
    foreach ($users as $user) {
        if ($user['username'] === $username) {
            return false; // логин занят
        }
    }
    $newId = getNextId($users);
    $users[] = [
        'id' => $newId,
        'username' => $username,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => 'user'
    ];
    return saveData('users.json', $users);
}

/**
 * Авторизация пользователя
 * @param string $username
 * @param string $password
 * @return bool
 */
function loginUser($username, $password) {
    $users = loadData('users.json');
    foreach ($users as $user) {
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user'] = $username;
            $_SESSION['role'] = $user['role'];
            return true;
        }
    }
    return false;
}

/**
 * Проверка, авторизован ли пользователь
 * @return bool
 */
function isLoggedIn() {
    session_start();
    return isset($_SESSION['user']);
}

/**
 * Проверка, является ли текущий пользователь администратором
 * @return bool
 */
function isAdmin() {
    session_start();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Перенаправляет на страницу входа, если пользователь не администратор
 */
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Перенаправляет на страницу входа, если пользователь не авторизован
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit;
    }
}