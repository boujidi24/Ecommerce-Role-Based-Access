<?php
// Démarrer la session si elle n'est pas déjà active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Définir un rôle par défaut si absent
if (!isset($_SESSION['user_role'])) {
    $_SESSION['user_role'] = 'guest'; // Rôle par défaut : guest
}

// Vérification du rôle de l'utilisateur
if ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'guest') {
    die("Accès non autorisé.");
}

// Si l'utilisateur est un invité (guest), il ne peut modifier que son propre compte
if ($_SESSION['user_role'] === 'guest' && isset($id) && $_SESSION['user_id'] !== $id) {
    die("Vous ne pouvez modifier que votre propre compte.");
}

// Initialisation du panier vide (si non défini)
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Affichage de la session et des cookies (pour debugging)
echo '<pre>';
print_r($_COOKIE);
print_r($_SESSION);
echo '</pre>';
?>
