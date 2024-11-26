<?php
// Configuration des informations de connexion à la base de données
$host = 'localhost'; // Adresse du serveur (ici, localhost)
$dbname = 'tp_users'; // Nom de votre base de données
$username = 'root'; // Nom d'utilisateur MySQL (par défaut, root)
$password = ''; // Mot de passe MySQL (par défaut, vide sur XAMPP)




try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Affiche l'erreur si la connexion échoue
    die('Erreur de connexion : ' . $e->getMessage());
}



?>

