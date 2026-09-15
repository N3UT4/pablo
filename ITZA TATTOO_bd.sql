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
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER trg_confirmar_cita_after_abono
AFTER INSERT ON abono
FOR EACH ROW
BEGIN
    -- Cambiar estado de la cita a 'confirmada'
    UPDATE cita 
    SET estado = 'confirmada', 
        abono_inicial = (SELECT SUM(monto) FROM abono WHERE id_cita = NEW.id_cita)
    WHERE id_cita = NEW.id_cita;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'Pablo Andrés Neuta','admin@itza.com','hashed_password_123','administrador','3001234567','2026-06-25 01:10:10'),(2,'Tatuador 1','tatuador1@itza.com','hashed_password_123','tatuador','3002345678','2026-06-25 01:10:10'),(3,'Cliente 1','cliente1@itza.com','hashed_password_123','cliente','3101112233','2026-06-25 01:10:10'),(4,'Cliente 2','cliente2@itza.com','hashed_password_123','cliente','3102223344','2026-06-25 01:10:10'),(5,'Cliente 3','cliente3@itza.com','hashed_password_123','cliente','3103334455','2026-06-25 01:10:10');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'itza_tattoo'
--

--
-- Dumping routines for database 'itza_tattoo'
--
/*!50003 DROP PROCEDURE IF EXISTS `SP_CALCULAR_SALDO` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CALCULAR_SALDO`(
    IN p_id_cita INT,
    OUT p_saldo_pendiente DECIMAL(10,2)
)
BEGIN
    DECLARE v_total DECIMAL(10,2);
    DECLARE v_abono_total DECIMAL(10,2);
    DECLARE v_saldo DECIMAL(10,2);
    
    SELECT total_servicio INTO v_total
    FROM cita WHERE id_cita = p_id_cita;
    
    SELECT IFNULL(SUM(monto), 0) INTO v_abono_total
    FROM abono WHERE id_cita = p_id_cita;
    
    SET v_saldo = v_total - v_abono_total;
    SET p_saldo_pendiente = v_saldo;
    
    UPDATE pago SET saldo_pendiente = v_saldo
    WHERE id_cita = p_id_cita;
    
    IF v_saldo <= 0 THEN
        UPDATE cita SET estado = 'completada' WHERE id_cita = p_id_cita;
        UPDATE pago SET estado = 'pagado' WHERE id_cita = p_id_cita;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_CAMBIAR_ESTADO_CITA` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CAMBIAR_ESTADO_CITA`(
    IN p_id_cita INT,
    IN p_nuevo_estado VARCHAR(20)
)
BEGIN
    DECLARE v_estado_actual VARCHAR(20);
    
    SELECT estado INTO v_estado_actual
    FROM cita WHERE id_cita = p_id_cita;
    
    IF p_nuevo_estado = 'cancelada' AND v_estado_actual = 'completada' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se puede cancelar una cita ya completada';
    ELSE
        UPDATE cita SET estado = p_nuevo_estado
        WHERE id_cita = p_id_cita;
        
        SELECT CONCAT('Cita #', p_id_cita, ' cambiada a ', p_nuevo_estado) AS Mensaje;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_CONSULTAR_CITAS_CLIENTE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_CONSULTAR_CITAS_CLIENTE`(
    IN p_id_cliente INT
)
BEGIN
    SELECT 
        c.id_cita,
        u.nombre_completo AS Tatuador,
        c.fecha_hora,
        c.estado,
        c.total_servicio,
        c.abono_inicial,
        (c.total_servicio - c.abono_inicial) AS SaldoPendiente
    FROM cita c
    JOIN tatuador t ON c.id_tatuador = t.id_tatuador
    JOIN usuario u ON t.id_usuario = u.id_usuario
    WHERE c.id_cliente = p_id_cliente
    ORDER BY c.fecha_hora DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_REGISTRAR_ABONO` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_ABONO`(
    IN p_id_cita INT,
    IN p_monto DECIMAL(10,2),
    IN p_metodo_pago VARCHAR(20),
    IN p_comprobante VARCHAR(255),
    IN p_observaciones TEXT
)
BEGIN
    INSERT INTO abono (id_cita, monto, metodo_pago, comprobante, observaciones)
    VALUES (p_id_cita, p_monto, p_metodo_pago, p_comprobante, p_observaciones);
    
    UPDATE cita 
    SET abono_inicial = abono_inicial + p_monto
    WHERE id_cita = p_id_cita;
    
    UPDATE cita 
    SET estado = 'confirmada' 
    WHERE id_cita = p_id_cita AND estado = 'agendada';
    
    CALL SP_CALCULAR_SALDO(p_id_cita, @saldo);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `SP_REGISTRAR_PAGO_FINAL` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `SP_REGISTRAR_PAGO_FINAL`(
    IN p_id_cita INT,
    IN p_monto DECIMAL(10,2),
    IN p_metodo_pago VARCHAR(20)
)
BEGIN
    DECLARE v_saldo DECIMAL(10,2);
    
    SELECT saldo_pendiente INTO v_saldo
    FROM pago WHERE id_cita = p_id_cita;
    
    IF p_monto >= v_saldo THEN
        UPDATE pago 
        SET estado = 'pagado',
            saldo_pendiente = 0,
            metodo_pago = p_metodo_pago,
            fecha_pago = NOW()
        WHERE id_cita = p_id_cita;
        
        UPDATE cita SET estado = 'completada'
        WHERE id_cita = p_id_cita;
        
        SELECT 'Pago registrado exitosamente' AS Mensaje;
    ELSE
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El monto no cubre el saldo pendiente';
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-26 23:20:44
