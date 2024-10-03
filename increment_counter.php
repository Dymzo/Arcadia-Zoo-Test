<?php
include 'database.php'; // Assure-toi que ce fichier est inclus pour la connexion PDO

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['animal'])) {
        $animalName = $data['animal'];

        // Préparer et exécuter la requête pour incrémenter le compteur
        $sql = "UPDATE animal SET compteur_consultations = compteur_consultations + 1 WHERE prenom = ?";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$animalName])) {
            echo json_encode(['success' => true, 'message' => 'Compteur mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du compteur']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Nom de l\'animal manquant']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Requête non valide']);
}
?>
