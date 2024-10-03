<?php
session_start();

// Vérifiez que la requête est bien une POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Détruire toutes les données de session
    session_unset();
    session_destroy();
    
    // Redirigez vers la page d'accueil ou une autre page
    header("Location: index.php");
    exit();
} else {
    // Gérer les cas où la méthode de requête n'est pas valide
    echo "Méthode de requête invalide.";
}
?>
