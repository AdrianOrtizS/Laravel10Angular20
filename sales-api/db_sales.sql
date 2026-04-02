-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 02-04-2026 a las 05:17:26
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
(1, '001', 'Los Libertadores', 'Av los Libertadores y psj Viracocha', '2665876', '2025-09-16 21:49:23', '2025-12-25 20:20:19', NULL, 1),
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
(5, 'categorie 1.', 'description5', 'categories/Pv0NjfYhuCKAJJMC0OE3nxXNu9UFggercjjohKZH.png', 1, '2022-01-24 20:16:37', '2025-12-26 03:32:54', NULL),
(9, 'categorie 2', 'la categoria 99 es la primera', 'categories/Hbarork1fydPqzCRZKDc2ds7ScWwn8XaMAu9IOg0.png', 1, '2025-07-11 21:49:16', '2025-12-26 03:33:00', NULL),
(10, 'categorie 3', 'description5 de categoria 33333', 'categories/WGRVbq5MiUxTt9RyyluK3TAMCQfXQ2LXtZws8v1X.png', 1, '2025-06-24 20:16:37', '2025-11-21 20:25:38', NULL),
(11, 'categorie 4', 'description5', 'categories/e1QQnx2POK9FJUH8NqvOPc5Ur44BMz6tegSbIgzO.png', 1, '2025-06-24 20:16:37', '2025-12-26 03:33:14', NULL),
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
(1, '2025-08-28 15:05:13', '2026-03-31 19:36:43', NULL, 'version', '2.1.0', 1),
(2, '2025-08-28 15:13:54', '2025-12-26 03:26:55', NULL, 'ambiente', '1', 1),
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
(3, 'customer3', '1734653333', 'customer3@gmail.com', '0922234765', 'avenida addrress', 1, '2025-06-24 21:24:56', '2025-12-25 22:18:06', NULL),
(4, 'customer4 123', '1734653344', 'customer4@gmail.com', '0922234765', 'avenida addrress', 1, '2025-06-24 21:26:38', '2025-12-25 22:18:09', NULL),
(5, 'customer5', '1735533344', 'customer5@gmail.com', '0922234765', 'avenida addrress', 1, '2025-06-24 21:26:57', '2025-07-30 15:32:16', NULL),
(19, 'juan piguabe', '1718276543', 'asd@gmail.com', '57657657', 'gjhjhghjgjhjhgjhgjhgjhgjhh', 1, '2025-08-01 19:33:39', '2025-08-01 19:33:39', NULL),
(25, 'mr popo', '1231231234', 'popo@gmail.com', '0909090987', '170101', 1, '2025-08-07 20:26:06', '2025-08-07 20:26:06', NULL),
(26, 'jijijijiijiji', '76867868786', 'jiji@gmail.com', '6876877', 'hkjhkjhkjhkjhkj', 1, '2025-08-07 21:06:20', '2025-08-07 21:06:20', NULL),
(27, 'andrea gutierrez', '1718348053', 'andre@gmail.com', '3332349823', 'la magdalena calle 1 y la que cruza mas por halla', 1, '2025-08-07 21:36:16', '2025-08-07 21:36:16', NULL),
(28, 'desirek luzgardeth', '1712334210', 'desirek@gmail.com', '0987654543', 'Atahualpa 123', 1, '2025-11-25 02:35:08', '2025-11-25 02:35:08', NULL);

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

--
-- Volcado de datos para la tabla `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(30, '2f55f505-0314-4b67-8348-2b222aaf0d6d', 'database', 'default', '{\"uuid\":\"2f55f505-0314-4b67-8348-2b222aaf0d6d\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"3003202601171825163800110010020000003661234567811\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1860;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1774895014;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:2;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1774897542;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-03-30 14:06:02.197077\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:17:\\\"America\\/Guayaquil\\\";}}\"}}', 'ReflectionException: Class \"App\\Jobs\\FacturaService\" does not exist in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php:912\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(912): ReflectionClass->__construct()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(795): Illuminate\\Container\\Container->build()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(986): Illuminate\\Container\\Container->resolve()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(731): Illuminate\\Foundation\\Application->resolve()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(971): Illuminate\\Container\\Container->make()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php(120): Illuminate\\Foundation\\Application->make()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(210): app()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(65): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#41 {main}\n\nNext Illuminate\\Contracts\\Container\\BindingResolutionException: Target class [App\\Jobs\\FacturaService] does not exist. in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php:914\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(795): Illuminate\\Container\\Container->build()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(986): Illuminate\\Container\\Container->resolve()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(731): Illuminate\\Foundation\\Application->resolve()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(971): Illuminate\\Container\\Container->make()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php(120): Illuminate\\Foundation\\Application->make()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(210): app()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(65): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#40 {main}', '2026-03-30 19:06:04'),
(31, '9a96b16c-2910-4698-ba6e-044aaaa80414', 'database', 'default', '{\"uuid\":\"9a96b16c-2910-4698-ba6e-044aaaa80414\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"3003202601171825163800110010020000003671234567817\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1861;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1774898461;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:0;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1774898461;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-03-30 14:21:11.763725\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"}}', 'ReflectionException: Class \"App\\Jobs\\FacturaService\" does not exist in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php:912\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(912): ReflectionClass->__construct()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(795): Illuminate\\Container\\Container->build()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(986): Illuminate\\Container\\Container->resolve()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(731): Illuminate\\Foundation\\Application->resolve()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(971): Illuminate\\Container\\Container->make()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php(120): Illuminate\\Foundation\\Application->make()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(210): app()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(65): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#41 {main}\n\nNext Illuminate\\Contracts\\Container\\BindingResolutionException: Target class [App\\Jobs\\FacturaService] does not exist. in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php:914\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(795): Illuminate\\Container\\Container->build()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(986): Illuminate\\Container\\Container->resolve()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(731): Illuminate\\Foundation\\Application->resolve()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(971): Illuminate\\Container\\Container->make()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php(120): Illuminate\\Foundation\\Application->make()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(210): app()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(65): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#40 {main}', '2026-03-30 19:21:12'),
(32, '2398b63c-adb5-467a-81bf-f9aa68b0666f', 'database', 'default', '{\"uuid\":\"2398b63c-adb5-467a-81bf-f9aa68b0666f\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"3003202601171825163800110010020000003681234567812\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1862;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1774898956;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:2;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1774901418;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-03-30 15:10:38.135432\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"}}', 'ReflectionException: Class \"App\\Jobs\\FacturaService\" does not exist in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php:912\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(912): ReflectionClass->__construct()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(795): Illuminate\\Container\\Container->build()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(986): Illuminate\\Container\\Container->resolve()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(731): Illuminate\\Foundation\\Application->resolve()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(971): Illuminate\\Container\\Container->make()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php(120): Illuminate\\Foundation\\Application->make()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(210): app()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(65): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#41 {main}\n\nNext Illuminate\\Contracts\\Container\\BindingResolutionException: Target class [App\\Jobs\\FacturaService] does not exist. in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php:914\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(795): Illuminate\\Container\\Container->build()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(986): Illuminate\\Container\\Container->resolve()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(731): Illuminate\\Foundation\\Application->resolve()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(971): Illuminate\\Container\\Container->make()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/helpers.php(120): Illuminate\\Foundation\\Application->make()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(210): app()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(65): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#40 {main}', '2026-03-30 20:10:40');
INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(33, '81cb826d-7684-4f43-b0e9-744eda400aa0', 'database', 'default', '{\"uuid\":\"81cb826d-7684-4f43-b0e9-744eda400aa0\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"0104202601171825163800110010020000003771234567811\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1882;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1775085455;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:0;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1775085455;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-04-01 18:17:45.867926\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"}}', 'PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T18:17:32-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:612\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(612): PDOStatement->execute()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(816): Illuminate\\Database\\Connection->Illuminate\\Database\\{closure}()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#43 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#44 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#45 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T18:17:32-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 (Connection: mysql, SQL: update `sales` set `estado_sri` = AUTORIZADO, `numero_autorizacion` = 0104202601171825163800110010020000003771234567811, `fecha_autorizacion_sri` = 2026-04-01T18:17:32-05:00, `sales`.`updated_at` = 2026-04-01 18:21:11 where `id` = 1882) in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:829\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#43 {main}', '2026-04-01 23:21:12'),
(34, '25dbf20f-aa48-4400-aa2e-649b2561c798', 'database', 'default', '{\"uuid\":\"25dbf20f-aa48-4400-aa2e-649b2561c798\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"0104202601171825163800110010020000003791234567812\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1883;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1775085861;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:0;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1775085861;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-04-01 18:24:31.130399\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"}}', 'PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T18:24:18-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:612\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(612): PDOStatement->execute()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(816): Illuminate\\Database\\Connection->Illuminate\\Database\\{closure}()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#43 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#44 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#45 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T18:24:18-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 (Connection: mysql, SQL: update `sales` set `estado_sri` = AUTORIZADO, `numero_autorizacion` = 0104202601171825163800110010020000003791234567812, `fecha_autorizacion_sri` = 2026-04-01T18:24:18-05:00, `sales`.`updated_at` = 2026-04-01 18:24:36 where `id` = 1883) in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:829\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#43 {main}', '2026-04-01 23:24:37');
INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(35, '14d1b3e2-52f4-4f83-b912-7280e98daf54', 'database', 'default', '{\"uuid\":\"14d1b3e2-52f4-4f83-b912-7280e98daf54\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"0104202601171825163800110010020000003801234567818\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1884;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1775086824;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:1;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1775088092;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-04-01 19:01:52.709378\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"}}', 'PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T19:01:29-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:612\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(612): PDOStatement->execute()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(816): Illuminate\\Database\\Connection->Illuminate\\Database\\{closure}()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#43 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#44 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#45 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T19:01:29-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 (Connection: mysql, SQL: update `sales` set `estado_sri` = AUTORIZADO, `numero_autorizacion` = 0104202601171825163800110010020000003801234567818, `fecha_autorizacion_sri` = 2026-04-01T19:01:29-05:00, `sales`.`updated_at` = 2026-04-01 19:01:57 where `id` = 1884) in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:829\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#43 {main}', '2026-04-02 00:01:57'),
(36, 'ad17b58f-48ff-41f2-bb1e-2ab87805a8d9', 'database', 'default', '{\"uuid\":\"ad17b58f-48ff-41f2-bb1e-2ab87805a8d9\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"0104202601171825163800110010020000003811234567813\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1885;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1775089738;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:0;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1775089738;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-04-01 19:29:08.469964\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"}}', 'PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T19:28:55-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:612\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(612): PDOStatement->execute()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(816): Illuminate\\Database\\Connection->Illuminate\\Database\\{closure}()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#43 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#44 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#45 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T19:28:55-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 (Connection: mysql, SQL: update `sales` set `estado_sri` = AUTORIZADO, `numero_autorizacion` = 0104202601171825163800110010020000003811234567813, `fecha_autorizacion_sri` = 2026-04-01T19:28:55-05:00, `sales`.`updated_at` = 2026-04-01 19:29:12 where `id` = 1885) in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:829\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#43 {main}', '2026-04-02 00:29:12');
INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(37, 'aada1a8d-d510-4c73-863a-b0743b373aa0', 'database', 'default', '{\"uuid\":\"aada1a8d-d510-4c73-863a-b0743b373aa0\",\"displayName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":1,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":60,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\",\"command\":\"O:36:\\\"App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\\":7:{s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000claveAcceso\\\";s:49:\\\"0104202601171825163800110010020000003821234567819\\\";s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ambiente\\\";s:7:\\\"pruebas\\\";s:44:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000saleId\\\";i:1886;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000creadoEn\\\";i:1775090122;s:46:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000reenvios\\\";i:0;s:49:\\\"\\u0000App\\\\Jobs\\\\ConsultarAutorizacionSriJob\\u0000ultimoEnvio\\\";i:1775090122;s:5:\\\"delay\\\";O:25:\\\"Illuminate\\\\Support\\\\Carbon\\\":3:{s:4:\\\"date\\\";s:26:\\\"2026-04-01 19:35:32.463745\\\";s:13:\\\"timezone_type\\\";i:3;s:8:\\\"timezone\\\";s:12:\\\"America\\/Lima\\\";}}\"}}', 'PDOException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T19:35:19-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:612\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(612): PDOStatement->execute()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(816): Illuminate\\Database\\Connection->Illuminate\\Database\\{closure}()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#43 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#44 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#45 {main}\n\nNext Illuminate\\Database\\QueryException: SQLSTATE[22007]: Invalid datetime format: 1292 Incorrect datetime value: \'2026-04-01T19:35:19-05:00\' for column `db_sales`.`sales`.`fecha_autorizacion_sri` at row 1 (Connection: mysql, SQL: update `sales` set `estado_sri` = AUTORIZADO, `numero_autorizacion` = 0104202601171825163800110010020000003821234567819, `fecha_autorizacion_sri` = 2026-04-01T19:35:19-05:00, `sales`.`updated_at` = 2026-04-01 19:35:37 where `id` = 1886) in /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php:829\nStack trace:\n#0 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(783): Illuminate\\Database\\Connection->runQueryCallback()\n#1 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(600): Illuminate\\Database\\Connection->run()\n#2 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Connection.php(552): Illuminate\\Database\\Connection->affectingStatement()\n#3 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php(3602): Illuminate\\Database\\Connection->update()\n#4 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php(1061): Illuminate\\Database\\Query\\Builder->update()\n#5 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1214): Illuminate\\Database\\Eloquent\\Builder->update()\n#6 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(1131): Illuminate\\Database\\Eloquent\\Model->performUpdate()\n#7 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Model.php(991): Illuminate\\Database\\Eloquent\\Model->save()\n#8 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(228): Illuminate\\Database\\Eloquent\\Model->update()\n#9 /home/llinux/Documents/Laravel10Angular20/sales-api/app/Jobs/ConsultarAutorizacionSriJob.php(72): App\\Jobs\\ConsultarAutorizacionSriJob->procesarAutorizado()\n#10 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): App\\Jobs\\ConsultarAutorizacionSriJob->handle()\n#11 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#12 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#13 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#14 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#15 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(128): Illuminate\\Container\\Container->call()\n#16 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}()\n#17 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#18 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(132): Illuminate\\Pipeline\\Pipeline->then()\n#19 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(123): Illuminate\\Bus\\Dispatcher->dispatchNow()\n#20 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(144): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}()\n#21 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(119): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}()\n#22 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(122): Illuminate\\Pipeline\\Pipeline->then()\n#23 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware()\n#24 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#25 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(439): Illuminate\\Queue\\Jobs\\Job->fire()\n#26 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(389): Illuminate\\Queue\\Worker->process()\n#27 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(176): Illuminate\\Queue\\Worker->runJob()\n#28 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(137): Illuminate\\Queue\\Worker->daemon()\n#29 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(120): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#30 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#31 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Util.php(41): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#32 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(93): Illuminate\\Container\\Util::unwrapIfClosure()\n#33 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#34 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Container/Container.php(662): Illuminate\\Container\\BoundMethod::call()\n#35 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#36 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Command/Command.php(326): Illuminate\\Console\\Command->execute()\n#37 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#38 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(1121): Illuminate\\Console\\Command->run()\n#39 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(324): Symfony\\Component\\Console\\Application->doRunCommand()\n#40 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/symfony/console/Application.php(175): Symfony\\Component\\Console\\Application->doRun()\n#41 /home/llinux/Documents/Laravel10Angular20/sales-api/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(201): Symfony\\Component\\Console\\Application->run()\n#42 /home/llinux/Documents/Laravel10Angular20/sales-api/artisan(35): Illuminate\\Foundation\\Console\\Kernel->handle()\n#43 {main}', '2026-04-02 00:35:37');

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
  `stock_min` int(11) NOT NULL DEFAULT 0,
  `id_product` int(11) UNSIGNED NOT NULL,
  `id_branch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventories`
--

INSERT INTO `inventories` (`id`, `created_at`, `updated_at`, `deleted_at`, `stock`, `stock_min`, `id_product`, `id_branch`) VALUES
(26, '2025-11-06 01:37:42', '2025-12-25 20:45:38', NULL, 2, 5, 41, 3),
(27, '2025-11-06 01:37:42', '2026-04-02 03:09:54', NULL, -5098, 5, 41, 1),
(28, '2025-11-06 01:37:42', '2025-11-06 01:37:42', NULL, 0, 0, 41, 2),
(29, '2025-11-07 19:13:43', '2025-11-11 21:05:35', NULL, 20, 3, 42, 3),
(30, '2025-11-07 19:13:43', '2026-04-02 03:09:54', NULL, -38, 6, 42, 1),
(31, '2025-11-07 19:13:43', '2025-11-11 20:51:44', NULL, 6, 0, 42, 2),
(32, '2025-11-07 19:20:02', '2025-12-25 21:52:04', NULL, -1, 2, 43, 3),
(33, '2025-11-07 19:20:02', '2026-03-30 18:23:32', NULL, 3, 5, 43, 1),
(34, '2025-11-07 19:20:02', '2025-11-07 19:20:02', NULL, 0, 0, 43, 2),
(35, '2025-11-07 22:00:55', '2025-11-11 20:53:44', NULL, 4, 5, 44, 3),
(36, '2025-11-07 22:00:55', '2026-04-02 03:09:54', NULL, -36, 7, 44, 1),
(37, '2025-11-07 22:00:55', '2025-11-10 02:50:36', NULL, 10, 0, 44, 2),
(62, '2026-03-27 15:31:00', '2026-03-27 15:31:00', NULL, 5, 4, 53, 1),
(63, '2026-03-27 15:31:00', '2026-03-27 15:31:00', NULL, 0, 0, 53, 2),
(64, '2026-03-27 15:31:00', '2026-03-27 15:31:00', NULL, 0, 0, 53, 3),
(65, '2026-03-27 15:31:45', '2026-04-02 01:43:24', NULL, -14, 5, 54, 1),
(66, '2026-03-27 15:31:45', '2026-03-27 15:31:45', NULL, 0, 0, 54, 2),
(67, '2026-03-27 15:31:45', '2026-03-27 15:31:45', NULL, 0, 0, 54, 3),
(68, '2026-03-27 19:45:50', '2026-04-02 03:09:54', NULL, -22, 3, 55, 1),
(69, '2026-03-27 19:45:50', '2026-03-27 19:45:50', NULL, 0, 0, 55, 2),
(70, '2026-03-27 19:45:50', '2026-03-27 19:45:50', NULL, 0, 0, 55, 3),
(71, '2026-03-27 19:55:20', '2026-04-02 03:02:45', NULL, -21, 3, 56, 1),
(72, '2026-03-27 19:55:20', '2026-03-27 19:55:20', NULL, 0, 0, 56, 2),
(73, '2026-03-27 19:55:20', '2026-03-27 19:55:20', NULL, 0, 0, 56, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(9, '2025_05_25_001355_create_order_details_table', 1),
(10, '2026_03_14_183439_add_estado_sri_to_sales_table', 2),
(11, '2026_03_14_184524_create_jobs_table', 3);

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
('2025-11-10 02:43:04', '2025-11-10 02:43:04', NULL, 138, '687686868767', 38.00, 'pays/526T0JBGrtc4IkmliClq8dCXjmlWpFZZVgYlXV29.png', 991),
('2025-12-25 20:44:26', '2025-12-25 20:44:26', NULL, 139, 'g657576577', 5.00, NULL, 992);

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
(1, 1, '001', 77, 'maquina principal', NULL, '2026-03-06 14:18:42', NULL),
(2, 1, '002', 419, 'maquina secundaria', '2025-11-06 21:46:49', '2026-04-02 03:09:54', NULL),
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
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `imagen` varchar(255) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1,
  `id_categorie` int(11) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `cod_pro` varchar(20) DEFAULT NULL,
  `id_tarifa_iva` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `imagen`, `state`, `id_categorie`, `created_at`, `updated_at`, `deleted_at`, `cod_pro`, `id_tarifa_iva`) VALUES
(41, 'parabrisas delantero luv 2300', 'parabrisas delantero luv 2300', 45.00, 'products/3Y89S5jLbkNmjSGMEhvdRpnFqWfyzkNAE7kmYeOZ.png', 1, 5, '2025-11-06 01:37:42', '2025-12-26 03:48:56', NULL, 'pdluv2300', 2),
(42, 'PD Chevrolet Aveo activo 2010', 'parabrisas delantero aveo activo, family', 66.00, 'products/S2DX0Q2GdTlsIYfzLgre4K9XVWak19RURAqMD4BO.png', 1, 10, '2025-11-07 19:13:43', '2025-11-07 19:13:43', NULL, 'pdaveoact', 2),
(43, 'ventana delantera derecha kia picanto 2019', 'ventana delantera derecha kia picanto 2019', 66.00, 'products/xJO1fH6iBCVM9oaOkFeFD0vgoCl6aehPNMgIRsiA.webp', 1, 11, '2025-11-07 19:20:02', '2026-03-26 02:54:19', NULL, 'vddkiapic', 1),
(44, 'espejo lateral', 'espejo lateral aveo', 4.00, 'products/zrMU5AT7TE0La5KOn9oMXjbstIQERsgckY7Eog5W.png', 1, 9, '2025-11-07 22:00:55', '2026-03-27 15:39:56', NULL, 'esp01', 1),
(53, 'prueba tarifa', 'jffhgfhgfhfhgfhg', 6.00, NULL, 1, 32, '2026-03-27 15:31:00', '2026-03-27 15:31:00', NULL, 'pr00w', 2),
(54, 'jhjh', 'ghfhgfhgfhg', 5.00, NULL, 1, 11, '2026-03-27 15:31:45', '2026-03-27 20:05:13', NULL, 'prw3', 2),
(55, 'ftfyfytfyty5tf', 'fgfggfgfgf', 5.00, NULL, 1, 10, '2026-03-27 19:45:50', '2026-03-27 19:45:50', NULL, 'gyt5', 2),
(56, 'hghghgfhgfg', 'dfdgfdgff', 5.00, NULL, 1, 10, '2026-03-27 19:55:20', '2026-03-28 21:50:57', NULL, '12iv', 2);

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
  `subtotal` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) DEFAULT NULL,
  `numero_factura` varchar(20) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `iva0` decimal(10,2) NOT NULL,
  `clave_acceso` varchar(250) DEFAULT NULL,
  `estado_sri` varchar(20) NOT NULL DEFAULT '''PENDIENTE''',
  `numero_autorizacion` varchar(50) DEFAULT NULL,
  `fecha_autorizacion_sri` timestamp NULL DEFAULT NULL,
  `type_receivable` int(11) UNSIGNED NOT NULL,
  `id_point_of_sale` int(11) NOT NULL,
  `establecimiento` varchar(3) NOT NULL,
  `punto_emision` varchar(3) NOT NULL,
  `secuencial` varchar(11) NOT NULL,
  `ambiente` varchar(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sales`
--

INSERT INTO `sales` (`id`, `id_customer`, `total`, `state`, `subtotal`, `discount`, `numero_factura`, `iva`, `iva0`, `clave_acceso`, `estado_sri`, `numero_autorizacion`, `fecha_autorizacion_sri`, `type_receivable`, `id_point_of_sale`, `establecimiento`, `punto_emision`, `secuencial`, `ambiente`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1870, 1, 5.75, 1, 5.00, 0.00, '001-002-000000374', 0.75, 0.00, '3103202601171825163800110010020000003741234567811', 'AUTORIZADO', NULL, NULL, 1, 2, '001', '002', '000000374', '1', '2026-03-31 12:13:05', '2026-03-31 18:58:05', NULL),
(1872, 27, 100.40, 1, 90.00, 2.70, '001-002-000000375', 13.10, 0.00, '3103202601171825163800110010020000003751234567815', 'NO AUTORIZADO', NULL, NULL, 1, 2, '001', '002', '000000375', '1', '2026-03-31 17:06:30', '2026-03-31 19:39:46', NULL),
(1873, 27, 4.00, 1, 4.00, 0.00, '001-002-000000376', 0.00, 4.00, '3103202601171825163800110010020000003761234567810', 'AUTORIZADO', NULL, NULL, 1, 2, '001', '002', '000000376', '1', '2026-03-31 17:07:52', '2026-03-31 18:56:57', NULL),
(1887, 27, 55.75, 1, 49.00, 0.00, '001-002-000000384', 6.75, 4.00, '0104202601171825163800110010020000003841234567811', 'AUTORIZADO', '0104202601171825163800110010020000003841234567811', '2026-04-02 01:00:06', 1, 2, '001', '002', '000000384', '1', '2026-04-02 01:00:05', '2026-04-02 01:00:23', NULL),
(1888, 2, 102.33, 1, 94.00, 4.50, '001-002-000000386', 12.83, 4.00, '0104202601171825163800110010020000003861234567810', 'AUTORIZADO', '0104202601171825163800110010020000003861234567810', '2026-04-02 01:16:19', 1, 2, '001', '002', '000000386', '1', '2026-04-02 01:16:18', '2026-04-02 01:16:36', NULL),
(1889, 27, 390.69, 1, 369.00, 28.75, '001-002-000000388', 50.44, 4.00, '0104202601171825163800110010020000003881234567811', 'NO AUTORIZADO', NULL, NULL, 1, 2, '001', '002', '000000388', '1', '2026-04-02 01:26:42', '2026-04-02 01:27:00', NULL),
(1890, 27, 185.99, 1, 169.00, 6.75, '001-002-000000393', 23.74, 4.00, '0104202601171825163800110010020000003931234567819', 'AUTORIZADO', '0104202601171825163800110010020000003931234567819', '2026-04-02 01:43:25', 1, 2, '001', '002', '000000393', '1', '2026-04-02 01:43:24', '2026-04-02 01:43:41', NULL),
(1891, 1, 514.21, 1, 477.00, 29.34, '001-002-000000397', 66.55, 4.00, '0104202601171825163800110010020000003971234567810', 'NO AUTORIZADO', NULL, NULL, 1, 2, '001', '002', '000000397', '1', '2026-04-02 02:02:25', '2026-04-02 02:02:45', NULL),
(1892, 27, 491.30, 1, 446.00, 16.70, '001-002-000000401', 62.00, 16.00, '0104202601171825163800110010020000004011234567810', 'AUTORIZADO', '0104202601171825163800110010020000004011234567810', '2026-04-02 02:46:27', 1, 2, '001', '002', '000000401', '1', '2026-04-02 02:46:26', '2026-04-02 02:46:44', NULL),
(1893, 27, 510.77, 1, 473.00, 23.79, '001-002-000000405', 61.56, 38.80, '0104202601171825163800110010020000004051234567812', 'AUTORIZADO', '0104202601171825163800110010020000004051234567812', '2026-04-02 02:49:27', 1, 2, '001', '002', '000000405', '1', '2026-04-02 02:49:26', '2026-04-02 02:49:43', NULL),
(1894, 2, 55.75, 1, 49.00, 0.00, '001-002-000000410', 6.75, 4.00, '0104202601171825163800110010020000004101234567811', 'AUTORIZADO', '0104202601171825163800110010020000004101234567811', '2026-04-02 02:59:53', 1, 2, '001', '002', '000000410', '1', '2026-04-02 02:59:52', '2026-04-02 03:00:10', NULL),
(1895, 2, 474.06, 1, 434.00, 21.25, '001-002-000000412', 61.31, 4.00, '0104202601171825163800110010020000004121234567810', 'AUTORIZADO', '0104202601171825163800110010020000004121234567810', '2026-04-02 03:02:46', 1, 2, '001', '002', '000000412', '1', '2026-04-02 03:02:45', '2026-04-02 03:03:02', NULL),
(1896, 27, 1598.59, 1, 1402.00, 11.40, '001-002-000000416', 207.99, 4.00, '0104202601171825163800110010020000004161234567812', 'AUTORIZADO', '0104202601171825163800110010020000004161234567812', '2026-04-02 03:09:55', 1, 2, '001', '002', '000000416', '1', '2026-04-02 03:09:54', '2026-04-02 03:10:13', NULL);

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
  `discount` decimal(10,2) DEFAULT NULL,
  `impuesto` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`impuesto`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `sale_details`
--

INSERT INTO `sale_details` (`id`, `id_sale`, `id_product`, `quantity`, `price`, `subtotal`, `created_at`, `updated_at`, `discount`, `impuesto`) VALUES
(2231, 1870, 54, 1, 5.00, 5.00, '2026-03-31 12:13:05', '2026-03-31 12:13:05', 0.00, NULL),
(2233, 1872, 41, 2, 45.00, 90.00, '2026-03-31 17:06:30', '2026-03-31 17:06:30', 2.70, NULL),
(2234, 1873, 44, 1, 4.00, 4.00, '2026-03-31 17:07:52', '2026-03-31 17:07:52', 0.00, NULL),
(2257, 1887, 44, 1, 4.00, 4.00, '2026-04-02 01:00:05', '2026-04-02 01:00:05', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}'),
(2258, 1887, 41, 1, 45.00, 45.00, '2026-04-02 01:00:05', '2026-04-02 01:00:05', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":45,\"valor\":\"6.75\"}'),
(2259, 1888, 41, 2, 45.00, 90.00, '2026-04-02 01:16:18', '2026-04-02 01:16:18', 4.50, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":85.5,\"valor\":\"12.82\"}'),
(2260, 1888, 44, 1, 4.00, 4.00, '2026-04-02 01:16:18', '2026-04-02 01:16:18', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}'),
(2261, 1889, 44, 1, 4.00, 4.00, '2026-04-02 01:26:42', '2026-04-02 01:26:42', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}'),
(2262, 1889, 55, 1, 5.00, 5.00, '2026-04-02 01:26:42', '2026-04-02 01:26:42', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":5,\"valor\":\"0.75\"}'),
(2263, 1889, 56, 11, 5.00, 55.00, '2026-04-02 01:26:42', '2026-04-02 01:26:42', 2.75, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":52.25,\"valor\":\"7.84\"}'),
(2264, 1889, 54, 16, 5.00, 80.00, '2026-04-02 01:26:42', '2026-04-02 01:26:42', 8.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":72,\"valor\":\"10.80\"}'),
(2265, 1889, 41, 5, 45.00, 225.00, '2026-04-02 01:26:42', '2026-04-02 01:26:42', 18.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":207,\"valor\":\"31.05\"}'),
(2266, 1890, 44, 1, 4.00, 4.00, '2026-04-02 01:43:24', '2026-04-02 01:43:24', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}'),
(2267, 1890, 55, 5, 5.00, 25.00, '2026-04-02 01:43:24', '2026-04-02 01:43:24', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":25,\"valor\":\"3.75\"}'),
(2268, 1890, 54, 1, 5.00, 5.00, '2026-04-02 01:43:24', '2026-04-02 01:43:24', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":5,\"valor\":\"0.75\"}'),
(2269, 1890, 41, 3, 45.00, 135.00, '2026-04-02 01:43:24', '2026-04-02 01:43:24', 6.75, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":128.25,\"valor\":\"19.24\"}'),
(2270, 1891, 55, 1, 5.00, 5.00, '2026-04-02 02:02:25', '2026-04-02 02:02:25', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":5,\"valor\":\"0.75\"}'),
(2271, 1891, 44, 1, 4.00, 4.00, '2026-04-02 02:02:25', '2026-04-02 02:02:25', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}'),
(2272, 1891, 41, 6, 45.00, 270.00, '2026-04-02 02:02:26', '2026-04-02 02:02:26', 13.50, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":256.5,\"valor\":\"38.48\"}'),
(2273, 1891, 42, 3, 66.00, 198.00, '2026-04-02 02:02:26', '2026-04-02 02:02:26', 15.84, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":182.16,\"valor\":\"27.32\"}'),
(2274, 1892, 44, 4, 4.00, 16.00, '2026-04-02 02:46:26', '2026-04-02 02:46:26', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":16,\"valor\":\"0.00\"}'),
(2275, 1892, 55, 2, 5.00, 10.00, '2026-04-02 02:46:26', '2026-04-02 02:46:26', 0.20, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":9.8,\"valor\":\"1.47\"}'),
(2276, 1892, 42, 5, 66.00, 330.00, '2026-04-02 02:46:27', '2026-04-02 02:46:27', 16.50, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":313.5,\"valor\":\"47.02\"}'),
(2277, 1892, 41, 2, 45.00, 90.00, '2026-04-02 02:46:27', '2026-04-02 02:46:27', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":90,\"valor\":\"13.50\"}'),
(2278, 1893, 41, 5, 45.00, 225.00, '2026-04-02 02:49:26', '2026-04-02 02:49:26', 6.75, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":218.25,\"valor\":\"32.74\"}'),
(2279, 1893, 44, 10, 4.00, 40.00, '2026-04-02 02:49:26', '2026-04-02 02:49:26', 1.20, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":38.8,\"valor\":\"0.00\"}'),
(2280, 1893, 56, 1, 5.00, 5.00, '2026-04-02 02:49:26', '2026-04-02 02:49:26', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":5,\"valor\":\"0.75\"}'),
(2281, 1893, 42, 3, 66.00, 198.00, '2026-04-02 02:49:26', '2026-04-02 02:49:26', 15.84, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":182.16,\"valor\":\"27.32\"}'),
(2282, 1893, 55, 1, 5.00, 5.00, '2026-04-02 02:49:26', '2026-04-02 02:49:26', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":5,\"valor\":\"0.75\"}'),
(2283, 1894, 41, 1, 45.00, 45.00, '2026-04-02 02:59:52', '2026-04-02 02:59:52', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":45,\"valor\":\"6.75\"}'),
(2284, 1894, 44, 1, 4.00, 4.00, '2026-04-02 02:59:52', '2026-04-02 02:59:52', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}'),
(2285, 1895, 44, 1, 4.00, 4.00, '2026-04-02 03:02:45', '2026-04-02 03:02:45', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}'),
(2286, 1895, 55, 1, 5.00, 5.00, '2026-04-02 03:02:45', '2026-04-02 03:02:45', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":5,\"valor\":\"0.75\"}'),
(2287, 1895, 56, 13, 5.00, 65.00, '2026-04-02 03:02:45', '2026-04-02 03:02:45', 3.25, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":61.75,\"valor\":\"9.26\"}'),
(2288, 1895, 41, 8, 45.00, 360.00, '2026-04-02 03:02:45', '2026-04-02 03:02:45', 18.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":342,\"valor\":\"51.30\"}'),
(2289, 1896, 41, 4, 45.00, 180.00, '2026-04-02 03:09:54', '2026-04-02 03:09:54', 10.80, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":169.2,\"valor\":\"25.38\"}'),
(2290, 1896, 55, 6, 5.00, 30.00, '2026-04-02 03:09:54', '2026-04-02 03:09:54', 0.60, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":29.4,\"valor\":\"4.41\"}'),
(2291, 1896, 42, 18, 66.00, 1188.00, '2026-04-02 03:09:54', '2026-04-02 03:09:54', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"4\",\"tarifa\":\"15\",\"baseImponible\":1188,\"valor\":\"178.20\"}'),
(2292, 1896, 44, 1, 4.00, 4.00, '2026-04-02 03:09:54', '2026-04-02 03:09:54', 0.00, '{\"codigo\":\"2\",\"codigoPorcentaje\":\"0\",\"tarifa\":\"0\",\"baseImponible\":4,\"valor\":\"0.00\"}');

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
(1, 'juan Calderon', '1712347623001', 'calderon@gmail.com', '0987765410', 'Av los arupos', 1, '2025-10-08 20:49:51', '2025-12-26 03:23:12', NULL),
(2, 'juan chicaiza', '1718234611', 'juanimport@gmail.com', '3238765', 'el inca jaja', 1, '2025-10-12 03:29:53', '2025-12-26 03:23:15', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarifa_ivas`
--

CREATE TABLE `tarifa_ivas` (
  `id` int(11) NOT NULL,
  `codigo` varchar(2) NOT NULL,
  `porcentaje` decimal(5,2) NOT NULL,
  `descripcion` varchar(100) DEFAULT NULL,
  `estado` tinyint(4) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarifa_ivas`
--

INSERT INTO `tarifa_ivas` (`id`, `codigo`, `porcentaje`, `descripcion`, `estado`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '0', 0.00, 'tarifa 0', 1, NULL, NULL, NULL),
(2, '1', 15.00, 'tarifa 15', 1, NULL, NULL, NULL);

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
  `imagen` varchar(250) DEFAULT NULL,
  `state` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `email_verified_at`, `uniqid`, `code_verified`, `id_point_of_sale`, `created_at`, `updated_at`, `deleted_at`, `imagen`, `state`) VALUES
(35, 'Adrian Ortiz', 'adrian-2222@hotmail.com', '$2y$12$9OtF1/8fp1s3jQ6XFBzPGeNqi8VYoK0Fgx2ZL/Hsqj28DtHCN1sC2', 'admin', '2025-07-10 14:10:57', '686fc6f78348a', NULL, 2, '2025-07-10 13:58:15', '2026-03-25 02:12:02', NULL, 'users/9BxP5xxcBAbsiyPVRJbnPYvVHd0EXw2ZxlnDXTIR.webp', 1),
(37, 'juan piguabe cacerez', 'juanpiguabe@gmail.com', '$2y$12$Q5fHCnWfpO/OwI9THlON.O87kHCSa2Nf2X.8eUfudvzjpeD9UqHHq', 'user', NULL, '68d5acfdd437a', NULL, 4, '2025-09-25 20:58:37', '2026-01-05 02:10:34', NULL, NULL, 1),
(62, 'joel eduardo luna', 'joeleduardolunamoya7@gmail.com', '$2y$12$O2NVOHORhqOu7n8U9XcNSupTawtdgDypsDQYzjEBaw/k/S5x4iWpG', 'user', '2025-11-10 02:36:45', '690e6a8b286ef', NULL, 5, '2025-11-07 21:54:19', '2026-01-05 02:08:25', NULL, 'users/lW0GuOn0drBqyET3vAVXSVJ27vtPQ08oE3F2SPIQ.png', 1);

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
-- Indices de la tabla `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

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
  ADD KEY `products_id_categorie_foreign` (`id_categorie`),
  ADD KEY `products_id_tarifa_iva_foreign` (`id_tarifa_iva`);

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
-- Indices de la tabla `tarifa_ivas`
--
ALTER TABLE `tarifa_ivas`
  ADD PRIMARY KEY (`id`);

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=996;

--
-- AUTO_INCREMENT de la tabla `buy_details`
--
ALTER TABLE `buy_details`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1360;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `inventories`
--
ALTER TABLE `inventories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5334;

--
-- AUTO_INCREMENT de la tabla `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `pays`
--
ALTER TABLE `pays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

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
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT de la tabla `receivables`
--
ALTER TABLE `receivables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT de la tabla `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1897;

--
-- AUTO_INCREMENT de la tabla `sale_details`
--
ALTER TABLE `sale_details`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2293;

--
-- AUTO_INCREMENT de la tabla `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tarifa_ivas`
--
ALTER TABLE `tarifa_ivas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  ADD CONSTRAINT `products_id_categorie_foreign` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_id_tarifa_iva_foreign` FOREIGN KEY (`id_tarifa_iva`) REFERENCES `tarifa_ivas` (`id`);

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
