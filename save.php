<?php
try {
    // Vérifiez que les données sont envoyées via POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?msg=Méthode non autorisée");
        exit;
    }

    // Vérifiez si toutes les données nécessaires sont présentes
    if (empty($_POST['idd']) || empty($_POST['email']) || empty($_POST['role'])) {
        header("Location: index.php?msg=Tous les champs sont obligatoires !");
        exit;
    }

    // Récupération des données
    $id = (int)$_POST['idd'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Si un mot de passe est fourni, on le hache
    if (!empty($pass)) {
        $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
        // Requête SQL pour la mise à jour avec le mot de passe haché
        $sql = "UPDATE users SET email = :email, password = :pass, role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        // Liaison des valeurs
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':pass', $hashedPass, PDO::PARAM_STR);  // Utilisation du mot de passe haché
    } else {
        // Si aucun mot de passe n'est fourni, on ne met pas à jour le mot de passe
        $sql = "UPDATE users SET email = :email, role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        // Liaison des valeurs
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    }

    // Liaison du rôle et de l'ID
    $stmt->bindValue(':role', $role, PDO::PARAM_STR);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);

    // Exécution de la requête
    $stmt->execute();

    // Redirection vers index.php avec un message de succès
    header("Location: index.php?msg=Utilisateur mis à jour avec succès !");
    exit;
} catch (PDOException $e) {
    // Gérer les erreurs SQL
    header("Location: index.php?msg=Erreur lors de la mise à jour !");
    error_log($e->getMessage());
    exit;
}
