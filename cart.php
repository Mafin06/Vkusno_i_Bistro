<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$cartItems = getCartItems();
$total = 0;

// Удаление из корзины
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    unset($_SESSION['cart'][$_POST['remove_item']]);
    header('Location: cart.php');
    exit;
}

include 'includes/header.php';
?>

<h1>Ваша корзина</h1>

<?php if (empty($cartItems)): ?>
    <p>Ваша корзина пуста</p>
<?php else: ?>
    <div class="cart-items">
        <?php foreach ($cartItems as $cartItem): 
            $itemTotal = $cartItem['item']['price'] * $cartItem['quantity'];
            $total += $itemTotal;
        ?>
        <div class="cart-item">
            <h3><?php echo htmlspecialchars($cartItem['item']['name']); ?></h3>
            <p>Цена: <?php echo $cartItem['item']['price']; ?> руб.</p>
            <p>Количество: <?php echo $cartItem['quantity']; ?></p>
            <p>Итого: <?php echo $itemTotal; ?> руб.</p>
            
            <form method="POST">
                <input type="hidden" name="remove_item" value="<?php echo $cartItem['item']['id']; ?>">
                <button type="submit">Удалить</button>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="cart-total">
        <h3>Общая сумма: <?php echo $total; ?> руб.</h3>
    </div>
    
    <a href="order.php">Оформить заказ</a>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>