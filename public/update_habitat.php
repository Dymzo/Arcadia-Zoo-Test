<?php
session_start();

// Vérifiez si l'utilisateur est un administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrateur') {
    header("Location: index.php"); // Redirige vers la page d'accueil ou une page d'erreur
    exit();
}

// Récupérez les données du formulaire
$id = isset($_POST['id']) ? $_POST['id'] : '';
$name = isset($_POST['name']) ? $_POST['name'] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';

// Validez les données
$valid_ids = ['savane', 'jungle', 'marais'];
if (!in_array($id, $valid_ids)) {
    echo "Habitat non valide.";
    exit();
}

// Ici, vous devriez normalement mettre à jour les données dans une base de données
// Exemple de message de succès pour démonstration
echo "Les modifications ont été enregistrées avec succès pour l'habitat : " . htmlspecialchars($name);

// Redirection après la mise à jour
header("Location: page habitats.php"); // Redirige vers la page des habitats
exit();
