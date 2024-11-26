<?php
try {
    // Récupération des données envoyées par le formulaire
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');

    // Hachage du mot de passe
    $hashedPass = password_hash($pass, PASSWORD_DEFAULT);

    // Préparation de la requête SQL
    $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $email, PDO::PARAM_STR);
    $stmt->bindValue(2, $hashedPass, PDO::PARAM_STR); // Utilisation du mot de passe haché
    $stmt->bindValue(3, $role, PDO::PARAM_STR);

    // Exécution de la requête
    $stmt->execute();

    // Redirection avec un message de succès
    header("Location: index.php?msg=Utilisateur ajouté avec succès");
} catch (PDOException $e) {
    // Redirection avec un message d'erreur
    header("Location: index.php?msg=Erreur lors de l'ajout de l'utilisateur");
}
?>
