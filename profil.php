<?php
// Démarrage de la session
session_start();

// Inclusion de l'autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Chemin vers le fichier .env (situé en dehors du répertoire web)
$dotenvFile = __DIR__ . DIRECTORY_SEPARATOR . '../../private/.env';

// Chargement des variables d'environnement à partir du fichier .env
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        list($key, $value) = explode('=', $line, 2);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Vérification de la session email, redirection si non connecté
if (!isset($_SESSION["email"])) {
    header("Location: connexion_admin.php");
    exit();
}

// Connexion à la base de données avec PDO
try {
    $mysqlConnection = new PDO('mysql:host=' . $_ENV['HOSTNAME'] . ';dbname=' . $_ENV['DATABASE'], $_ENV['DB_SUPER_USER'], $_ENV['DB_PWD_SUPUSER']);
    $mysqlConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
    echo 'Échec de la connexion : ' . $error->getMessage();
    exit();
}

// Récupération des informations de l'utilisateur
try {
    $query = "SELECT * FROM utilisateur WHERE courriel = ?";
    $stmt = $mysqlConnection->prepare($query);
    $stmt->execute([$_SESSION["email"]]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
    echo 'Échec de la récupération des données : ' . $error->getMessage();
    exit();
}

// Vérification si le formulaire de changement de mot de passe est soumis
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["new_password"])) {
    // Vérification des champs obligatoires
    if (empty($_POST["new_password"])) {
        echo "Le champ du nouveau mot de passe est obligatoire.";
    } else {
        // Hashage du nouveau mot de passe
        $new_password_hash = password_hash($_POST["new_password"], PASSWORD_DEFAULT);
        try {
            // Mise à jour du mot de passe dans la base de données
            $update_query = "UPDATE utilisateur SET mdp = ? WHERE courriel = ?";
            $update_stmt = $mysqlConnection->prepare($update_query);
            $update_stmt->execute([$new_password_hash, $_SESSION["email"]]);
            echo "Mot de passe mis à jour avec succès.";
        } catch (PDOException $error) {
            echo 'Échec de la mise à jour du mot de passe : ' . $error->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
</head>
<body>
    <h2>Profil Utilisateur</h2>
    <h3>Informations Utilisateur</h3>
    <p><strong>Courriel:</strong> <?php echo $user["courriel"]; ?></p>
    <p><strong>Nom:</strong> <?php echo $user["nom"]; ?></p>
    <p><strong>Prénom:</strong> <?php echo $user["prenom"]; ?></p>
    
    <h3>Changer le mot de passe</h3>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="new_password">Nouveau mot de passe:</label><br>
        <input type="password" id="new_password" name="new_password"><br><br>
        <input type="submit" value="Changer le mot de passe">
    </form>
</body>
</html>