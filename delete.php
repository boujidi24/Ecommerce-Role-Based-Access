<?php
try {
    // Récupération de l'ID depuis l'URL avec une vérification de sécurité
    if (!isset($_GET['idd']) || !is_numeric($_GET['idd'])) {
        header("location: index.php?msg=ID invalide");
        exit;
    }
    $id = $_GET['idd'];

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Préparer et exécuter la requête SQL pour supprimer l'utilisateur
    $sql = "DELETE FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Vérifier si la suppression a bien eu lieu
    if ($stmt->rowCount() > 0) {
        header("location: index.php?msg=Utilisateur supprimé avec succès");
    } else {
        header("location: index.php?msg=Aucun utilisateur trouvé pour cet ID");
    }
} catch (PDOException $e) {
    // Afficher un message d'erreur en cas de problème avec la base de données
    header("location: index.php?msg=Erreur : " . $e->getMessage());
}
