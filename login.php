<?php
session_start();

$email = $_POST['email'];
$password = $_POST['password'];

// Identifiants de connexion valides
$valid_email = 'directeur.general@johannabusiness.com';
$valid_password = 'DirecteurGeneral6388';

// Vérification des identifiants
if ($email === $valid_email && $password === $valid_password) {
    $_SESSION['loggedin'] = true;
    $_SESSION['email'] = $email;
    header("Location: dashboard.php");
    exit;
} else {
    header("Location: index.php?error=1");
    exit;
}
?>
