-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 04 août 2022 à 13:48
-- Version du serveur : 5.7.36
-- Version de PHP : 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `oui-sortie`
--

-- --------------------------------------------------------

--
-- Structure de la table `participant`
--

DROP TABLE IF EXISTS `participant`;
CREATE TABLE IF NOT EXISTS `participant` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `est_rattache_a_id` int(11) DEFAULT NULL,
  `nom` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `actif` tinyint(1) NOT NULL,
  `pseudo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_D79F6B11E7927C74` (`email`),
  KEY `IDX_D79F6B11C8761AC1` (`est_rattache_a_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `participant`
--

INSERT INTO `participant` (`id`, `email`, `roles`, `password`, `est_rattache_a_id`, `nom`, `prenom`, `telephone`, `actif`, `pseudo`) VALUES
(1, 'user@user.user', '[\"ROLE_USER\"]', '$2y$13$v8JAvNhTGhRfcQoL3UeDAucQFRfbSqX.l/Y/8rzuamwiUWgm91qju', NULL, 'User', 'USER', '0504030201', 1, 'user'),
(2, 'admin@admin.admin', '[\"ROLE_ADMIN\"]', '$2y$13$7B4E8GwgIIJJXzm2k/8le.oWoRLTsaYtQKL/4Bn7xC1vcVLh31Sm6', NULL, 'Admin', 'ADMIN', '0102030405', 1, 'admin');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `participant`
--
ALTER TABLE `participant`
  ADD CONSTRAINT `FK_D79F6B11C8761AC1` FOREIGN KEY (`est_rattache_a_id`) REFERENCES `campus` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
