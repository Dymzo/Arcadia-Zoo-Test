<?php
session_start();

// Vérifiez si l'utilisateur est un administrateur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrateur') {
    header("Location: index.php"); // Redirige vers la page d'accueil ou une page d'erreur
    exit();
}

// Vérifiez le paramètre 'id' dans l'URL
$id = isset($_GET['id']) ? $_GET['id'] : '';
$valid_ids = ['savane', 'jungle', 'marais'];

if (!in_array($id, $valid_ids)) {
    echo "Habitat non valide.";
    exit();
}

// Ici, vous devriez normalement récupérer les données existantes pour l'habitat
// Exemple de données statiques pour démonstration
$habitat_data = [
    'savane' => ['name' => 'La Savane', 'description' => 'Description de la Savane'],
    'jungle' => ['name' => 'La Jungle', 'description' => 'Description de la Jungle'],
    'marais' => ['name' => 'Le Marais', 'description' => 'Description du Marais']
];

$data = $habitat_data[$id];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Habitat</title>
    <!-- Lien vers la feuille de style principale -->
    <link href="CSS page accueil.css" rel="stylesheet" />
</head>
<body>
    <h1>Modifier l'Habitat : <?php echo htmlspecialchars($data['name']); ?></h1>
    <form action="update_habitat.php" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <label for="name">Nom de l'Habitat:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($data['name']); ?>" required>
        
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($data['description']); ?></textarea>
        
        <input type="submit" value="Enregistrer les modifications">
    </form>
</body>
</html>
