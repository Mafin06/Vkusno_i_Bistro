<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Проверка прав доступа
if (!isLoggedIn() || $_SESSION['user_role'] !== 'service_admin') {
    header('Location: ../login.php');
    exit;
}

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $delivery_area = trim($_POST['delivery_area']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Валидация
    if (empty($name) || empty($address) || empty($delivery_area)) {
        $_SESSION['error'] = "Все обязательные поля должны быть заполнены";
    } else {
        // Создание нового ресторана
        $stmt = $conn->prepare("INSERT INTO restaurants (name, address, delivery_area, is_active) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssi", $name, $address, $delivery_area, $is_active);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Ресторан успешно добавлен";
            
            // Получаем ID нового ресторана
            $new_restaurant_id = $conn->insert_id;
            
            // Создаем администратора для ресторана
            $admin_username = 'admin_' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
            $admin_password = password_hash('admin123', PASSWORD_DEFAULT); // Генерируем временный пароль
            
            $stmt = $conn->prepare("INSERT INTO users (username, password, role, restaurant_id) VALUES (?, ?, 'restaurant_admin', ?)");
            $stmt->bind_param("ssi", $admin_username, $admin_password, $new_restaurant_id);
            $stmt->execute();
            
            header('Location: service.php');
            exit;
        } else {
            $_SESSION['error'] = "Ошибка при добавлении ресторана: " . $conn->error;
        }
    }
}

include '../includes/header2.php';
?>

<h1>Добавление нового ресторана</h1>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="name">Название ресторана *</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>
    
    <div class="form-group">
        <label for="address">Адрес ресторана *</label>
        <input type="text" class="form-control" id="address" name="address" required>
    </div>
    
    <div class="form-group">
        <label for="delivery_area">Зона доставки *</label>
        <textarea class="form-control" id="delivery_area" name="delivery_area" required></textarea>
        <small class="form-text text-muted">Укажите районы доставки через запятую (например: Центральный район, Северный район, Западный район)</small>
    </div>
    
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
        <label class="form-check-label" for="is_active">Активен</label>
    </div>
    
    <div class="form-group">
        <h4>Создать администратора ресторана</h4>
        <p>Будет автоматически создан администратор с логином вида "admin_название_ресторана" и временным паролем "admin123"</p>
    </div>
    
    <button type="submit" class="btn btn-primary">Добавить ресторан</button>
    <a href="service.php" class="btn btn-secondary">Отмена</a>
</form>

<?php include '../includes/footer.php'; ?>