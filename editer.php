<?php
// Récupération de l'ID de l'utilisateur
if (!isset($_GET['idd']) || !is_numeric($_GET['idd'])) {
    header("Location: /index.php?msg=ID invalide");
    exit;
}

$id = (int)$_GET['idd'];

try {
    // Connexion à la base de données
    $pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Requête pour récupérer les informations de l'utilisateur
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifiez si l'utilisateur existe
    if (!$row) {
        header("Location: /index.php?msg=Utilisateur introuvable");
        exit;
    }
} catch (PDOException $e) {
    // Gérer les erreurs SQL
    header("Location: /index.php?msg=Erreur de connexion à la base de données");
    error_log($e->getMessage());
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Éditer un utilisateur</title>
    <link rel="stylesheet" href="https://bootswatch.com/5/darkly/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background-color: white; /* Fond blanc */
        }
        .container {
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container pt-3">
        <h1>Éditer un utilisateur</h1>
        <form action="save.php" method="post">
            <!-- Champ caché pour passer l'ID -->
            <input type="hidden" name="idd" value="<?= htmlspecialchars($id) ?>">

            <!-- Email -->
            <input type="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>"
                   placeholder="Email" name="email" required>

            <!-- Mot de passe -->
            <input type="password" class="form-control" value="<?= htmlspecialchars($row['password']) ?>"
                   placeholder="Mot de passe" name="pass">

            <!-- Rôle -->
            <select name="role" class="form-select" required>
                <option value="">Choisir un rôle</option>
                <option value="guest" <?= ($row['role'] === 'guest') ? 'selected' : '' ?>>Guest</option>
                <option value="admin" <?= ($row['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            </select>

            <!-- Bouton Enregistrer -->
            <button class="btn btn-success w-100 my-2">Enregistrer</button>
        </form>
    </div>
</body>
</html>
