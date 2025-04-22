<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Проверка прав доступа
if (!isLoggedIn() || $_SESSION['user_role'] !== 'service_admin') {
    header('Location: ../login.php');
    exit;
}

// Получаем ID ресторана из GET-параметра
$restaurant_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Получаем данные ресторана
$restaurant = [];
if ($restaurant_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE id = ?");
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant = $result->fetch_assoc();
    
    if (!$restaurant) {
        $_SESSION['error'] = "Ресторан не найден";
        header('Location: service.php');
        exit;
    }
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
        if ($restaurant_id > 0) {
            // Обновление существующего ресторана
            $stmt = $conn->prepare("UPDATE restaurants SET name = ?, address = ?, delivery_area = ?, is_active = ? WHERE id = ?");
            $stmt->bind_param("sssii", $name, $address, $delivery_area, $is_active, $restaurant_id);
        } else {
            // Создание нового ресторана
            $stmt = $conn->prepare("INSERT INTO restaurants (name, address, delivery_area, is_active) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $name, $address, $delivery_area, $is_active);
        }

        if ($stmt->execute()) {
            $_SESSION['success'] = $restaurant_id > 0 ? "Ресторан успешно обновлен" : "Ресторан успешно добавлен";
            header('Location: service.php');
            exit;
        } else {
            $_SESSION['error'] = "Ошибка при сохранении ресторана";
        }
    }
}

include '../includes/header2.php';
?>

<h1><?php echo $restaurant_id > 0 ? 'Редактирование ресторана' : 'Добавление ресторана'; ?></h1>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="name">Название ресторана *</label>
        <input type="text" class="form-control" id="name" name="name" 
               value="<?php echo isset($restaurant['name']) ? htmlspecialchars($restaurant['name']) : ''; ?>" required>
    </div>
    
    <div class="form-group">
        <label for="address">Адрес ресторана *</label>
        <input type="text" class="form-control" id="address" name="address" 
               value="<?php echo isset($restaurant['address']) ? htmlspecialchars($restaurant['address']) : ''; ?>" required>
    </div>
    
    <div class="form-group">
        <label for="delivery_area">Зона доставки *</label>
        <textarea class="form-control" id="delivery_area" name="delivery_area" required><?php 
            echo isset($restaurant['delivery_area']) ? htmlspecialchars($restaurant['delivery_area']) : ''; 
        ?></textarea>
        <small class="form-text text-muted">Укажите районы доставки через запятую</small>
    </div>
    
    <div class="form-group form-check">
        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
               <?php echo (isset($restaurant['is_active']) && $restaurant['is_active']) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="is_active">Активен</label>
    </div>
    
    <button type="submit" class="btn btn-primary">Сохранить</button>
    <a href="service.php" class="btn btn-secondary">Отмена</a>
</form>

<?php include '../includes/footer.php'; ?>