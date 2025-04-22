<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$cartItems = getCartItems();

if (empty($cartItems)) {
    header('Location: cart.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем первый ресторан из корзины (для простоты предполагаем, что все из одного ресторана)
    $firstItemId = array_keys($_SESSION['cart'])[0];
    $stmt = $conn->prepare("SELECT restaurant_id FROM menu_items WHERE id = ?");
    $stmt->bind_param("i", $firstItemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $restaurant_id = $result->fetch_assoc()['restaurant_id'];
    
    // Создаем заказ
    $user_id = isLoggedIn() ? $_SESSION['user_id'] : NULL;
    $delivery_address = $_SESSION['delivery_address'];
    $contact_phone = $_POST['phone'];
    $payment_method = $_POST['payment_method'];
    
    $stmt = $conn->prepare("INSERT INTO orders (user_id, restaurant_id, delivery_address, contact_phone, payment_method) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iisss", $user_id, $restaurant_id, $delivery_address, $contact_phone, $payment_method);
    $stmt->execute();
    $order_id = $conn->insert_id;
    
    // Добавляем элементы заказа
    foreach ($cartItems as $cartItem) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $cartItem['item']['id'], $cartItem['quantity'], $cartItem['item']['price']);
        $stmt->execute();
    }
    
    // Очищаем корзину
    unset($_SESSION['cart']);
    
    header("Location: order_success.php?order_id=$order_id");
    exit;
}

include 'includes/header.php';
?>

<h1>Оформление заказа</h1>

<form method="POST">
    <div>
        <label>Адрес доставки:</label>
        <input type="text" value="<?php echo htmlspecialchars($_SESSION['delivery_address']); ?>" readonly>
    </div>
    
    <div>
        <label>Контактный телефон:</label>
        <input type="tel" name="phone" required>
    </div>
    
    <div>
        <label>Способ оплаты:</label>
        <select name="payment_method" required>
            <option value="cash">Наличными при получении</option>
            <option value="card">Картой онлайн</option>
        </select>
    </div>
    
    <button type="submit">Подтвердить заказ</button>
</form>

<?php include 'includes/footer.php'; ?>