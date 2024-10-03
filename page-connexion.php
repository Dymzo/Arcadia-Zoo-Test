<?php
session_start();
require 'database.php'; // Connexion à la base de données

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sécurisation des entrées utilisateurs
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $role = ucfirst(strtolower(trim($_POST['role']))); // Normalisation de la casse du rôle

    // Vérification que les champs ne sont pas vides
    if (empty($username) || empty($password) || empty($role)) {
        echo "Tous les champs sont obligatoires.";
        exit();
    }

    try {
        // Rechercher l'utilisateur dans la base de données
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE username = :username");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        // Vérifier si l'utilisateur existe
        if (!$user) {
            echo "Nom d'utilisateur ou mot de passe incorrect.";
            exit();
        }

        // Rechercher le role_id à partir du label du rôle
        $stmtRole = $pdo->prepare("SELECT role_id FROM role WHERE label = :label");
        $stmtRole->execute(['label' => $role]);
        $roleData = $stmtRole->fetch();
        $role_id = $roleData ? $roleData['role_id'] : null;

        // Vérifier si le rôle est valide
        if (!$role_id) {
            echo "Rôle non trouvé.";
            exit();
        }

        // Vérification du mot de passe et du rôle
        if (password_verify($password, $user['password']) && $user['role_id'] == $role_id) {
            // Connexion réussie
            session_regenerate_id(); // Sécurisation de la session
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;

           // Redirection après connexion
header("Location: index.php"); // Redirige vers la page d'accueil ou une page de votre choix
exit();
        } else {
            echo "Nom d'utilisateur, mot de passe, ou rôle incorrect.";
            exit();
        }
    } catch (PDOException $e) {
        // Gestion des erreurs de base de données
        echo "Erreur de connexion : " . $e->getMessage();
        exit();
    }
} else {
    echo "Méthode de requête invalide.";
}
?>
