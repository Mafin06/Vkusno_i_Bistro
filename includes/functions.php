<?php
// Проверка авторизации
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Получение текущего пользователя
function getCurrentUser() {
    global $conn;
    if (isLoggedIn()) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    return null;
}

// Редирект для неавторизованных
function requireAuth() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Редирект для определенных ролей
function requireRole($role) {
    requireAuth();
    if ($_SESSION['user_role'] !== $role) {
        header('Location: index.php');
        exit;
    }
}

// Получить информацию о текущем пользователе
// function getCurrentUser() {
//     global $conn;
//     if (isLoggedIn()) {
//         $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
//         $stmt->bind_param("i", $_SESSION['user_id']);
//         $stmt->execute();
//         return $stmt->get_result()->fetch_assoc();
//     }
//     return null;
// }

// Получить рестораны по зоне доставки
function getRestaurantsByArea($area) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM restaurants WHERE delivery_area LIKE ? AND is_active = TRUE");
    $searchArea = "%$area%";
    $stmt->bind_param("s", $searchArea);
    $stmt->execute();
    return $stmt->get_result();
}

// Получить меню ресторана
function getMenu($restaurant_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM menu_items WHERE restaurant_id = ?");
    $stmt->bind_param("i", $restaurant_id);
    $stmt->execute();
    return $stmt->get_result();
}

// Добавить товар в корзину
function addToCart($item_id, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id] += $quantity;
    } else {
        $_SESSION['cart'][$item_id] = $quantity;
    }
}

// Получить содержимое корзины
function getCartItems() {
    global $conn;
    $cartItems = [];
    
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $item_ids = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($item_ids), '?'));
        
        $stmt = $conn->prepare("SELECT * FROM menu_items WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($item_ids)), ...$item_ids);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $cartItems[] = [
                'item' => $row,
                'quantity' => $_SESSION['cart'][$row['id']]
            ];
        }
    }
    
    return $cartItems;
}
?>