<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_GET['restaurant_id'])) {
    header('Location: restaurants.php');
    exit;
}

$restaurant_id = intval($_GET['restaurant_id']);
$menu = getMenu($restaurant_id);

// Добавление в корзину
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['quantity'])) {
    addToCart($_POST['item_id'], $_POST['quantity']);
    header("Location: menu.php?restaurant_id=$restaurant_id");
    exit;
}

include 'includes/header.php';
?>

<h1>Меню ресторана</h1>

<div class="menu-items">
    <?php while ($item = $menu->fetch_assoc()): ?>
    <div class="menu-item">
        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
        <p><?php echo htmlspecialchars($item['description']); ?></p>
        <p>Цена: <?php echo $item['price']; ?> руб.</p>
        
        <form method="POST">
            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
            <input type="number" name="quantity" value="1" min="1">
            <button type="submit">В корзину</button>
        </form>
    </div>
    <?php endwhile; ?>
</div>

<a href="cart.php">Перейти в корзину</a>

<?php include 'includes/footer.php'; ?>