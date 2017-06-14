-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `acte`;
CREATE TABLE `acte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dia` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `hores` float DEFAULT NULL,
  `titol` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `descripcio` text COLLATE utf8_spanish_ci,
  `lloc` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `ubicacio` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `json` text COLLATE utf8_spanish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `pagina`;
CREATE TABLE `pagina` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `slug` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `categoria` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `tags` varchar(200) COLLATE utf8_spanish_ci DEFAULT NULL,
  `idioma` char(2) COLLATE utf8_spanish_ci DEFAULT NULL,
  `autor` int(11) DEFAULT NULL,
  `titol` varchar(150) COLLATE utf8_spanish_ci NOT NULL,
  `contingut` text COLLATE utf8_spanish_ci,
  `imatge` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `json` text COLLATE utf8_spanish_ci,
  `alta` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `modificacio` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `publicacio` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  `baixa` varchar(12) COLLATE utf8_spanish_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `pagina` (`id`, `slug`, `categoria`, `tags`, `idioma`, `autor`, `titol`, `contingut`, `imatge`, `json`, `alta`, `modificacio`, `publicacio`, `baixa`) VALUES
(1,	'test1',	'noticia',	NULL,	'va',	1,	'noticia test 1',	'exemple de contingut',	NULL,	NULL,	'20170524',	'20170525',	NULL,	'');

DROP TABLE IF EXISTS `partida`;
CREATE TABLE `partida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data` varchar(12) COLLATE utf8_spanish_ci NOT NULL,
  `lloc` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `ubicacio` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `titol` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `json` text COLLATE utf8_spanish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `producte`;
CREATE TABLE `producte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codi` varchar(20) COLLATE utf8_spanish_ci DEFAULT NULL,
  `nom` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `descripcio` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `preu` float NOT NULL,
  `stock` int(11) DEFAULT NULL,
  `json` text COLLATE utf8_spanish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `pwd` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_spanish_ci DEFAULT NULL,
  `dni` varchar(10) COLLATE utf8_spanish_ci DEFAULT NULL,
  `json` text COLLATE utf8_spanish_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

INSERT INTO `usuario` (`id`, `nom`, `pwd`, `email`, `dni`, `json`) VALUES
(1,	'Àlfons Sánchez',	'37e714943a89182fce6e1b038fc264d8',	NULL,	'73775662B',	NULL);

-- 2017-05-30 19:34:11