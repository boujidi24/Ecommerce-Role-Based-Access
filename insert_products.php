<?php
// Connexion à la base de données
$pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Charger les produits depuis le fichier JSON
$jsonData = file_get_contents('products.json');
$products = json_decode($jsonData, true);

try {
    // Préparer la requête avec ON DUPLICATE KEY UPDATE
    $stmt = $pdo->prepare("
        INSERT INTO products (id, title, description, category, price, discountPercentage, rating, stock, brand, sku, thumbnail)
        VALUES (:id, :title, :description, :category, :price, :discountPercentage, :rating, :stock, :brand, :sku, :thumbnail)
        ON DUPLICATE KEY UPDATE
        title = VALUES(title),
        description = VALUES(description),
        category = VALUES(category),
        price = VALUES(price),
        discountPercentage = VALUES(discountPercentage),
        rating = VALUES(rating),
        stock = VALUES(stock),
        brand = VALUES(brand),
        sku = VALUES(sku),
        thumbnail = VALUES(thumbnail)
    ");

    // Insérer ou mettre à jour chaque produit
    foreach ($products as $product) {
        $stmt->execute([
            ':id' => $product['id'],
            ':title' => $product['title'],
            ':description' => $product['description'],
            ':category' => $product['category'],
            ':price' => $product['price'],
            ':discountPercentage' => $product['discountPercentage'],
            ':rating' => $product['rating'],
            ':stock' => $product['stock'],
            ':brand' => $product['brand'],
            ':sku' => $product['sku'],
            ':thumbnail' => $product['thumbnail']
        ]);
    }

    echo "Données insérées ou mises à jour avec succès !";
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
