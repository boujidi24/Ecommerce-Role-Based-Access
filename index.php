<?php
require 'auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    die("Veuillez vous connecter pour accéder à cette page.");
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'guest';

$pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$products = json_decode(file_get_contents("products.json"), true);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="https://bootswatch.com/5/cosmo/bootstrap.min.css">
    <style>
        .welcome-text {
            font-size: 3rem;
            font-weight: bold;
            text-align: center;
            color: #2a9d8f;
            margin-top: 20px;
            animation: fadeInDown 2s;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .product-card img {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: contain;
            background-color: #f8f9fa;
        }

        .cart-table th,
        .cart-table td {
            text-align: center;
            vertical-align: middle;
        }

        .admin-section h1,
        .guest-section h1 {
            color: #264653;
        }

        .btn-custom {
            background-color: #2a9d8f;
            border: none;
        }

        .btn-custom:hover {
            background-color: #1b6f5e;
        }
    </style>
</head>

<body>
    <div class="container pt-4">
        <?php if ($role === 'admin') : ?>
            <div class="admin-section">
                <h1 class="text-center mb-4">Bienvenue Administrateur</h1>
                <h2>Gestion des utilisateurs</h2>
                <form action="add.php" method="post" class="my-4">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="email" class="form-control" placeholder="Email" name="email" required>
                        </div>
                        <div class="col-md-4">
                            <input type="password" class="form-control" placeholder="Mot de passe" name="pass" required>
                        </div>
                        <div class="col-md-2">
                            <select name="role" class="form-select" required>
                                <option value="">Choisir un rôle</option>
                                <option value="guest">Guest</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-custom w-100">Ajouter</button>
                        </div>
                    </div>
                </form>

                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Supprimer</th>
                            <th>Modifier</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->query("SELECT * FROM users");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) :
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']); ?></td>
                                <td><?= htmlspecialchars($row['email']); ?></td>
                                <td><?= htmlspecialchars($row['role']); ?></td>
                                <td><a href="delete.php?idd=<?= $row['id']; ?>" class="btn btn-danger">Supprimer</a></td>
                                <td><a href="editer.php?idd=<?= $row['id']; ?>" class="btn btn-primary">Modifier</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($role === 'guest') : ?>
            <div class="guest-section">
                <p class="welcome-text">Bienvenue dans notre Boutique</p>

                <h2>Produits Disponibles</h2>
                <div class="row">
                    <?php foreach ($products as $product) : ?>
                        <div class="col-md-4 mb-4">
                            <div class="product-card">
                                <img src="<?= htmlspecialchars($product['thumbnail']); ?>" alt="<?= htmlspecialchars($product['title']); ?>">
                                <div class="p-3">
                                    <h5><?= htmlspecialchars($product['title']); ?></h5>
                                    <p><?= htmlspecialchars($product['description']); ?></p>
                                    <p><strong><?= htmlspecialchars($product['price']); ?>€</strong></p>
                                    <form action="add_to_cart.php" method="post">
                                        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                        <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock']; ?>" class="form-control mb-2" required>
                                        <button type="submit" class="btn btn-custom w-100">Ajouter au Panier</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <h2>Votre Panier</h2>
                <?php
                $cart_stmt = $pdo->prepare("
                SELECT p.name AS title, p.price, c.quantity 
                FROM cart_items c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = ?
                ");
                $cart_stmt->execute([$user_id]);
                $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($cart_items) :
                    $total_price = 0;
                ?>
                    <table class="table table-striped cart-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix</th>
                                <th>Quantité</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item) : ?>
                                <?php $item_total = $item['price'] * $item['quantity']; ?>
                                <tr>
                                    <td><?= htmlspecialchars($item['title']); ?></td>
                                    <td><?= htmlspecialchars($item['price']); ?>€</td>
                                    <td><?= htmlspecialchars($item['quantity']); ?></td>
                                    <td><?= htmlspecialchars($item_total); ?>€</td>
                                </tr>
                                <?php $total_price += $item_total; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <p><strong>Total : </strong><?= $total_price; ?>€</p>
                <?php else : ?>
                    <p>Votre panier est vide.</p>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <h1>Accès non autorisé</h1>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
