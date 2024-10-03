<?php
session_start();

// Durée de la session avant expiration due à l'inactivité (exemple : 30 minutes)
$timeout_duration = 1800; // 1800 secondes = 30 minutes

// Vérification de l'inactivité
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Si le temps d'inactivité est dépassé, détruire la session et rediriger vers la page de login
    session_unset();
    session_destroy();
    header("Location: page-connexion.php"); // Redirige vers la page de connexion
    exit();
}

// Mise à jour de la dernière activité
$_SESSION['last_activity'] = time();

// Vérifier si l'utilisateur est connecté et a le bon rôle
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Administrateur';
}

?>
