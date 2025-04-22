<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Проверка прав доступа
if (!isLoggedIn() || $_SESSION['user_role'] !== 'restaurant_admin') {
    header('Location: ../login.php');
    exit;
}

$user = getCurrentUser();
$restaurant_id = $user['restaurant_id'];

// Получение заказов для ресторана (ИСПРАВЛЕННЫЙ ЗАПРОС)
$orders = $conn->query("
    SELECT o.*, u.username 
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    WHERE o.restaurant_id = $restaurant_id
    ORDER BY o.created_at DESC
");

// Изменение статуса заказа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ? AND restaurant_id = ?");
    $stmt->bind_param("sii", $status, $order_id, $restaurant_id);
    $stmt->execute();
    
    header("Location: dashboard.php");
    exit;
}

include '../includes/header2.php';
?>

<!-- Остальная часть кода остается без изменений -->

<h1>Панель администратора ресторана</h1>

<h2>Текущие заказы</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Клиент</th>
            <th>Телефон</th>
            <th>Адрес</th>
            <th>Список блюд</th>
            <th>Сумма</th>
            <th>Статус</th>
            <th>Дата</th>
            <th>Действия</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = $orders->fetch_assoc()): 
            // Получаем блюда для заказа
            $items = $conn->query("
                SELECT mi.name, oi.quantity, oi.price 
                FROM order_items oi
                JOIN menu_items mi ON oi.menu_item_id = mi.id
                WHERE oi.order_id = {$order['id']}
            ");
            
            $total = 0;
            $items_list = [];
            while ($item = $items->fetch_assoc()) {
                $items_list[] = "{$item['name']} x{$item['quantity']}";
                $total += $item['price'] * $item['quantity'];
            }
        ?>
        <tr>
            <td><?php echo $order['id']; ?></td>
            <td><?php echo htmlspecialchars($order['username']); ?></td>
            <td><?php echo htmlspecialchars($order['contact_phone']); ?></td>
            <td><?php echo htmlspecialchars($order['delivery_address']); ?></td>
            <td><?php echo implode(', ', $items_list); ?></td>
            <td><?php echo $total; ?> руб.</td>
            <td>
                <?php 
                $statuses = [
                    'pending' => 'Ожидает',
                    'accepted' => 'Принят',
                    'cooking' => 'Готовится',
                    'on_way' => 'В пути',
                    'delivered' => 'Доставлен',
                    'canceled' => 'Отменен'
                ];
                echo $statuses[$order['status']]; 
                ?>
            </td>
            <td><?php echo $order['created_at']; ?></td>
            <td>
                <form method="POST">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <select name="status">
                        <?php foreach ($statuses as $value => $label): ?>
                        <option value="<?php echo $value; ?>" <?php echo $value === $order['status'] ? 'selected' : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">Обновить</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>