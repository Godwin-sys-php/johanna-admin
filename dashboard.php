<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<?php
// get the json file
$file_path = 'articles/list.json';
$json = file_get_contents($file_path);
$data = json_decode($json, true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Articles</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Articles</h1>
    <a href="edit.php" class="btn btn-primary mb-4">Nouvel Article</a>
    <ul class="list-group">
        <?php foreach ($data as $item): ?>
            <li class="list-group-item">
                <a href="https://admin.johannabusiness.com/article.php?id=<?= $item['id'] ?>" target="_blank"><?= $item['title'] ?></a>
                <a href="edit.php?id=<?= $item['id'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                <a href="delete.php?id=<?= $item['id'] ?>" class="btn btn-danger btn-sm">Supprimer</a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
