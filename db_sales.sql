-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2025 a las 03:10:13
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
-- Base de datos: `db_sales`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `branches`
--

CREATE TABLE `branches` (
  `id` int(11) UNSIGNED NOT NULL,
  `num_establecimiento` varchar(100) NOT NULL,
  `name` varchar(250) NOT NULL,
  `address` varchar(250) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `state` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `branches`
--

INSERT INTO `branches` (`id`, `num_establecimiento`, `name`, `address`, `phone`, `created_at`, `updated_at`, `deleted_at`, `state`) VALUES
(1, '001', 'Los Libertadores', 'Av los Libertadores y psj Viracocha', '2665876', '2025-09-16 21:49:23', '2025-11-08 02:03:18', NULL, 1),
(2, '002', 'La Gasca', 'La Gasca y Amarica', '3123456', '2025-09-16 21:52:21', '2025-09-17 17:16:08', NULL, 1),
(3, '003', 'Chillogallo', 'Av mariscal suche y ampetra', '1234567', '2025-09-17 17:13:09', '2025-09-17 17:13:09', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buys`
--

CREATE TABLE `buys` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_supplier` int(11) UNSIGNED NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `fecha_ingreso` datetime NOT NULL,
  `type_pay` varchar(1) NOT NULL DEFAULT '1',
  `type_doc` varchar(1) NOT NULL DEFAULT '1',
  `id_point_of_sale` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `buys`
--

INSERT INTO `buys` (`id`, `id_supplier`, `total`, `state`, `created_at`, `updated_at`, `deleted_at`, `subtotal`, `numero_factura`, `iva`, `fecha_ingreso`, `type_pay`, `type_doc`, `id_point_of_sale`) VALUES
(990, 2, 30.71, 1, '2025-11-08 03:47:38', '2025-11-08 03:47:38', NULL, 26.70, '577-657-657657657', 4.01, '2025-11-07 00:00:00', '2', '1', 1),
(991, 1, 38.92, 1, '2025-11-10 02:38:39', '2025-11-10 02:38:39', NULL, 33.84, '001-002-003455554', 5.08, '2025-11-09 00:00:00', '2', '1', 3),
(992, 1, 29.88, 1, '2025-11-10 02:48:59', '2025-11-10 02:48:59', NULL, 25.98, '657-657-657657657', 3.90, '2025-11-08 00:00:00', '2', '1', 1),
(993, 2, 67.62, 1, '2025-11-10 02:50:36', '2025-11-10 02:50:36', NULL, 58.80, '456-656-565464655', 8.82, '2025-11-07 00:00:00', '2', '1', 3),
(994, 1, 53.59, 1, '2025-11-11 21:05:35', '2025-11-11 21:05:35', NULL, 46.60, '001-002-000000098', 6.99, '2025-11-11 00:00:00', '2', '1', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `buy_details`
--

CREATE TABLE `buy_details` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_buy` int(11) UNSIGNED NOT NULL,
  `id_product` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `buy_details`
--

INSERT INTO `buy_details` (`id`, `id_buy`, `id_product`, `quantity`, `price`, `subtotal`, `created_at`, `updated_at`) VALUES
(1354, 990, 41, 10, 2.67, 26.70, '2025-11-08 03:47:38', '2025-11-08 03:47:38'),
(1355, 991, 42, 8, 4.23, 33.84, '2025-11-10 02:38:39', '2025-11-10 02:38:39'),
(1356, 992, 44, 6, 4.33, 25.98, '2025-11-10 02:48:59', '2025-11-10 02:48:59'),
(1357, 993, 44, 10, 5.88, 58.80, '2025-11-10 02:50:36', '2025-11-10 02:50:36'),
(1358, 994, 42, 10, 4.66, 46.60, '2025-11-11 21:05:35', '2025-11-11 21:05:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `imagen`, `state`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, 'categorie 1.', 'description5', 'categories/Pv0NjfYhuCKAJJMC0OE3nxXNu9UFggercjjohKZH.png', 1, '2022-01-24 20:16:37', '2025-11-21 20:07:22', NULL),
(9, 'categorie 2', 'la categoria 99 es la primera', 'categories/Hbarork1fydPqzCRZKDc2ds7ScWwn8XaMAu9IOg0.png', 1, '2025-07-11 21:49:16', '2025-11-21 20:24:12', NULL),
(10, 'categorie 3', 'description5 de categoria 33333', 'categories/WGRVbq5MiUxTt9RyyluK3TAMCQfXQ2LXtZws8v1X.png', 1, '2025-06-24 20:16:37', '2025-11-21 20:25:38', NULL),
(11, 'categorie 4', 'description5', 'categories/e1QQnx2POK9FJUH8NqvOPc5Ur44BMz6tegSbIgzO.png', 0, '2025-06-24 20:16:37', '2025-11-21 20:49:00', NULL),
(21, 'categorie 5', 'description5', 'categories/ZIpepgwnDZkCyMCPRBbqtGIyRrpCeSoOUsQu5Y0o.png', 1, '2025-06-24 20:16:37', '2025-11-21 16:23:46', NULL),
(32, 'jijijijijij', 'jijijijijijj', 'categories/L0YgQJgWnHmxSBeyfZ6MsoDFyTrZ25gfwSK3a2E0.png', 1, '2025-11-21 19:55:31', '2025-11-21 19:55:31', NULL),
(33, 'ihhihiuu', 'hiuhiuhihiu', 'categories/5X30WEkf16TAGYwvGPQW8JhM83BUVfSdnFYfYc3I.png', 1, '2025-11-21 19:56:17', '2025-11-21 19:56:17', NULL),
(34, 'kokokokok', 'kokokookokok', 'categories/G7oTJ8q3caEMvHw4c15xtwUnULScRKaPFe0LnpHj.png', 1, '2025-11-21 19:57:08', '2025-11-21 19:57:08', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configurations`
--

CREATE TABLE `configurations` (
  `id` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(150) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configurations`
--

INSERT INTO `configurations` (`id`, `created_at`, `updated_at`, `deleted_at`, `name`, `value`, `state`) VALUES
(1, '2025-08-28 15:05:13', '2025-08-29 02:36:30', NULL, 'version', '2.1.0', 1),
(2, '2025-08-28 15:13:54', '2025-08-28 16:12:07', NULL, 'ambiente', '1', 1),
(3, '2025-08-28 15:54:48', '2025-08-28 15:54:48', NULL, 'tipoEmision', '1', 1),
(4, '2025-08-28 15:55:38', '2025-08-28 21:43:37', NULL, 'razonSocial', 'Joel Eduardo Luna Moya', 1),
(5, '2025-08-28 15:56:06', '2025-08-28 15:56:06', NULL, 'nombreComercial', 'Parabrisas Libertadores', 1),
(6, '2025-08-28 15:56:24', '2025-08-28 15:56:24', NULL, 'ruc', '1718251638001', 1),
(7, '2025-08-28 15:57:16', '2025-08-28 15:57:16', NULL, 'dirMatriz', 'Av. Los Libertadores Oe4-131 y pasaje Viracocha', 1),
(8, '2025-08-28 15:57:50', '2025-08-28 15:57:50', NULL, 'dirEstablecimiento', 'Av. Los Libertadores Oe4-131 y pasaje Viracocha', 1),
(9, '2025-08-28 15:58:23', '2025-08-28 15:58:23', NULL, 'obligadoContabilidad', 'NO', 1),
(14, '2025-08-29 15:57:59', '2025-08-29 15:57:59', NULL, 'iva', '15', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `customers`
--

CREATE TABLE `customers` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `num_identificador` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `customers`
--

INSERT INTO `customers` (`id`, `name`, `num_identificador`, `email`, `phone`, `address`, `state`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'customer12', '1734652322', 'customer2@gmail.com', '0922234722', 'avenida addrress22', 1, '2025-06-24 21:24:03', '2025-07-30 14:47:03', NULL),
(2, 'customer2333 44455', '1734652444', 'customer444@gmail.com', '0922234444', 'avenida addrress444', 1, '2025-06-24 21:24:30', '2025-07-30 15:32:10', NULL),
(3, 'customer3', '1734653333', 'customer3@gmail.com', '0922234765', 'avenida addrress', 1, '2025-06-24 21:24:56', '2025-07-30 15:32:13', NULL),
(4, 'customer4 123', '1734653344', 'customer4@gmail.com', '0922234765', 'avenida addrress', 1, '2025-06-24 21:26:38', '2025-08-01 21:30:36', NULL),
(5, 'customer5', '1735533344', 'customer5@gmail.com', '0922234765', 'avenida addrress', 1, '2025-06-24 21:26:57', '2025-07-30 15:32:16', NULL),
(19, 'juan piguabe', '1718276543', 'asd@gmail.com', '57657657', 'gjhjhghjgjhjhgjhgjhgjhgjhh', 1, '2025-08-01 19:33:39', '2025-08-01 19:33:39', NULL),
(25, 'mr popo', '1231231234', 'popo@gmail.com', '0909090987', '170101', 1, '2025-08-07 20:26:06', '2025-08-07 20:26:06', NULL),
(26, 'jijijijiijiji', '76867868786', 'jiji@gmail.com', '6876877', 'hkjhkjhkjhkjhkj', 1, '2025-08-07 21:06:20', '2025-08-07 21:06:20', NULL),
(27, 'andrea gutierrez', '1718348053', 'andre@gmail.com', '3332349823', 'la magdalena calle 1 y la que cruza mas por halla', 1, '2025-08-07 21:36:16', '2025-08-07 21:36:16', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventories`
--

CREATE TABLE `inventories` (
  `id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `stock_min` int(11) NOT NULL,
  `id_product` int(11) UNSIGNED NOT NULL,
  `id_branch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventories`
--

INSERT INTO `inventories` (`id`, `created_at`, `updated_at`, `deleted_at`, `stock`, `stock_min`, `id_product`, `id_branch`) VALUES
(26, '2025-11-06 01:37:42', '2025-11-06 01:37:42', NULL, 12, 5, 41, 3),
(27, '2025-11-06 01:37:42', '2025-11-17 21:40:00', NULL, -11, 0, 41, 1),
(28, '2025-11-06 01:37:42', '2025-11-06 01:37:42', NULL, 0, 0, 41, 2),
(29, '2025-11-07 19:13:43', '2025-11-11 21:05:35', NULL, 20, 3, 42, 3),
(30, '2025-11-07 19:13:43', '2025-11-07 19:13:43', NULL, 0, 0, 42, 1),
(31, '2025-11-07 19:13:43', '2025-11-11 20:51:44', NULL, 6, 0, 42, 2),
(32, '2025-11-07 19:20:02', '2025-11-07 19:20:37', NULL, 4, 2, 43, 3),
(33, '2025-11-07 19:20:02', '2025-11-07 19:20:02', NULL, 0, 0, 43, 1),
(34, '2025-11-07 19:20:02', '2025-11-07 19:20:02', NULL, 0, 0, 43, 2),
(35, '2025-11-07 22:00:55', '2025-11-11 20:53:44', NULL, 4, 5, 44, 3),
(36, '2025-11-07 22:00:55', '2025-11-11 20:33:56', NULL, -2, 0, 44, 1),
(37, '2025-11-07 22:00:55', '2025-11-10 02:50:36', NULL, 10, 0, 44, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2025_05_25_001200_create_categories_table', 1),
(6, '2025_05_25_00129900_create_products_table', 1),
(7, '2025_05_25_001300_create_customers_table', 1),
(8, '2025_05_25_001300_create_orders_table', 1),
(9, '2025_05_25_001355_create_order_details_table', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pays`
--

CREATE TABLE `pays` (
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id` int(11) NOT NULL,
  `num_comprobante_abono` varchar(20) NOT NULL,
  `valor_abono` decimal(10,2) NOT NULL,
  `imagen` varchar(250) DEFAULT NULL,
  `id_buy` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pays`
--

INSERT INTO `pays` (`created_at`, `updated_at`, `deleted_at`, `id`, `num_comprobante_abono`, `valor_abono`, `imagen`, `id_buy`) VALUES
('2025-11-10 02:43:04', '2025-11-10 02:43:04', NULL, 138, '687686868767', 38.00, 'pays/526T0JBGrtc4IkmliClq8dCXjmlWpFZZVgYlXV29.png', 991);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `points_of_sales`
--

CREATE TABLE `points_of_sales` (
  `id` int(11) NOT NULL,
  `id_branch` int(11) UNSIGNED NOT NULL,
  `codigo_punto_emision` varchar(10) NOT NULL,
  `secuencial_actual` bigint(20) UNSIGNED NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `points_of_sales`
--

INSERT INTO `points_of_sales` (`id`, `id_branch`, `codigo_punto_emision`, `secuencial_actual`, `descripcion`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '001', 19, 'maquina principal', NULL, '2025-11-17 21:40:00', NULL),
(2, 1, '002', 23, 'maquina secundaria', '2025-11-06 21:46:49', '2025-11-10 16:36:12', NULL),
(3, 2, '001', 29, 'maquina principal', '2025-11-07 17:31:56', '2025-11-11 20:51:44', NULL),
(4, 2, '002', 32, 'maquina secundaria', '2025-11-10 16:36:53', '2025-11-10 16:37:04', NULL),
(5, 3, '001', 39, 'maquina principal', '2025-11-10 16:37:32', '2025-11-11 20:53:44', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `products`
--

CREATE TABLE `products` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `id_categorie` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `cod_pro` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `imagen`, `state`, `id_categorie`, `created_at`, `updated_at`, `deleted_at`, `cod_pro`) VALUES
(41, 'parabrisas delantero luv 2300', 'parabrisas delantero luv 2300', 45.00, 'products/o5sO2hreD33I1dLtzHuisvqu5cy7GmNIbWi1y4qo.png', 1, 5, '2025-11-06 01:37:42', '2025-11-06 01:37:42', NULL, 'pdluv2300'),
(42, 'PD Chevrolet Aveo activo 2010', 'parabrisas delantero aveo activo, family', 66.00, 'products/S2DX0Q2GdTlsIYfzLgre4K9XVWak19RURAqMD4BO.png', 1, 10, '2025-11-07 19:13:43', '2025-11-07 19:13:43', NULL, 'pdaveoact'),
(43, 'ventana delantera derecha kia picanto 2019', 'ventana delantera derecha kia picanto 2019', 66.00, 'products/W4OE0NTWqBiHQCX1r67Ix3hemdRriutx11BMtUHh.png', 1, 11, '2025-11-07 19:20:02', '2025-11-07 19:20:36', NULL, 'vddkiapic'),
(44, 'hgjhgghgjkokokokokok', 'hgjhghjgjhgjh', 4.00, 'products/zrMU5AT7TE0La5KOn9oMXjbstIQERsgckY7Eog5W.png', 1, 9, '2025-11-07 22:00:55', '2025-11-07 22:00:55', NULL, 'gjgj');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `receivables`
--

CREATE TABLE `receivables` (
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id` int(11) NOT NULL,
  `num_comprobante_abono` varchar(20) NOT NULL,
  `valor_abono` decimal(10,2) NOT NULL,
  `id_sale` int(11) UNSIGNED NOT NULL,
  `observacion` varchar(250) DEFAULT NULL,
  `secuencial` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sales`
--

CREATE TABLE `sales` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_customer` int(11) UNSIGNED NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `clave_acceso` varchar(250) DEFAULT NULL,
  `type_receivable` int(11) UNSIGNED NOT NULL,
  `id_point_of_sale` int(11) NOT NULL,
  `establecimiento` varchar(3) NOT NULL,
  `punto_emision` varchar(3) NOT NULL,
  `secuencial` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `id_customer`, `total`, `state`, `created_at`, `updated_at`, `deleted_at`, `subtotal`, `discount`, `numero_factura`, `iva`, `clave_acceso`, `type_receivable`, `id_point_of_sale`, `establecimiento`, `punto_emision`, `secuencial`) VALUES
(988, 1, 4.60, 1, '2025-11-11 20:33:56', '2025-11-11 20:33:56', NULL, 4.00, 0.00, '001-001-000000018', 0.60, '1111202501171825163800110010010000000181234567816', 1, 1, '001', '001', '000000018'),
(990, 1, 75.90, 1, '2025-11-11 20:51:44', '2025-11-11 20:51:44', NULL, 66.00, 0.00, '002-001-000000029', 9.90, '1111202501171825163800110020010000000291234567811', 1, 3, '002', '001', '000000029'),
(991, 2, 4.60, 1, '2025-11-11 20:53:44', '2025-11-11 20:53:44', NULL, 4.00, 0.00, '003-001-000000039', 0.60, '1111202501171825163800110030010000000391234567811', 1, 5, '003', '001', '000000039'),
(992, 4, 98.33, 1, '2025-11-17 21:40:00', '2025-11-17 21:40:00', NULL, 90.00, 4.50, '001-001-000000019', 12.83, '1711202501171825163800110010010000000191234567819', 1, 1, '001', '001', '000000019');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sale_details`
--

CREATE TABLE `sale_details` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_sale` int(11) UNSIGNED NOT NULL,
  `id_product` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sale_details`
--

INSERT INTO `sale_details` (`id`, `id_sale`, `id_product`, `quantity`, `price`, `subtotal`, `created_at`, `updated_at`, `discount`) VALUES
(1348, 988, 44, 1, 4.00, 4.00, '2025-11-11 20:33:56', '2025-11-11 20:33:56', 0.00),
(1349, 990, 42, 1, 66.00, 66.00, '2025-11-11 20:51:44', '2025-11-11 20:51:44', 0.00),
(1350, 991, 44, 1, 4.00, 4.00, '2025-11-11 20:53:44', '2025-11-11 20:53:44', 0.00),
(1351, 992, 41, 2, 45.00, 90.00, '2025-11-17 21:40:00', '2025-11-17 21:40:00', 4.50);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `num_identificador` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `num_identificador`, `email`, `phone`, `address`, `state`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'juan Calderon', '1712347623001', 'calderon@gmail.com', '0987765410', 'Av los arupos', 1, '2025-10-08 20:49:51', '2025-10-08 20:55:09', NULL),
(2, 'juan chicaiza', '1718234611', 'juanimport@gmail.com', '3238765', 'el inca jaja', 1, '2025-10-12 03:29:53', '2025-10-12 03:29:53', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `uniqid` varchar(255) DEFAULT NULL,
  `code_verified` varchar(255) DEFAULT NULL,
  `id_point_of_sale` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `imagen` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `email_verified_at`, `uniqid`, `code_verified`, `id_point_of_sale`, `created_at`, `updated_at`, `deleted_at`, `imagen`) VALUES
(35, 'Adrian Ortiz', 'adrian-2222@hotmail.com', '$2y$12$9OtF1/8fp1s3jQ6XFBzPGeNqi8VYoK0Fgx2ZL/Hsqj28DtHCN1sC2', 'user', '2025-07-10 14:10:57', '686fc6f78348a', NULL, 1, '2025-07-10 13:58:15', '2025-11-17 21:30:43', NULL, 'users/D9HrG5Drw33fBqJcOeGvjtSdVIQPrmaNpySL5RSS.jpg'),
(37, 'juan piguabe cacerez', 'juanpiguabe@gmail.com', '$2y$12$Q5fHCnWfpO/OwI9THlON.O87kHCSa2Nf2X.8eUfudvzjpeD9UqHHq', 'user', NULL, '68d5acfdd437a', NULL, 2, '2025-09-25 20:58:37', '2025-09-25 20:58:37', NULL, NULL),
(62, 'joel eduardo luna', 'joeleduardolunamoya7@gmail.com', '$2y$12$O2NVOHORhqOu7n8U9XcNSupTawtdgDypsDQYzjEBaw/k/S5x4iWpG', 'user', '2025-11-10 02:36:45', '690e6a8b286ef', NULL, 5, '2025-11-07 21:54:19', '2025-11-18 16:53:17', NULL, 'users/1it3IBqfBWmwKRwpoH1S1Rfuy0aQhMVyQ1U8xu63.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `buys`
--
ALTER TABLE `buys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_fac` (`numero_factura`),
  ADD KEY `id_supplier` (`id_supplier`),
  ADD KEY `fk_points_of_sale` (`id_point_of_sale`);

--
-- Indices de la tabla `buy_details`
--
ALTER TABLE `buy_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_id_order_foreign` (`id_buy`),
  ADD KEY `order_details_id_product_foreign` (`id_product`);

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `configurations`
--
ALTER TABLE `configurations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indices de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indices de la tabla `inventories`
--
ALTER TABLE `inventories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Product` (`id_product`),
  ADD KEY `fk_Branch` (`id_branch`);

--
-- Indices de la tabla `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indices de la tabla `pays`
--
ALTER TABLE `pays`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_buy` (`id_buy`);

--
-- Indices de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indices de la tabla `points_of_sales`
--
ALTER TABLE `points_of_sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_Branchhh` (`id_branch`);

--
-- Indices de la tabla `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_id_categorie_foreign` (`id_categorie`);

--
-- Indices de la tabla `receivables`
--
ALTER TABLE `receivables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_saleid` (`id_sale`);

--
-- Indices de la tabla `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `num_fac` (`numero_factura`),
  ADD KEY `orders_id_customer_foreign` (`id_customer`),
  ADD KEY `fk_point_sale22` (`id_point_of_sale`);

--
-- Indices de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_id_order_foreign` (`id_sale`),
  ADD KEY `order_details_id_product_foreign` (`id_product`);

--
-- Indices de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `fk_point_sale` (`id_point_of_sale`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `buys`
--
ALTER TABLE `buys`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=995;

--
-- AUTO_INCREMENT de la tabla `buy_details`
--
ALTER TABLE `buy_details`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1359;

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `configurations`
--
ALTER TABLE `configurations`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pays`
--
ALTER TABLE `pays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT de la tabla `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `points_of_sales`
--
ALTER TABLE `points_of_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `receivables`
--
ALTER TABLE `receivables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=993;

--
-- AUTO_INCREMENT de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1352;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `buys`
--
ALTER TABLE `buys`
  ADD CONSTRAINT `buys_ibfk_1` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id`),
  ADD CONSTRAINT `fk_points_of_sale` FOREIGN KEY (`id_point_of_sale`) REFERENCES `points_of_sales` (`id`);

--
-- Filtros para la tabla `buy_details`
--
ALTER TABLE `buy_details`
  ADD CONSTRAINT `buy_details_ibfk_1` FOREIGN KEY (`id_buy`) REFERENCES `buys` (`id`);

--
-- Filtros para la tabla `inventories`
--
ALTER TABLE `inventories`
  ADD CONSTRAINT `fk_Branch` FOREIGN KEY (`id_branch`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `fk_Product` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `pays`
--
ALTER TABLE `pays`
  ADD CONSTRAINT `pays_ibfk_1` FOREIGN KEY (`id_buy`) REFERENCES `buys` (`id`);

--
-- Filtros para la tabla `points_of_sales`
--
ALTER TABLE `points_of_sales`
  ADD CONSTRAINT `fk_Branchhh` FOREIGN KEY (`id_branch`) REFERENCES `branches` (`id`);

--
-- Filtros para la tabla `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_id_categorie_foreign` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id`);

--
-- Filtros para la tabla `receivables`
--
ALTER TABLE `receivables`
  ADD CONSTRAINT `fk_saleid` FOREIGN KEY (`id_sale`) REFERENCES `sales` (`id`);

--
-- Filtros para la tabla `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_point_sale22` FOREIGN KEY (`id_point_of_sale`) REFERENCES `points_of_sales` (`id`),
  ADD CONSTRAINT `orders_id_customer_foreign` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`);

--
-- Filtros para la tabla `sale_details`
--
ALTER TABLE `sale_details`
  ADD CONSTRAINT `order_details_id_order_foreign` FOREIGN KEY (`id_sale`) REFERENCES `sales` (`id`),
  ADD CONSTRAINT `order_details_id_product_foreign` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`);

--
-- Filtros para la tabla `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_point_sale` FOREIGN KEY (`id_point_of_sale`) REFERENCES `points_of_sales` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
