-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-07-2025 a las 21:27:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `caps`
--

CREATE DATABASE IF NOT EXISTS `caps` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `caps`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caps`
--

CREATE TABLE `caps` (
  `id_caps` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `coordenadas` varchar(150) NOT NULL,
  `horario` time NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `telefono` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestaciones`
--

CREATE TABLE `prestaciones` (
  `id_prestaciones` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestaciones_caps`
--

CREATE TABLE `prestaciones_caps` (
  `id_prestaciones_caps` int(11) NOT NULL,
  `id_caps` int(11) NOT NULL,
  `id_prestaciones` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesionales`
--

CREATE TABLE `profesionales` (
  `id_profesionales` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesionales_prestaciones`
--

CREATE TABLE `profesionales_prestaciones` (
  `id_profesionales_prestaciones` int(11) NOT NULL,
  `id_profesionales` int(11) NOT NULL,
  `horario_profesionales` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `caps`
--
ALTER TABLE `caps`
  ADD PRIMARY KEY (`id_caps`);

--
-- Indices de la tabla `prestaciones`
--
ALTER TABLE `prestaciones`
  ADD PRIMARY KEY (`id_prestaciones`);

--
-- Indices de la tabla `prestaciones_caps`
--
ALTER TABLE `prestaciones_caps`
  ADD PRIMARY KEY (`id_prestaciones_caps`),
  ADD UNIQUE KEY `id_prestaciones_caps` (`id_prestaciones_caps`),
  ADD UNIQUE KEY `FOREIGN` (`id_caps`),
  ADD UNIQUE KEY `SECUNDARIA` (`id_prestaciones`);

--
-- Indices de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  ADD PRIMARY KEY (`id_profesionales`);

--
-- Indices de la tabla `profesionales_prestaciones`
--
ALTER TABLE `profesionales_prestaciones`
  ADD PRIMARY KEY (`id_profesionales_prestaciones`),
  ADD UNIQUE KEY `FOREIGN PROFESIONALES` (`id_profesionales`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `caps`
--
ALTER TABLE `caps`
  MODIFY `id_caps` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prestaciones`
--
ALTER TABLE `prestaciones`
  MODIFY `id_prestaciones` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prestaciones_caps`
--
ALTER TABLE `prestaciones_caps`
  MODIFY `id_prestaciones_caps` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  MODIFY `id_profesionales` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesionales_prestaciones`
--
ALTER TABLE `profesionales_prestaciones`
  MODIFY `id_profesionales_prestaciones` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
