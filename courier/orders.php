<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Проверка прав доступа
if (!isLoggedIn() || $_SESSION['user_role'] !== 'courier') {
    header('Location: ../login.php');
    exit;
}

// Получение заказов для доставки
$orders = $conn->query("
    SELECT o.*, r.name as restaurant_name 
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.status IN ('cooking', 'on_way')
    ORDER BY o.created_at
");

// Изменение статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    
    header("Location: orders.php");
    exit;
}

include '../includes/header2.php';
?>

<h1>Панель курьера</h1>

<h2>Текущие заказы</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Ресторан</th>
            <th>Адрес доставки</th>
            <th>Телефон</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = $orders->fetch_assoc()): ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['restaurant_name']); ?></td>
            <td><?php echo htmlspecialchars($order['delivery_address']); ?></td>
            <td><?php echo htmlspecialchars($order['contact_phone']); ?></td>
            <td><?php echo $order['status'] === 'cooking' ? 'Готовится' : 'В пути'; ?></td>
            <td><?php echo $order['created_at']; ?></td>
            <td>
                <?php if ($order['status'] === 'cooking'): ?>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="hidden" name="status" value="on_way">
                        <button type="submit">Взять в доставку</button>
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <input type="hidden" name="status" value="delivered">
                        <button type="submit">Доставлен</button>
                    </form>
                <?php endif; ?>
                <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo urlencode($order['delivery_address']); ?>" target="_blank">
                    Маршрут
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>