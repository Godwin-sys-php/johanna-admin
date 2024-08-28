<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<?php
$id = $_POST['id'] ?? uniqid();
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$content = $_POST['content'] ?? '';

function modify_img_tag($matches) {
  $src = $matches[1]; // Capture le contenu du src
  $new_src = 'https://admin.johannabusiness.com/' . $src; // Ajoute l'URL de base
  // Reconstruit la balise <img> avec le nouveau src et width="100%"
  return '<img src="' . $new_src . '" alt="' . $matches[2] . '" max-height="20wh">';
}

// Utilisation de preg_replace_callback pour modifier toutes les balises <img> dans le contenu
$content = preg_replace_callback(
  '/<img src="([^"]+)" alt="([^"]*)"[^>]*>/i',
  'modify_img_tag',
  $content
);

$html = $content;

file_put_contents("articles/$id.html", $html);

$file_path = './articles/list.json';

// Vérifier si le fichier existe
if (!file_exists($file_path)) {
    // Si le fichier n'existe pas, on initialise un tableau vide
    $data = [];
} else {
    // Lire le contenu actuel du fichier JSON
    $json = file_get_contents($file_path);

    // Décoder le JSON en tableau PHP
    $data = json_decode($json, true);

    // Vérifier si le JSON a été correctement décodé
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        die('Erreur lors du décodage du JSON.');
    }
}

// Vérifier si l'article avec cet ID existe déjà
$articleExists = false;
foreach ($data as &$item) {
    if ($item['id'] === $id) {
        // Mise à jour de l'article existant
        $item['title'] = $title;
        $item['description'] = $description;
        $articleExists = true;
        break;
    }
}

// Si l'article n'existe pas, on l'ajoute
if (!$articleExists) {
    $new_item = [
        'id' => $id,
        'title' => $title,
        'description' => $description,
    ];
    $data[] = $new_item;
}

// Re-encoder le tableau en JSON
$new_json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

// Vérifier si le JSON a été correctement encodé
if ($new_json === false) {
    die('Erreur lors de l\'encodage du JSON.');
}

// Sauvegarder le nouveau JSON dans le fichier
if (file_put_contents($file_path, $new_json) === false) {
    die('Erreur lors de l\'écriture dans le fichier JSON.');
}

echo 'Article ajouté ou mis à jour avec succès.';

header("Location: index.php");
exit();
?>
