<?php
// Стартуем сессию и подключаем конфигурации
require_once '../includes/config.php';
require_once '../includes/functions.php';

// 1. Очищаем все данные сессии
$_SESSION = [];

// 2. Удаляем куки сессии
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Уничтожаем сессию полностью
session_destroy();

// 4. Чистим кеш браузера
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
header("Pragma: no-cache"); // HTTP 1.0
header("Expires: 0"); // Proxies

// 5. Перенаправляем с дополнительными мерами
header("Location: login.php?logout=1");
exit;
?>

<h1>Вход в систему</h1>

<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="username">Имя пользователя:</label>
        <input type="text" id="username" name="username" required>
    </div>
    
    <div class="form-group">
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    
    <button type="submit" class="btn">Войти</button>
</form>

<p>Нет аккаунта? <a href="register.php">Зарегистрироваться</a></p>

<!-- Подключаем подвал сайта -->
<?php include 'includes/footer.php'; ?>