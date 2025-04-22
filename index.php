<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address'])) {
    $_SESSION['delivery_address'] = $_POST['address'];
    header('Location: restaurants.php');
    exit;
}

include 'includes/header.php';
?>

<h1>Укажите адрес доставки</h1>
<form method="POST">
    <input type="text" name="address" placeholder="Введите ваш адрес" required>
    <button type="submit">Продолжить</button>
</form>

<?php include 'includes/footer.php'; ?>