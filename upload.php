<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
<?php
// Dossier où les images seront sauvegardées
$targetDir = "uploads/";

if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$response = array();
if ($_FILES) {
    $file = $_FILES['file'];
    $fileName = basename($file['name']);
    $targetFile = $targetDir . $fileName;
    
    // Vérification et déplacement du fichier
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Utiliser une URL absolue en incluant le domaine complet
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $absoluteUrl = $protocol . $_SERVER['HTTP_HOST'] . '/' . $targetFile; // Construire une URL absolue
        $response = array('location' => $absoluteUrl);
    } else {
        header("HTTP/1.1 500 Internal Server Error");
        $response = array('error' => 'Could not upload file');
    }
}

// Retourne la réponse en JSON
echo json_encode($response);
