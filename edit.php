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

$title = '';
$description = '';
$content = '';

if ($id && file_exists("articles/$id.html")) {
    // get data from the json file & from the html file
    $file_path = 'articles/list.json';
    $json = file_get_contents($file_path);
    $data = json_decode($json, true);
    
    // get the specific article witht the id
    $article = null;
    foreach ($data as $key => $item) {
        if ($item['id'] === $id) {
            $article = $item;
            break;
        }
    }

    $title = $article['title'];
    $description = $article['description'];
    $content = file_get_contents("articles/$id.html");
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Éditeur TinyMCE avec Images</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <!-- TinyMCE Local -->
    <script src="../js/tinymce/tinymce.min.js"></script><script>
document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
        selector: '#content',
        height: 300,
        plugins: 'lists link image table',
        toolbar: 'undo redo | formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        image_title: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_url: 'upload.php',
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
                var file = this.files[0];
                var reader = new FileReader();
                reader.onload = function () {
                    var id = 'blobid' + (new Date()).getTime();
                    var blobCache = tinymce.activeEditor.editorUpload.blobCache;
                    var base64 = reader.result.split(',')[1];
                    var blobInfo = blobCache.create(id, file, base64);
                    blobCache.add(blobInfo);

                    cb("/" + blobInfo.blobUri(), { title: file.name });
                };
                reader.readAsDataURL(file);
            };

            input.click();
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                editor.setContent(<?= json_encode($content) ?>);
            });
        }
    });
});
</script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Nouvel Article</h1>
        <form action="save.php" method="post">
            <div class="form-group mb-3">
                <label for="title" class="form-label">Titre</label>
                <input value="<?= $title ?>" type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="form-group mb-3">
                <label for="title" class="form-label">Description</label>
                <input value="<?= $description ?>" type="text" class="form-control" id="description" name="description" required>
            </div>
            <div class="form-group mb-3">
                <label for="content" class="form-label">Contenu</label>
                <textarea id="content" name="content" class="form-control"><?= htmlspecialchars($content) ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Sauvegarder</button>
            <a href="index.php" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
