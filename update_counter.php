<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";  // Ton utilisateur MySQL (vérifie si c'est correct)
$password = "";  // Ton mot de passe MySQL (vide si pas de mot de passe)
$dbname = "arcadia_zoo";  // Remplace par le nom de ta base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Fonction pour incrémenter le compteur de consultations
function incrementConsultationCounter($animalName) {
    global $conn;

    // Préparer la requête SQL pour mettre à jour le compteur
    $sql = "UPDATE animal SET compteur_consultations = compteur_consultations + 1 WHERE animal_name = ?";
    $stmt = $conn->prepare($sql);
    
    // Liaison du paramètre
    $stmt->bind_param("s", $animalName);
    
    // Exécution de la requête
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['animal'])) {
    $animalName = $data['animal'];

    // Incrémenter le compteur
    if (incrementConsultationCounter($animalName)) {
        echo json_encode(['success' => true, 'message' => 'Compteur de consultations mis à jour avec succès.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour du compteur.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Aucun nom d\'animal fourni.']);
}

$conn->close();
?>
