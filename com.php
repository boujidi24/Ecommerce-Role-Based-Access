<?php
session_start();
$products = json_decode(file_get_contents("./products.json"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DDEV</title>
    <link rel="stylesheet" href="https://bootswatch.com/5/darkly/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container pt-4">
        <h1>Products</h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product) : ?>
                    <tr id="produit_<?= $product->id ?>">
                        <td><?= htmlspecialchars($product->id) ?></td>
                        <td><?= htmlspecialchars($product->title) ?></td>
                        <td><input type="number" class="form-control text-end" value="1"></td>
                        <td><?= htmlspecialchars($product->price) ?></td>
                        <td>
                            <button class="btn btn-primary" onclick="addToCart(<?= $product->id ?>)">
                                <i class="bi bi-basket"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function addToCart(id) {
            const qte = document.querySelector('#produit_' + id + ' input').value;
            fetch(`add_tocart.php?id=${id}&qte=${qte}`)
                .then(response => response.json())
                .then(data => console.log(data));
        }
    </script>
</body>
</html>
