-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 29-03-2020 a las 07:30:22
-- Versión del servidor: 8.0.18
-- Versión de PHP: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `lisproject_db`
--
CREATE DATABASE IF NOT EXISTS `lisproject_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `lisproject_db`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriasregistros`
--

DROP TABLE IF EXISTS `categoriasregistros`;
CREATE TABLE IF NOT EXISTS `categoriasregistros` (
  `cRID` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`cRID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categoriasregistros`
--

INSERT INTO `categoriasregistros` (`cRID`, `descripcion`) VALUES
(1, 'Reserva'),
(2, 'Prestamo'),
(3, 'Mantenimiento'),
(4, 'Liberación');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoriasusuarios`
--

DROP TABLE IF EXISTS `categoriasusuarios`;
CREATE TABLE IF NOT EXISTS `categoriasusuarios` (
  `cUID` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cUID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categoriasusuarios`
--

INSERT INTO `categoriasusuarios` (`cUID`, `descripcion`) VALUES
(1, 'Bibliotecario'),
(2, 'Estudiante'),
(3, 'Docente'),
(4, 'Administrativo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elementos`
--

DROP TABLE IF EXISTS `elementos`;
CREATE TABLE IF NOT EXISTS `elementos` (
  `elementoID` varchar(50) NOT NULL,
  `descripcion` varchar(50) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `limiteDeUso` time NOT NULL,
  `ubicacion` varchar(50) NOT NULL,
  `estadoID` int(11) DEFAULT NULL,
  PRIMARY KEY (`elementoID`),
  KEY `fk_elementos_estados` (`estadoID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `elementos`
--

INSERT INTO `elementos` (`elementoID`, `descripcion`, `capacidad`, `limiteDeUso`, `ubicacion`, `estadoID`) VALUES
('c1P1', 'Cubículo inclusivo', 2, '02:00:00', 'Primera planta - Edificio VIPE', 1),
('c2P1', 'Cubículo 2', 2, '02:00:00', 'Primera planta - Edificio VIPE', 1),
('c3P1', 'Cubículo 3', 2, '02:00:00', 'Primera planta - Edificio VIPE', 1),
('c4P1', 'Cubículo 4', 2, '02:00:00', 'Primera planta - Edificio VIPE', 1),
('c5P1', 'Cubículo 5', 2, '02:00:00', 'Primera planta - Edificio VIPE', 1),
('c1P2', 'Cubículo 1', 5, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('c2P2', 'Cubículo 2', 5, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('c3P2', 'Cubículo 3', 5, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('c4P2', 'Cubículo 4', 6, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('c5P2', 'Cubículo 5', 6, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('c6P2', 'Cubículo 6', 6, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('c7P2', 'Cubículo 7', 8, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('sRP2', 'Sala de reuniones', 20, '02:00:00', 'Segunda planta - Edificio VIPE', 1),
('sCP4', 'Sala de conferencias', 40, '02:00:00', 'Cuarta planta - Edificio ex-biblioteca', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

DROP TABLE IF EXISTS `estados`;
CREATE TABLE IF NOT EXISTS `estados` (
  `estadoID` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`estadoID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`estadoID`, `descripcion`) VALUES
(1, 'DISPONIBLE'),
(2, 'RESERVADO'),
(3, 'INHABILITADO'),
(4, 'EN USO'),
(5, 'EN ESPERA DE AUTORIZACIÓN');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registros`
--

DROP TABLE IF EXISTS `registros`;
CREATE TABLE IF NOT EXISTS `registros` (
  `registroID` int(11) NOT NULL AUTO_INCREMENT,
  `carnetUsuario` varchar(10) NOT NULL,
  `fecha` date NOT NULL,
  `entrada` time NOT NULL,
  `salida` time NOT NULL,
  `autorizacion` varchar(10) NOT NULL,
  `cRID` int(11) DEFAULT NULL,
  `elementoID` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`registroID`),
  KEY `fk_registros_usuarios` (`carnetUsuario`),
  KEY `fk_registros_categoriasregistros` (`cRID`),
  KEY `fk_registros_elementos` (`elementoID`)
) ENGINE=MyISAM AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes`
--

DROP TABLE IF EXISTS `solicitudes`;
CREATE TABLE IF NOT EXISTS `solicitudes` (
  `solicitudID` int(11) NOT NULL AUTO_INCREMENT,
  `carnet` varchar(10) NOT NULL,
  `fecha` date NOT NULL,
  `entrada` time NOT NULL,
  `salida` time NOT NULL,
  `elementoID` varchar(50) NOT NULL,
  `cRID` int(11) NOT NULL,
  PRIMARY KEY (`solicitudID`),
  KEY `fk_reservas_usuarios` (`carnet`),
  KEY `fk_reservas_elementos` (`elementoID`),
  KEY `fk_reservas_categoriasRegistros` (`cRID`)
) ENGINE=MyISAM AUTO_INCREMENT=149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `carnet` varchar(10) NOT NULL,
  `nombres` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `clave` char(32) NOT NULL,
  `cUID` int(11) DEFAULT NULL,
  PRIMARY KEY (`carnet`),
  KEY `fk_usuarios_categorias` (`cUID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`carnet`, `nombres`, `apellidos`, `clave`, `cUID`) VALUES
('B01', 'Bibliotecario', 'Master', 'eb0a191797624dd3a48fa681d3061212', 4),
('MN170344', 'Eduardo Arturo', 'Monterrosa Nave', '202cb962ac59075b964b07152d234b70', 2),
('OR180204', 'Gabriela Lissette', 'Ortiz Rojas', '250cf8b51c773f3f8dc8b4be867a9a02', 2),
('HP150470', 'Manuel Alejandro', 'Hurtado Pineda', '96917805fd060e3766a9a1b834639d35', 2),
('GA162760', 'Benjamín Eleázar', 'Gómez Alfaro', '5d9f71b71b207b9e665820c0dce67bdb', 2),
('HP210459', 'Victor Manuel', 'Hurtado Pineda', 'Holi', 2),
('CM160117', 'Andrés de Jesús', 'Chapetón Mata', '231badb19b93e44f47da1bd64a8147f2', 2);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
