<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['delivery_address'])) {
    header('Location: index.php');
    exit;
}

$restaurants = getRestaurantsByArea($_SESSION['delivery_address']);

include 'includes/header.php';
?>

<h1>Выберите ресторан</h1>
<p>Доставка по адресу: <?php echo htmlspecialchars($_SESSION['delivery_address']); ?></p>

<div class="restaurants">
    <?php while ($restaurant = $restaurants->fetch_assoc()): ?>
    <div class="restaurant">
        <h2><?php echo htmlspecialchars($restaurant['name']); ?></h2>
        <p><?php echo htmlspecialchars($restaurant['address']); ?></p>
        <a href="menu.php?restaurant_id=<?php echo $restaurant['id']; ?>">Посмотреть меню</a>
    </div>
    <?php endwhile; ?>
</div>

<?php include 'includes/footer.php'; ?>