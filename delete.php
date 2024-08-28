<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<?php
$id = $_GET['id'] ?? '';

if ($id && file_exists("articles/$id.html")) {
    unlink("articles/$id.html");
}

$file_path = './articles/list.json';

// Vérifier si le fichier JSON existe
if (file_exists($file_path)) {
    // Lire le contenu actuel du fichier JSON
    $json = file_get_contents($file_path);

    // Décoder le JSON en tableau PHP
    $data = json_decode($json, true);

    // Vérifier si le JSON a été correctement décodé
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        die('Erreur lors du décodage du JSON.');
    }

    // Supprimer l'article du tableau
    foreach ($data as $key => $item) {
        if ($item['id'] === $id) {
            unset($data[$key]);
            break;
        }
    }

    // Re-indexer le tableau
    $data = array_values($data);

    // Re-encoder le tableau en JSON
    $new_json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    // Vérifier si le JSON a été correctement encodé
    if ($new_json === false) {
        die('Erreur lors de l\'encodage du JSON.');
    }

    // Sauvegarder le nouveau JSON dans le fichier
    if (file_put_contents($file_path, $new_json) === false) {
        die('Erreur lors de la sauvegarde du JSON.');
    }
}

header("Location: index.php");
exit();
