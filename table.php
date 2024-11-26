<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Charger les données JSON
$jsonData = file_get_contents('products.json');
$products = json_decode($jsonData, true);

try {
    // Désactiver le mode autocommit pour une transaction
    $pdo->beginTransaction();

    // Parcourir chaque produit dans le JSON
    foreach ($products as $product) {
        // Insérer le produit dans la table products
        $stmt = $pdo->prepare("
            INSERT INTO products (guid, name, price, description, stock, created_at, isActive, balance, picture, age, eyeColor, gender, company, email, phone, address, about, registered, latitude, longitude, favoriteFruit)
            VALUES (:guid, :name, :price, :description, :stock, NOW(), :isActive, :balance, :picture, :age, :eyeColor, :gender, :company, :email, :phone, :address, :about, :registered, :latitude, :longitude, :favoriteFruit)
        ");
        $stmt->execute([
            ':guid' => $product['guid'],
            ':name' => $product['name'],
            ':price' => $product['price'],
            ':description' => $product['description'],
            ':stock' => $product['stock'],
            ':isActive' => $product['isActive'],
            ':balance' => $product['balance'],
            ':picture' => $product['picture'],
            ':age' => $product['age'],
            ':eyeColor' => $product['eyeColor'],
            ':gender' => $product['gender'],
            ':company' => $product['company'],
            ':email' => $product['email'],
            ':phone' => $product['phone'],
            ':address' => $product['address'],
            ':about' => $product['about'],
            ':registered' => $product['registered'],
            ':latitude' => $product['latitude'],
            ':longitude' => $product['longitude'],
            ':favoriteFruit' => $product['favoriteFruit'],
        ]);

        // Récupérer l'ID du produit inséré
        $productId = $pdo->lastInsertId();

        // Insérer les tags dans la table product_tags
        foreach ($product['tags'] as $tag) {
            $tagStmt = $pdo->prepare("INSERT INTO product_tags (product_id, tag) VALUES (:product_id, :tag)");
            $tagStmt->execute([
                ':product_id' => $productId,
                ':tag' => $tag,
            ]);
        }

        // Insérer les amis dans la table product_friends
        foreach ($product['friends'] as $friend) {
            $friendStmt = $pdo->prepare("INSERT INTO product_friends (product_id, friend_id, friend_name) VALUES (:product_id, :friend_id, :friend_name)");
            $friendStmt->execute([
                ':product_id' => $productId,
                ':friend_id' => $friend['id'],
                ':friend_name' => $friend['name'],
            ]);
        }
    }

    // Valider la transaction
    $pdo->commit();
    echo "Données insérées avec succès !";
} catch (Exception $e) {
    // Annuler la transaction en cas d'erreur
    $pdo->rollBack();
    die("Erreur : " . $e->getMessage());
}
