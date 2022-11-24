CREATE DATABASE  IF NOT EXISTS `pethero` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `pethero`;
-- MySQL dump 10.13  Distrib 8.0.31, for Win64 (x86_64)
--
-- Host: localhost    Database: pethero
-- ------------------------------------------------------
-- Server version	8.0.31

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
-- Table structure for table `guardianes`
--

DROP TABLE IF EXISTS `guardianes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `guardianes` (
  `idGuardian` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varbinary(150) NOT NULL,
  `tipo` tinyint NOT NULL DEFAULT '2',
  `rutaFoto` varchar(100) NOT NULL,
  `alta` tinyint(1) NOT NULL DEFAULT '1',
  `reputacion` float DEFAULT '2.5',
  `precioXDia` float DEFAULT '0',
  `fk_idDireccion` int DEFAULT NULL,
  `fk_idDisponibilidad` int DEFAULT NULL,
  `fk_idTamanioMascota` int DEFAULT NULL,
  PRIMARY KEY (`idGuardian`),
  KEY `fk_idDireccion_idx` (`fk_idDireccion`),
  KEY `fk_idDisponibilidad_idx` (`fk_idDisponibilidad`),
  KEY `fk_idTamanioMascota_idx` (`fk_idTamanioMascota`),
  CONSTRAINT `fk_idDireccion` FOREIGN KEY (`fk_idDireccion`) REFERENCES `direcciones` (`idDireccion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_idDisponibilidad` FOREIGN KEY (`fk_idDisponibilidad`) REFERENCES `disponibilidades` (`idDisponibilidad`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_idTamanioMascota` FOREIGN KEY (`fk_idTamanioMascota`) REFERENCES `tamaniomascota` (`idTamanioMascota`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `guardianes`
--

LOCK TABLES `guardianes` WRITE;
/*!40000 ALTER TABLE `guardianes` DISABLE KEYS */;
/*!40000 ALTER TABLE `guardianes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2022-11-22 18:50:10
