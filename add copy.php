<?php
try {
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $role = $_POST['role'];

    
    $pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');

    
    $sql = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $email, PDO::PARAM_STR);
    $stmt->bindValue(2, $pass, PDO::PARAM_STR);
    $stmt->bindValue(3, $role, PDO::PARAM_STR);
    $stmt->execute();

    
    header("Location: index.php?msg=Utilisateur ajouté avec succès");
} catch (PDOException $e) {
    header("Location: index.php?msg=Erreur lors de l'ajout de l'utilisateur");
}
?>


