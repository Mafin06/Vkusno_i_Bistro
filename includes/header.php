<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вкусно и быстро</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
<header class="site-header">
    <div class="header-container">
        <!-- Логотип сайта -->
        <div class="logo-container">
            <a href="index.php" class="logo-link">
                <img src="images/logo.png" alt="Вкусно и быстро" class="logo-img">
                <span class="logo-text">Вкусно и быстро</span>
            </a>
        </div>
        
        <!-- Адрес доставки -->
        <div class="delivery-address">
            <?php if(isset($_SESSION['delivery_address'])): ?>
                <i class="fas fa-map-marker-alt"></i>
                <span><?php echo htmlspecialchars($_SESSION['delivery_address']); ?></span>
            <?php endif; ?>
        </div>
        
        <!-- Навигация пользователя -->
        <div class="user-nav">
            <?php if(isLoggedIn()): ?>
                <a href="cart.php" class="cart-link">
                    <i class="fas fa-shopping-cart"> </i> 
                     Корзина
                    <?php if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                        <span class="cart-count"><?php echo array_sum($_SESSION['cart']); ?></span>
                    <?php endif; ?>
                </a>
                
                <div class="user-profile">
                    <i class="fas fa-user-circle"></i>
                    <span>
                        <?php 
                        // Проверяем активную сессию по нескольким параметрам
                        $isReallyLoggedIn = isset($_SESSION['loggedin']) && 
                                        $_SESSION['loggedin'] === true && 
                                        isset($_SESSION['user_id']);

                        if ($isReallyLoggedIn && isset($_SESSION['username'])) {
                            echo htmlspecialchars($_SESSION['username']);
                        } else {
                            // Принудительная очистка если что-то пошло не так
                            unset($_SESSION['username']);
                            echo 'Гость';
                        }
                        ?>
                    </span>
                    <div class="dropdown-content">
                        <a href="logout.php" class="logout-link">
                            <i class="fas fa-sign-out-alt"></i> Выйти
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-link">Войти</a>
                <a href="register.php" class="register-link">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="main-content">