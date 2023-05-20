<?php
namespace App\Service;

use PDO;
use PDOException;

class ResetDataService
{
    public function resetTablesAndIncrements() {

        // Récupérez les variables d'environnement pour la base de données
        $dbhost = $_SERVER['DB_HOST'];
        $dbname = $_SERVER['DB_NAME'];
        $dbUser = $_SERVER['DB_USER'];
        $dbPass = $_SERVER['DB_PASS'];

        try {
            // Connexion à la bdd:
            $pdo = new PDO("mysql:host=$dbhost; dbname=$dbname", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Suppression de toutes les données de chacunes des tables
            $sqlO = 'DELETE FROM `order`';
            $stm = $pdo->prepare($sqlO);
            $stm->execute();

            $sqlP = 'DELETE FROM `product`';
            $stm = $pdo->prepare($sqlP);
            $stm->execute();

            /*$sqlC = 'DELETE FROM `customers`';
            $stm = $pdo->prepare($sqlC);
            $stm->execute();*/

            // Remise à zéro des incrémentation d'id
            $sqlO = 'ALTER TABLE `order` AUTO_INCREMENT = 0';
            $stm = $pdo->prepare($sqlO);
            $stm->execute();

            $sqlP = 'ALTER TABLE `product` AUTO_INCREMENT = 0';
            $stm = $pdo->prepare($sqlP);
            $stm->execute();

            /*$sqlC = 'ALTER TABLE `customers` AUTO_INCREMENT = 0';
            $stm = $pdo->prepare($sqlC);
            $stm->execute();*/

            // Le reste sera fait dans le controller directement (il utilise l'autre service)
            // Et on épargne la table Users :o)

        } catch(PDOException $e) {
            die("Connexion MySQL local failed: " . $e->getMessage());
        }
    }

    public function resetUsersTablesAndIncrements() {

        // Récupérez les variables d'environnement pour la base de données
        $dbhost = $_SERVER['DB_HOST'];
        $dbname = $_SERVER['DB_NAME'];
        $dbUser = $_SERVER['DB_USER'];
        $dbPass = $_SERVER['DB_PASS'];

        try {
            // Connexion à la bdd:
            $pdo = new PDO("mysql:host=$dbhost; dbname=$dbname", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Suppression de toutes les données de la table des utilisateurs (revendeurs et clients)
            $sqlU = 'DELETE FROM `user`';
            $stm = $pdo->prepare($sqlU);
            $stm->execute();

            // Remise à zéro des incrémentation d'id
            $sqlU = 'ALTER TABLE `user` AUTO_INCREMENT = 0';
            $stm = $pdo->prepare($sqlU);
            $stm->execute();

        } catch(PDOException $e) {
            die("Connexion MySQL local failed: " . $e->getMessage());
        }
    }
}