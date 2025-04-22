-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Апр 21 2025 г., 14:14
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `food_delivery`
--

-- --------------------------------------------------------

--
-- Структура таблицы `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `menu_items`
--

INSERT INTO `menu_items` (`id`, `restaurant_id`, `name`, `description`, `price`) VALUES
(1, 1, 'Пицца Маргарита', 'Классическая пицца с томатным соусом, моцареллой и базиликом', 450.00),
(2, 1, 'Пицца Пепперони', 'Пицца с острой колбасой пепперони и сыром', 550.00),
(3, 1, 'Четыре сыра', 'Пицца с сырами моцарелла, горгонзола, пармезан и эмменталь', 600.00),
(4, 2, 'Воппер', 'Классический бургер с говяжьей котлетой, овощами и соусом', 250.00),
(5, 2, 'Чизбургер', 'Бургер с сыром, котлетой и специальным соусом', 180.00),
(6, 2, 'Картофель фри', 'Хрустящий картофель фри с солью', 120.00),
(7, 3, 'Ролл Филадельфия', 'Ролл с лососем, сливочным сыром и огурцом', 320.00),
(8, 3, 'Ролл Калифорния', 'Ролл с крабом, авокадо и икрой тобико', 280.00),
(9, 3, 'Суши с тунцом', 'Нигири с тунцом', 90.00),
(10, 4, 'Борщ', 'Классический украинский борщ со сметаной', 200.00),
(11, 4, 'Котлета по-киевски', 'Куриная котлета с масляно-зеленым соусом', 350.00),
(12, 4, 'Оливье', 'Салат оливье по классическому рецепту', 180.00);

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `restaurant_id` int(11) NOT NULL,
  `delivery_address` varchar(255) NOT NULL,
  `contact_phone` varchar(20) NOT NULL,
  `payment_method` enum('cash','card') NOT NULL,
  `status` enum('pending','accepted','cooking','on_way','delivered','canceled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `restaurant_id`, `delivery_address`, `contact_phone`, `payment_method`, `status`, `created_at`) VALUES
(2, 14, 1, 'Центральный район', '88005553535', 'cash', 'delivered', '2025-04-21 09:27:21'),
(3, 14, 4, 'Центральный район', '88005553535', 'card', 'pending', '2025-04-21 09:34:13');

-- --------------------------------------------------------

--
-- Структура таблицы `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price`) VALUES
(4, 2, 2, 1, 550.00),
(5, 3, 10, 1, 200.00),
(6, 3, 11, 1, 350.00),
(7, 3, 12, 3, 180.00);

-- --------------------------------------------------------

--
-- Структура таблицы `restaurants`
--

CREATE TABLE `restaurants` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `delivery_area` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `restaurants`
--

INSERT INTO `restaurants` (`id`, `name`, `address`, `delivery_area`, `is_active`) VALUES
(1, 'Папа Гриль', 'ул. Центральная, 30', 'Центральный район, Заречный район, Кэльвинкеляйново', 1),
(2, 'Бургер Кинг', 'ул. Ленина, 42', 'Центральный район, Северный район', 1),
(3, 'Суши Вок', 'ул. Восточная, 7', 'Восточный район, Центральный район', 1),
(4, 'Кафе Печка', 'ул. Парковая, 33', 'Южный район, Центральный район', 1),
(7, 'Cafe BalerinaCupputino', 'ул. Центральная, 36', 'Центральный район, Северный район, Западный район', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','restaurant_admin','courier','service_admin') NOT NULL,
  `restaurant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `restaurant_id`) VALUES
(13, 'pizza_admin', '$2y$10$eItZvEIEqoYq5ECOF7myCuyp2lxA.0kEfzHGBY5gDvkHRvQm6TPti', 'restaurant_admin', 1),
(14, 'MissMafin', '$2y$10$R7ZAmd0ypSrTdiCNxZInf.2uYuBTOCm.5TEUCabQOtvx4URDCqBfG', 'customer', NULL),
(16, 'cafe_admin', '$2y$10$iyK7FDpnDy5Y6Hn3KN3aGumFGEV2UREFvganvE5LHVKwycqW470h6', 'restaurant_admin', 4),
(17, 'sushi_admin', '$2y$10$sm76T6D/iRTpSkBmVVmMg.HRbnaKEvfenj/PsFGXF.TI12RyRJtli', 'restaurant_admin', 3),
(18, 'burger_admin', '$2y$10$FfM3leK4h7yLw43KUDmQXehbaVs2spdgeOWT8DX9gdicwtor281Ka', 'restaurant_admin', 2),
(19, 'courier1', '$2y$10$9/UTw4pZs1oFLYmYnPg39eLkDfgX/K9KWTBm4yBqfFhCiB7j.Cgii', 'courier', NULL),
(20, 'serviceadmin', '$2y$10$423XFRSOktoeqykpMxH3uOgSD8yVrB2dp7Ea8IWFSNspeNImQmMQ2', 'service_admin', NULL),
(22, 'admin_cafebalerinacupputino', '$2y$10$EYvgfOfLTLaa5Dwha6894.nK5lJ8n02OlUarROsNCN2Xkp6DjoVOG', 'restaurant_admin', 7),
(23, '111', '$2y$10$AZvUw3s8lvvXydsXPRw.lO2UEn9jx9o7y95Wz7c/wBXOnuOGkktIK', 'customer', NULL),
(24, 'aaa', '$2y$10$3.bQ2KmyoNNcwDZ9audu9OgsKknyCPkuKh4JfVTt/vXUQSoMsLWe6', 'customer', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `restaurant_id` (`restaurant_id`);

--
-- Индексы таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Индексы таблицы `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`);

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`id`);

--
-- Ограничения внешнего ключа таблицы `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
