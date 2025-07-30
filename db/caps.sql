-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-07-2025 a las 05:43:00
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
-- Base de datos: `caps`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campañas`
--

CREATE TABLE `campañas` (
  `id_campañas` int(11) NOT NULL,
  `imagen` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `campañas`
--

INSERT INTO `campañas` (`id_campañas`, `imagen`) VALUES
(1, 'campaña pap.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campañas_caps`
--

CREATE TABLE `campañas_caps` (
  `id_campañas_cap` int(11) NOT NULL,
  `id_caps` int(11) NOT NULL,
  `id_campañas` int(11) NOT NULL,
  `horario` varchar(70) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `requisitos` varchar(90) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `caps`
--

CREATE TABLE `caps` (
  `id_caps` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `coordenadas` varchar(150) NOT NULL,
  `horario` varchar(70) NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `telefono` int(150) NOT NULL,
  `Campaña` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caps`
--

INSERT INTO `caps` (`id_caps`, `nombre`, `descripcion`, `coordenadas`, `horario`, `imagen`, `telefono`, `Campaña`) VALUES
(1, 'Doctor Carlos Fucile', 'Centro de atencion primaria de la salud Carlos Fucile', '-38.57816486446628,-58.72158052883532', '08:00', 'Fucile.jpeg', 111111111, 0),
(2, 'Barrio Sur', 'Centro periferico barrio sur', '-38.56709554734896, -58.74036619782407', '08:00', 'PerifericoBarrioSur.jpeg', 226243818, 0),
(3, 'Centro Prueba', 'prueba', '-38.559336899309834, -58.62806087327264', '08:00', 'richard-sanchez-en-el-club-america-1751983737-hq.w', 2147483647, 0),
(4, 'prueba 2', 'probando 2 jaja', '-38.57816486446628,-58.7215805288353234', '08:00', '', 123, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestaciones`
--

CREATE TABLE `prestaciones` (
  `id_prestaciones` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestaciones`
--

INSERT INTO `prestaciones` (`id_prestaciones`, `nombre`) VALUES
(1, 'Vacunación'),
(2, 'Consultorio general'),
(3, 'Pediatría'),
(4, 'Ginecología'),
(5, 'Kinesiología');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestaciones_caps`
--

CREATE TABLE `prestaciones_caps` (
  `id_prestaciones_caps` int(11) NOT NULL,
  `id_caps` int(11) NOT NULL,
  `id_prestaciones` int(11) NOT NULL,
  `id_profesional` int(11) DEFAULT NULL,
  `horario_profesional` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestaciones_caps`
--

INSERT INTO `prestaciones_caps` (`id_prestaciones_caps`, `id_caps`, `id_prestaciones`, `id_profesional`, `horario_profesional`) VALUES
(27, 3, 2, NULL, NULL),
(30, 4, 1, NULL, NULL),
(31, 4, 2, NULL, NULL),
(34, 1, 2, 2, '08:00-20:00'),
(35, 2, 2, 2, '08:00-20:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesionales`
--

CREATE TABLE `profesionales` (
  `id_profesionales` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesionales`
--

INSERT INTO `profesionales` (`id_profesionales`, `nombre`, `apellido`) VALUES
(1, 'Maria', 'Rodriguez'),
(2, 'Juan', 'Mobilia');

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
-- Indices de la tabla `campañas`
--
ALTER TABLE `campañas`
  ADD PRIMARY KEY (`id_campañas`);

--
-- Indices de la tabla `campañas_caps`
--
ALTER TABLE `campañas_caps`
  ADD PRIMARY KEY (`id_campañas_cap`),
  ADD UNIQUE KEY `id_caps` (`id_caps`),
  ADD UNIQUE KEY `id_campañas` (`id_campañas`);

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
  ADD UNIQUE KEY `unique_caps_prestacion` (`id_caps`,`id_prestaciones`),
  ADD KEY `idx_profesional` (`id_profesional`);

--
-- Indices de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  ADD PRIMARY KEY (`id_profesionales`);

--
-- Indices de la tabla `profesionales_prestaciones`
--
ALTER TABLE `profesionales_prestaciones`
  ADD PRIMARY KEY (`id_profesionales_prestaciones`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `campañas`
--
ALTER TABLE `campañas`
  MODIFY `id_campañas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `campañas_caps`
--
ALTER TABLE `campañas_caps`
  MODIFY `id_campañas_cap` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caps`
--
ALTER TABLE `caps`
  MODIFY `id_caps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `prestaciones`
--
ALTER TABLE `prestaciones`
  MODIFY `id_prestaciones` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `prestaciones_caps`
--
ALTER TABLE `prestaciones_caps`
  MODIFY `id_prestaciones_caps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT de la tabla `profesionales`
--
ALTER TABLE `profesionales`
  MODIFY `id_profesionales` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `profesionales_prestaciones`
--
ALTER TABLE `profesionales_prestaciones`
  MODIFY `id_profesionales_prestaciones` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
