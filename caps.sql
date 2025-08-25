-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 25-08-2025 a las 23:15:42
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
  `descripcion` varchar(100) NOT NULL,
  `coordenadas` varchar(150) NOT NULL,
  `horario` time NOT NULL,
  `imagen` varchar(50) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `Campaña` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `caps`
--

INSERT INTO `caps` (`id_caps`, `nombre`, `descripcion`, `coordenadas`, `horario`, `imagen`, `telefono`, `Campaña`) VALUES
(2, 'Carlos Fucile', 'Centro de atencion primaria de la salud Carlos Fuc', '-38.57799736065549, -58.72151405994533', '08:00:00', '', '2262', 0),
(3, 'Puerto', 'Centro de atencion primaria de la salud Puerto', '-38.56749403705967, -58.71781523280543', '08:00:00', '', '43', 3),
(4, 'Villa Zabala', 'Postas de atencion Villa zabala', '-38.60235360352555, -58.79659156585377', '08:00:00', '', '2262', 0),
(5, 'Sur', 'Centro de atencion primaria de la salud Sur', '-38.56680163447325, -58.74087074815009', '08:00:00', '', '43', 0),
(6, 'Sudoeste', 'Centro de atencion primaria de la salud Sudoeste', '-38.562849176165095, -58.749767405822645', '08:00:00', '', '42', 0),
(7, 'Flores', 'Centro de atencion primaria de la salud Flores', '-38.553284435141656, -58.75988214815095', '08:00:00', '', '43', 0),
(8, 'Norte', 'Centro de atencion primaria de la salud Norte', '-38.54456411641815, -58.74102121931522', '08:00:00', '', '42', 0),
(9, '9 de julio', 'Centro de atencion primaria de la salud 9 de julio', '-38.55364441376124, -58.75754722863928', '08:00:00', '', '43', 0),
(10, 'San martin', 'Centro de atencion primaria de la salud San martin', '-38.559106995282086, -58.762802803969535', '08:00:00', '', '42', 0),
(11, 'CIC', 'Centro de atencion primaria de la salud CIC', '-38.53773723091765, -58.75119215952562', '08:00:00', '', '45', 0),
(12, 'Seis esquinas', 'Centro de atencion primaria de la salud Seis esqui', '-38.56548393529879, -58.69711879301142', '08:00:00', '', '45', 0),
(13, 'Fomento', 'Centro de atencion primaria de la salud Fomento', '-38.55983476631371, -58.71089770396959', '08:00:00', '', '45', 0),
(14, 'Estacion quequen', 'Centro de atencion primaria de la salud Estacion q', '-38.53174225277787, -58.705440390479865', '08:00:00', '', '45', 0);

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
-- AUTO_INCREMENT de la tabla `campañas`
--
ALTER TABLE `campañas`
  MODIFY `id_campañas` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `campañas_caps`
--
ALTER TABLE `campañas_caps`
  MODIFY `id_campañas_cap` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `caps`
--
ALTER TABLE `caps`
  MODIFY `id_caps` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
