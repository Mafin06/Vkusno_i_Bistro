<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = 'customer';
     // По умолчанию регистрируем как клиента

    // Валидация
    if (empty($username) || empty($password)) {
        $error = 'Все поля обязательны для заполнения';
    } else {
        // Проверяем, существует ли пользователь
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = 'Пользователь с таким именем уже существует';
        } else {
            // Хешируем пароль
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Сохраняем пользователя
            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['user_role'] = $role;
                header('Location: login.php');
                exit;
            } else {
                $error = 'Ошибка при регистрации';
            }
        }
    }
}

include 'includes/header.php';
?>

<h1>Регистрация</h1>

<?php if ($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
    <div>
        <label>Имя пользователя:</label>
        <input type="text" name="username" required>
    </div>
    
    <div>
        <label>Пароль:</label>
        <input type="password" name="password" required>
    </div>
    
    <button type="submit">Зарегистрироваться</button>
</form>

<p>Уже есть аккаунт? <a href="login.php">Войти</a></p>

<?php include 'includes/footer.php'; ?>