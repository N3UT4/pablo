CREATE DATABASE  IF NOT EXISTS `itza_tattoo` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `itza_tattoo`;
-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: itza_tattoo
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `abono`
--

DROP TABLE IF EXISTS `abono`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `abono` (
  `id_abono` int(11) NOT NULL AUTO_INCREMENT,
  `id_cita` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `fecha_abono` datetime DEFAULT current_timestamp(),
  `metodo_pago` enum('efectivo','transferencia','nequi','tarjeta') NOT NULL,
  `comprobante` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_abono`),
  KEY `id_cita` (`id_cita`),
  CONSTRAINT `abono_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `cita` (`id_cita`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abono`
--

LOCK TABLES `abono` WRITE;
/*!40000 ALTER TABLE `abono` DISABLE KEYS */;
INSERT INTO `abono` VALUES (1,1,200000.00,'2026-06-25 01:10:10','nequi','comp_001.png','Abono inicial'),(2,2,150000.00,'2026-06-25 01:10:10','transferencia','comp_002.png','Abono inicial'),(3,3,100000.00,'2026-06-25 01:10:10','efectivo',NULL,'Abono inicial');
/*!40000 ALTER TABLE `abono` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cita`
--

DROP TABLE IF EXISTS `cita`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cita` (
  `id_cita` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `id_tatuador` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `duracion_minutos` int(11) DEFAULT 120,
  `estado` enum('agendada','confirmada','en_curso','completada','cancelada') DEFAULT 'agendada',
  `total_servicio` decimal(10,2) NOT NULL,
  `abono_inicial` decimal(10,2) DEFAULT 0.00,
  `notas` text DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_cita`),
  KEY `id_cliente` (`id_cliente`),
  KEY `id_tatuador` (`id_tatuador`),
  CONSTRAINT `cita_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE,
  CONSTRAINT `cita_ibfk_2` FOREIGN KEY (`id_tatuador`) REFERENCES `tatuador` (`id_tatuador`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cita`
--

LOCK TABLES `cita` WRITE;
/*!40000 ALTER TABLE `cita` DISABLE KEYS */;
INSERT INTO `cita` VALUES (1,1,1,'2026-06-20 10:00:00',120,'confirmada',500000.00,200000.00,'Tatuaje de dragón en brazo','2026-06-25 01:10:10'),(2,2,1,'2026-06-20 14:00:00',150,'confirmada',350000.00,150000.00,'Flor de loto en espalda','2026-06-25 01:10:10'),(3,3,1,'2026-06-21 16:00:00',90,'confirmada',250000.00,100000.00,'Líneas geométricas en antebrazo','2026-06-25 01:10:10');
/*!40000 ALTER TABLE `cita` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `documento` varchar(20) NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  UNIQUE KEY `documento` (`documento`),
  CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,3,'12345678','1990-05-15'),(2,4,'87654321','1985-08-20'),(3,5,'11223344','2008-03-10');
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pago`
--

DROP TABLE IF EXISTS `pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pago` (
  `id_pago` int(11) NOT NULL AUTO_INCREMENT,
  `id_cita` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `saldo_pendiente` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` enum('efectivo','transferencia','nequi','tarjeta') NOT NULL,
  `estado` enum('pendiente','pagado','cancelado') DEFAULT 'pendiente',
  `fecha_pago` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pago`),
  KEY `id_cita` (`id_cita`),
  CONSTRAINT `pago_ibfk_1` FOREIGN KEY (`id_cita`) REFERENCES `cita` (`id_cita`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pago`
--

LOCK TABLES `pago` WRITE;
/*!40000 ALTER TABLE `pago` DISABLE KEYS */;
INSERT INTO `pago` VALUES (1,1,500000.00,300000.00,'efectivo','pendiente','2026-06-25 01:10:10');
/*!40000 ALTER TABLE `pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tatuador`
--

DROP TABLE IF EXISTS `tatuador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tatuador` (
  `id_tatuador` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `especialidad` varchar(50) DEFAULT NULL,
  `horario_trabajo` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_tatuador`),
  UNIQUE KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `tatuador_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tatuador`
--

LOCK TABLES `tatuador` WRITE;
/*!40000 ALTER TABLE `tatuador` DISABLE KEYS */;
INSERT INTO `tatuador` VALUES (1,2,'Realista, Tradicional','Lun-Vie 9am-6pm');
/*!40000 ALTER TABLE `tatuador` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `rol` enum('administrador','tatuador','cliente') NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Pablo Andrés Neuta','admin@itza.com','hashed_password_123','administrador','3001234567','2026-06-25 01:10:10'),(2,'Tatuador 1','tatuador1@itza.com','hashed_password_123','tatuador','3002345678','2026-06-25 01:10:10'),(3,'Cliente 1','cliente1@itza.com','hashed_password_123','cliente','3101112233','2026-06-25 01:10:10'),(4,'Cliente 2','cliente2@itza.com','hashed_password_123','cliente','3102223344','2026-06-25 01:10:10'),(5,'Cliente 3','cliente3@itza.com','hashed_password_123','cliente','3103334455','2026-06-25 01:10:10');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-25  4:39:35
