CREATE DATABASE  IF NOT EXISTS `registro_horas` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `registro_horas`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: registro_horas
-- ------------------------------------------------------
-- Server version	8.4.3

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
-- Table structure for table `atividades`
--

DROP TABLE IF EXISTS `atividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `atividades` (
  `atividade_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `descricao` text,
  `inicio_atividade` datetime NOT NULL,
  `fim_atividade` datetime DEFAULT NULL,
  `turno_fk` bigint unsigned NOT NULL,
  PRIMARY KEY (`atividade_id`),
  KEY `atividades_turnos_FK` (`turno_fk`),
  CONSTRAINT `atividades_turnos_FK` FOREIGN KEY (`turno_fk`) REFERENCES `turnos` (`turno_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `cliente_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cnpj` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `cliente` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL,
  `data_cadastro` date NOT NULL,
  PRIMARY KEY (`cliente_id`),
  UNIQUE KEY `clientes_unique` (`cnpj`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `projetos`
--

DROP TABLE IF EXISTS `projetos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `projetos` (
  `projeto_id` int unsigned NOT NULL AUTO_INCREMENT,
  `projeto` varchar(100) NOT NULL,
  `cliente_fk` bigint unsigned NOT NULL,
  PRIMARY KEY (`projeto_id`),
  KEY `projetos_projeto_IDX` (`projeto`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tipo_usuario`
--

DROP TABLE IF EXISTS `tipo_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_usuario` (
  `tipo_id` tinyint NOT NULL AUTO_INCREMENT,
  `tipo_nome` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`tipo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `turnos`
--

DROP TABLE IF EXISTS `turnos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `turnos` (
  `turno_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cliente_fk` bigint unsigned NOT NULL,
  `user_fk` bigint unsigned NOT NULL,
  `projeto_fk` int unsigned NOT NULL,
  `inicio_turno` datetime NOT NULL,
  `fim_turno` datetime DEFAULT NULL,
  `aberto` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`turno_id`),
  KEY `turnos_clientes_FK` (`cliente_fk`),
  KEY `turnos_usuarios_FK` (`user_fk`),
  CONSTRAINT `turnos_clientes_FK` FOREIGN KEY (`cliente_fk`) REFERENCES `clientes` (`cliente_id`),
  CONSTRAINT `turnos_usuarios_FK` FOREIGN KEY (`user_fk`) REFERENCES `usuarios` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `user_id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) NOT NULL,
  `user_nome` varchar(255) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT '1',
  `data_registro` datetime NOT NULL,
  `tipo_usuario_fk` tinyint NOT NULL DEFAULT '2',
  `cliente_fk` bigint unsigned NOT NULL,
  `session_token` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `usuarios_unique` (`usuario`,`cliente_fk`),
  KEY `usuarios_tipo_usuario_FK` (`tipo_usuario_fk`),
  CONSTRAINT `usuarios_tipo_usuario_FK` FOREIGN KEY (`tipo_usuario_fk`) REFERENCES `tipo_usuario` (`tipo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-29 19:06:14
