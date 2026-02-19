-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 19 fév. 2026 à 06:17
-- Version du serveur : 8.2.0
-- Version de PHP : 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `l3ipgee`
--

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
CREATE TABLE IF NOT EXISTS `evenement` (
  `e_num` int NOT NULL AUTO_INCREMENT,
  `date_evenement` datetime NOT NULL,
  `adresse_evenement` varchar(100) DEFAULT NULL,
  `titre` varchar(50) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `description_longue` varchar(500) DEFAULT NULL,
  `nb_places_totales` int DEFAULT NULL,
  `nb_places_dispo` int DEFAULT '0',
  `date_derniere_modif` datetime DEFAULT NULL,
  `u_num` int NOT NULL,
  `te_num` int NOT NULL,
  PRIMARY KEY (`e_num`),
  KEY `u_num` (`u_num`),
  KEY `te_num` (`te_num`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

DROP TABLE IF EXISTS `inscription`;
CREATE TABLE IF NOT EXISTS `inscription` (
  `u_num` int NOT NULL,
  `e_num` int NOT NULL,
  `date_inscription` datetime DEFAULT NULL,
  PRIMARY KEY (`u_num`,`e_num`),
  KEY `e_num` (`e_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `inscription`
--

INSERT INTO `inscription` (`u_num`, `e_num`, `date_inscription`) VALUES
(3, 3, '2026-02-18 21:11:48'),
(2, 2, '2026-02-14 09:48:48'),
(3, 1, '2026-02-18 23:33:06');

-- --------------------------------------------------------

--
-- Structure de la table `typeevenement`
--

DROP TABLE IF EXISTS `typeevenement`;
CREATE TABLE IF NOT EXISTS `typeevenement` (
  `te_num` int NOT NULL AUTO_INCREMENT,
  `libelle` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `u_num` int DEFAULT NULL,
  PRIMARY KEY (`te_num`),
  KEY `u_num` (`u_num`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `typeevenement`
--

INSERT INTO `typeevenement` (`te_num`, `libelle`, `u_num`) VALUES
(1, 'Conférence', NULL),
(2, 'Rencontre sportive', NULL),
(3, 'Atelier', NULL),
(4, 'Concert', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `typeutilisateur`
--

DROP TABLE IF EXISTS `typeutilisateur`;
CREATE TABLE IF NOT EXISTS `typeutilisateur` (
  `tu_num` int NOT NULL,
  `libelle` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`tu_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `typeutilisateur`
--

INSERT INTO `typeutilisateur` (`tu_num`, `libelle`) VALUES
(0, 'Étudiant'),
(1, 'Organisateur'),
(2, 'Administrateur');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `u_num` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) DEFAULT NULL,
  `prenom` varchar(30) DEFAULT NULL,
  `adresse_email` varchar(50) NOT NULL,
  `hash_mdp` char(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `tu_num` int NOT NULL,
  PRIMARY KEY (`u_num`),
  UNIQUE KEY `adresse_email` (`adresse_email`),
  KEY `tu_num` (`tu_num`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
