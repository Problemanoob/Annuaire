-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 11 jan. 2024 à 12:51
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
-- Base de données : `annuaire`
--

-- --------------------------------------------------------

--
-- Structure de la table `association`
--

DROP TABLE IF EXISTS `association`;
CREATE TABLE IF NOT EXISTS `association` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `acronyme` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `num_tel` varchar(14) COLLATE utf8mb4_general_ci NOT NULL,
  `num_fax` varchar(14) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `site` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `association`
--

INSERT INTO `association` (`id`, `nom`, `acronyme`, `num_tel`, `num_fax`, `email`, `site`) VALUES
(1, 'Agence Départementale d\'Information sur le Logement', 'ADIL 32', '05 81 32 35 05', '05 81 32 35 09', 'cgosserez@adil32.org', 'http://www.adil32.org'),
(2, 'Association Départementale pour le Développement des Arts', 'ADDA 32', '05 62 67 47 47', '05 62 67 47 50', 'addagers@cg32.fr', 'http://www.addagers.fr'),
(3, 'Comité Départemental du Tourisme et des Loisirs', 'CDTL 32', '05 62 05 95 95', '05 62 05 02 16', 'info@tourisme-gers.com', 'http://www.tourisme-gers.com'),
(4, 'Comité des Oeuves Sociales', 'COS', '05 62 67 44 00', '05 62 67 44 01', 'cos@cg32.fr', 'http://cos'),
(5, 'Foyer Ludovic Lapeyrère', 'FLL', '05 62 63 19 44', '/', '/', '/'),
(6, 'GIP GERS SOLIDAIRE', 'GIPGS', '/', '/', '/', 'https://twitter.com/GersSolidaire'),
(7, 'Maison Départementale de l\'Enfance et de la Famille', 'MDEF', '05 62 63 37 33', '05 62 63 06 81', '/', '/'),
(8, 'Maison Départementale des Personnes Handicapées du Gers', 'MDPH 32', '05 62 61 76 76', '05 62 61 76 67', 'mdph32@mdph32.fr', 'https://mdph32.gers.fr/'),
(9, 'Oxygers', '/', '05 62 98 66 34', '05 62 40 10 97', 'contact@oxygers.asso.fr', 'http://www.oxygers.asso.fr'),
(10, 'Paierie Départementale', '/', '05 62 05 63 03', '/', '/', '/'),
(11, 'Service Départemental d\'Incendie et de Secours', 'SDIS', '05 42 54 12 00', '05 42 54 12 07', 'secretariat.prevention@sdis32.fr', 'http://www.sdis32.fr'),
(12, 'Syndicat Mixte Gers Numérique', '/', '05 31 00 46 90', '/', 'contact@gersnumerique.fr', '/');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;