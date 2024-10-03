<?php
session_start();
require 'database.php'; // Connexion à la base de données

// Vérifiez que l'utilisateur est connecté et qu'il a un rôle d'administrateur
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'Administrateur') {
    header("Location: page-connexion.php"); // Redirige vers la page de connexion si l'utilisateur n'est pas administrateur
    exit();
}

// Fonction pour exécuter les requêtes et gérer les erreurs
function executeQuery($pdo, $query, $params) {
    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        return "<p class='success'>Opération réussie!</p>";
    } catch (PDOException $e) {
        return "<p class='error'>Erreur : " . $e->getMessage() . "</p>";
    }
}

// Gestion des comptes
if (isset($_POST['create_account'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $role_id = $_POST['role_id'];

    $message = executeQuery(
        $pdo,
        "INSERT INTO utilisateur (username, password, nom, prenom, role_id) VALUES (?, ?, ?, ?, ?)",
        [$username, $password, $nom, $prenom, $role_id]
    );
}

if (isset($_POST['delete_account'])) {
    $user_id = $_POST['user_id'];

    $message = executeQuery(
        $pdo,
        "DELETE FROM utilisateur WHERE user_id = ?",
        [$user_id]
    );
}

if (isset($_POST['update_account'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $role_id = $_POST['role_id'];

    if (!empty($password)) {
        $query = "UPDATE utilisateur SET username = ?, password = ?, nom = ?, prenom = ?, role_id = ? WHERE user_id = ?";
        $params = [$username, password_hash($password, PASSWORD_DEFAULT), $nom, $prenom, $role_id, $user_id];
    } else {
        $query = "UPDATE utilisateur SET username = ?, nom = ?, prenom = ?, role_id = ? WHERE user_id = ?";
        $params = [$username, $nom, $prenom, $role_id, $user_id];
    }

    $message = executeQuery($pdo, $query, $params);
}

// Gestion des habitats
if (isset($_POST['add_habitat'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Gestion de l'image
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        $target = 'uploads/' . $image;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $message = "<p class='error'>Erreur lors du téléchargement de l'image.</p>";
        }
    }

    $message = executeQuery(
        $pdo,
        "INSERT INTO habitats (name, description, image) VALUES (?, ?, ?)",
        [$name, $description, $image]
    );
}

if (isset($_POST['update_habitat'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Gestion de l'image
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        $target = 'uploads/' . $image;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $message = "<p class='error'>Erreur lors du téléchargement de l'image.</p>";
        }
    }

    $query = "UPDATE habitats SET name = ?, description = ?" . ($image ? ", image = ?" : "") . " WHERE id = ?";
    $params = [$name, $description];
    if ($image) {
        $params[] = $image;
    }
    $params[] = $id;

    $message = executeQuery($pdo, $query, $params);
}

if (isset($_POST['delete_habitat'])) {
    $id = $_POST['id'];

    // Supprimer l'image associée
    $stmt = $pdo->prepare("SELECT image FROM habitats WHERE id = ?");
    $stmt->execute([$id]);
    $habitat = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($habitat && $habitat['image']) {
        $imagePath = 'uploads/' . $habitat['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    $message = executeQuery(
        $pdo,
        "DELETE FROM habitats WHERE id = ?",
        [$id]
    );
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'Administration</title>
    <link href="CSS admin.css" rel="stylesheet" />
</head>
<body>
    <header>
        <h1>Panneau d'Administration</h1>
        <nav>
            <a href="admin-dashboard.php" class="active">Tableau de bord</a>
            <a href="manage_reviews.php">Gérer les avis</a>
            <a href="manage_services.php">Gérer les services</a>
            <a href="logout.php">Déconnexion</a>
        </nav>
    </header>
    <main>
        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
        <p>Vous pouvez gérer les avis, les services, etc.</p>

        <!-- Messages de notification -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Création de compte utilisateur -->
        <h3>Créer un nouveau compte</h3>
        <form action="admin-dashboard.php" method="post">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
            
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
            
            <label for="role_id">Rôle :</label>
            <select id="role_id" name="role_id" required>
                <option value="1">Administrateur</option>
                <option value="2">Employé</option>
                <option value="3">Vétérinaire</option>
            </select>
            
            <input type="submit" name="create_account" value="Créer le compte">
        </form>

        <!-- Gestion des comptes utilisateur -->
        <h3>Gérer les comptes</h3>
        <table>
            <tr>
                <th>Nom d'utilisateur</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Rôle</th>
                <th>Actions</th>
            </tr>
            <?php
            try {
                $stmt = $pdo->query("SELECT user_id, username, nom, prenom, role_id FROM utilisateur");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['role_id']) . "</td>";
                    echo "<td>
                            <form action='admin-dashboard.php' method='post' style='display:inline;'>
                                <input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'>
                                <input type='submit' name='delete_account' value='Supprimer' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ce compte ?\");'>
                            </form>
                            <form action='admin-dashboard.php' method='post' style='display:inline;'>
                                <input type='hidden' name='user_id' value='" . htmlspecialchars($row['user_id']) . "'>
                                <input type='submit' name='edit_account' value='Modifier'>
                            </form>
                          </td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='5'>Erreur lors de la récupération des comptes : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </table>

        <!-- Formulaire pour modifier un compte -->
        <?php
        if (isset($_POST['edit_account'])) {
            $user_id = $_POST['user_id'];

            $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <h3>Modifier le compte de <?php echo htmlspecialchars($user['username']); ?></h3>
        <form action="admin-dashboard.php" method="post">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
            
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password"> <!-- Laisser vide si pas de changement -->
            
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
            
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
            
            <label for="role_id">Rôle :</label>
            <select id="role_id" name="role_id" required>
                <option value="1" <?php echo $user['role_id'] == 1 ? 'selected' : ''; ?>>Administrateur</option>
                <option value="2" <?php echo $user['role_id'] == 2 ? 'selected' : ''; ?>>Employé</option>
                <option value="3" <?php echo $user['role_id'] == 3 ? 'selected' : ''; ?>>Vétérinaire</option>
            </select>
            
            <input type="submit" name="update_account" value="Mettre à jour">
        </form>
        <?php
        }
        ?>

        <!-- Gestion des habitats -->
        <h3>Ajouter un Habitat</h3>
        <form action="admin-dashboard.php" method="post" enctype="multipart/form-data">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name=nom" required>

            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required></textarea>

            <label for="image">Image :</label>
            <input type="file" id="image" name="image">

            <input type="submit" name="add_habitat" value="Ajouter l'habitat">
        </form>

        <h3>Modifier ou Supprimer un Habitat</h3>
        <?php
        $habitats = $pdo->query("SELECT * FROM habitat")->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <form action="admin-dashboard.php" method="post" enctype="multipart/form-data">
            <label for="habitat_id">Choisir un habitat :</label>
            <select id="habitat_id" name="habitat_id" required onchange="this.form.submit()">
                <option value="">-- Sélectionner un habitat --</option>
                <?php foreach ($habitats as $habitat): ?>
                    <option value="<?php echo htmlspecialchars($habitat['habitat_id']); ?>" <?php echo isset($_POST['habitat_id']) && $_POST['habitat_id'] == $habitat['habitat_id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($habitat['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>

        <?php
        if (isset($_POST['id'])) {
            $selectedId = $_POST['id'];
            $stmt = $pdo->prepare("SELECT * FROM habitat WHERE id = ?");
            $stmt->execute([$selectedId]);
            $selectedHabitat = $stmt->fetch(PDO::FETCH_ASSOC);
        ?>
        <form action="admin-dashboard.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="habitat_id" value="<?php echo htmlspecialchars($selectedHabitat['habitat_id']); ?>">
            
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($selectedHabitat['nom']); ?>" required>
            
            <label for="description">Description :</label>
            <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($selectedHabitat['description']); ?></textarea>
            
            <label for="image">Nouvelle Image (laisser vide pour conserver l'image actuelle) :</label>
            <input type="file" id="image" name="image">
            
            <input type="submit" name="update_habitat" value="Modifier">
            <input type="submit" name="delete_habitat" value="Supprimer" onclick='return confirm("Êtes-vous sûr de vouloir supprimer cet habitat ?");'>
        </form>
        <?php
        }
        ?>

        <!-- Ajouter ici des formulaires similaires pour services, horaires et animaux -->
    </main>
</body>
</html>
