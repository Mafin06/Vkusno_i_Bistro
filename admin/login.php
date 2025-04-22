<?php
// Подключаем необходимые файлы в самом начале
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Инициализируем переменную для ошибок
$error = '';

// Проверяем, была ли отправлена форма
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем и очищаем введенные данные
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Проверяем, что поля не пустые
    if (empty($username) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } else {
        // Подготавливаем запрос к базе данных
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Проверяем пользователя и пароль с password_verify()
        if ($user && password_verify($password, $user['password'])) {
            // Устанавливаем данные сессии
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['loggedin'] = true;
            
            // Перенаправляем в зависимости от роли
            switch ($user['role']) {
                case 'service_admin':
                    header('Location: admin/service.php');
                    break;
                case 'restaurant_admin':
                    header('Location: admin/dashboard.php');
                    break;
                case 'courier':
                    header('Location: courier/orders.php');
                    break;
                default:
                    header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Неверное имя пользователя или пароль';
        }
    }
}

// Подключаем шапку сайта
include '../includes/header2.php';
?>

<!-- HTML-форма для входа -->
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