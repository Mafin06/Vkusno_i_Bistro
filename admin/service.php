<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Проверка прав доступа
if (!isLoggedIn() || $_SESSION['user_role'] !== 'service_admin') {
    header('Location: ../login.php');
    exit;
}

// Получение списка ресторанов
$restaurants = $conn->query("SELECT * FROM restaurants");

// Получение статистики заказов
$stats = $conn->query("
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
        SUM(CASE WHEN status = 'canceled' THEN 1 ELSE 0 END) as canceled_orders
    FROM orders
")->fetch_assoc();

include '../includes/header2.php';
?>

<h1>Панель администратора сервиса</h1>

<h2>Статистика</h2>
<p>Всего заказов: <?php echo $stats['total_orders']; ?></p>
<p>Доставлено: <?php echo $stats['delivered_orders']; ?></p>
<p>Отменено: <?php echo $stats['canceled_orders']; ?></p>

<h2>Рестораны</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Адрес</th>
            <th>Зона доставки</th>
            <th>Статус</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($restaurant = $restaurants->fetch_assoc()): ?>
        <tr>
            <td><?php echo $restaurant['id']; ?></td>
            <td><?php echo htmlspecialchars($restaurant['name']); ?></td>
            <td><?php echo htmlspecialchars($restaurant['address']); ?></td>
            <td><?php echo htmlspecialchars($restaurant['delivery_area']); ?></td>
            <td><?php echo $restaurant['is_active'] ? 'Активен' : 'Неактивен'; ?></td>
            <td>
                <a href="edit_restaurant.php?id=<?php echo $restaurant['id']; ?>">Редактировать</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<div>
    <a href="add_restaurant.php">Добавить ресторан</a>
</div>

<?php include '../includes/footer.php'; ?>