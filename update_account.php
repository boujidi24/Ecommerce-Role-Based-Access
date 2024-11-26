<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=tp_users', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$user_id = $_SESSION['user_id'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

$stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
$stmt->execute([$email, $password, $user_id]);

header("Location: index.php?msg=Compte mis à jour avec succès.");
exit;
?>
