<?php
session_start();
require 'db.php'; // Connexion à la base de données
require 'session.php'; // Vérification de session utilisateur

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit("Vous devez être connecté pour ajouter des produits au panier.");
}

try {
    // Récupération des données utilisateur et produit
    $user_id = $_SESSION['user_id'];
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    // Validation des données
    if (!$product_id || !$quantity || $quantity <= 0) {
        header("Location: index.php?error=Données invalides.");
        exit;
    }

    // Charger les produits depuis le fichier JSON
    $products = json_decode(file_get_contents("products.json"), true);

    // Vérifier si le produit existe dans le fichier JSON
    $product = array_filter($products, fn($p) => $p['id'] === $product_id);
    if (!$product) {
        header("Location: index.php?error=Produit introuvable.");
        exit;
    }

    $product = reset($product); // Récupérer le premier élément correspondant

    // Vérification du stock
    if ($product['stock'] < $quantity) {
        header("Location: index.php?error=Stock insuffisant.");
        exit;
    }

    // Vérifier si le produit est déjà dans le panier
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        // Mise à jour de la quantité si le produit est déjà dans le panier
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $stmt->execute([$new_quantity, $cart_item['id']]);
    } else {
        // Ajout d'un nouvel article dans la table cart_items
        $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, product_id, quantity, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }

    // Mise à jour du stock dans le fichier JSON
    foreach ($products as &$p) {
        if ($p['id'] === $product_id) {
            $p['stock'] -= $quantity;
            break;
        }
    }
    file_put_contents("products.json", json_encode($products, JSON_PRETTY_PRINT));

    // Redirection avec message de succès
    header("Location: index.php?msg=Produit ajouté au panier !");
    exit;

} catch (PDOException $e) {
    // Gestion des erreurs SQL
    echo "Erreur SQL : " . $e->getMessage();
    exit;
} catch (Exception $e) {
    // Gestion des erreurs générales
    echo "Erreur : " . $e->getMessage();
    exit;
}
