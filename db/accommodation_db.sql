-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-01-2025 a las 21:03:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `accommodation_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accommodations`
--

CREATE TABLE `accommodations` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('available','occupied','maintenance') DEFAULT 'available',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `accommodations`
--

INSERT INTO `accommodations` (`id`, `name`, `description`, `price`, `image_url`, `status`, `created_at`) VALUES
(3, 'Villa Mediterránea', 'Descripción: Hermosa villa con vista al mar, 3 habitaciones, piscina privada y terraza panorámica. Diseño mediterráneo con espacios abiertos y luminosos. Perfecta para familias o grupos.', 250.00, 'assets/uploads/1738178880_Villa Mediterránea.jpg', 'available', '2025-01-29 19:28:00'),
(4, 'Cabaña Montana Lodge', 'Descripción: Acogedora cabaña de madera en el bosque, 2 habitaciones, chimenea y terraza exterior. Interior rústico con todas las comodidades modernas. Ideal para escapadas románticas.', 180.00, 'assets/uploads/1738178902_Cabaña Montana Lodge.jpg', 'available', '2025-01-29 19:28:22'),
(5, 'Apartamento Urban Loft', 'Descripción: Moderno loft en el centro de la ciudad, estilo industrial, cocina equipada y balcón. Espacio abierto con dormitorio en altillo. Perfecto para viajeros de negocios o parejas.', 150.00, 'assets/uploads/1738178923_Apartamento.jpg', 'available', '2025-01-29 19:28:43'),
(6, 'Casa de Playa \"Blue Paradise\"', 'Descripción: Espectacular casa frente al mar, 4 habitaciones, amplias áreas sociales y acceso directo a la playa. Decoración costera con tonos azules y blancos. Excelente para vacaciones familiares.', 320.00, 'assets/uploads/1738178963_Casa de Play.jpg', 'available', '2025-01-29 19:29:23'),
(7, 'Suite Ejecutiva Sky View', 'Descripción: Elegante suite en piso alto con vistas panorámicas de la ciudad, 1 habitación, sala de estar y cocina de concepto abierto. Decoración contemporánea y acabados de lujo. Ideal para ejecutivos.', 180.00, 'assets/uploads/1738178987_Suite Ejecutiva Sky View.jpeg', 'available', '2025-01-29 19:29:47');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `accommodation_id` int(11) DEFAULT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `accommodation_id`, `check_in`, `check_out`, `total_price`, `status`, `created_at`) VALUES
(6, 2, 5, '2025-01-29', '2025-01-30', 150.00, 'pending', '2025-01-29 19:53:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `is_admin`, `created_at`) VALUES
(1, 'admin', '$2y$10$JO17Gy/oDkUQGgdTChQXjewg1a5Z6tI7AENfqFdK0EleGhU1s3GRy', 'kevincha@gmail.com', 1, '2025-01-29 18:17:52'),
(2, 'Kevin', '$2y$10$EYFzFgoHs/UjYbmWFkPYBu/lRulHCh2Yg7qcWWom2ejD4ZhRwG/GW', 'evpal@gmail.com', 0, '2025-01-29 19:02:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `user_selections`
--

CREATE TABLE `user_selections` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `accommodation_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accommodations`
--
ALTER TABLE `accommodations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `accommodation_id` (`accommodation_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `user_selections`
--
ALTER TABLE `user_selections`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_selection` (`user_id`,`accommodation_id`),
  ADD KEY `accommodation_id` (`accommodation_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accommodations`
--
ALTER TABLE `accommodations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `user_selections`
--
ALTER TABLE `user_selections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`id`);

--
-- Filtros para la tabla `user_selections`
--
ALTER TABLE `user_selections`
  ADD CONSTRAINT `user_selections_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_selections_ibfk_2` FOREIGN KEY (`accommodation_id`) REFERENCES `accommodations` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
