<?php
// Démarrage de la session
session_start();

// Inclusion de l'autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Chemin vers le fichier .env
$dotenvFile = __DIR__ . DIRECTORY_SEPARATOR .'../../private/.env';

// Chargement des variables d'environnement à partir du fichier .env
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        list($key, $value) = explode('=', $line, 2);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Configuration de la connexion à la base de données
$hostname = $_ENV['HOSTNAME'];
$user = $_ENV['DB_USER'];
$pwd = $_ENV['DB_PWD_USER'];
$database = $_ENV['DATABASE'];

// Connexion à la base de données avec MySQLi
$connexion = mysqli_connect($hostname, $user, $pwd, $database);

// Configuration de l'encodage des caractères
mysqli_set_charset($connexion, "utf8");

// Vérification de la soumission du formulaire en méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Échappement des entrées utilisateur pour éviter les injections SQL
    $email = mysqli_real_escape_string($connexion, $_POST["email"]);
    $mot_de_passe = mysqli_real_escape_string($connexion, $_POST["mot_de_passe"]);

    // Requête SQL pour récupérer l'utilisateur avec l'email fourni
    $sqlQuery = "SELECT * FROM utilisateur WHERE courriel = '$email'";
    $result = mysqli_query($connexion, $sqlQuery);

    // Vérification du résultat de la requête
    if ($result && $row = mysqli_fetch_assoc($result)) {
        // Vérification du mot de passe avec la fonction password_verify
        if (password_verify($mot_de_passe, $row['mdp'])) {
            // Authentification réussie, enregistrement de l'email dans la session
            $_SESSION["email"] = $email;
            header("Location: admin_annuaire_asso.php");
            exit();
        } else {
            // Mot de passe incorrect
            $erreur_message = "Les informations d'identifications ne correspondent pas. Réessayez.";
        }
    } else {
        // Aucun utilisateur trouvé avec l'email fourni
        $erreur_message = "Les informations d'identifications ne correspondent pas. Réessayez.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
</head>
<body>

<h2>Connexion</h2>

<?php
// Affichage d'un message d'erreur s'il y a lieu
if (isset($erreur_message)) {
    echo "<p style='color: red;'>$erreur_message</p>";
}
?>

<!-- Formulaire de connexion -->
<form method="post" action="">
    <label for="email">Courriel :</label>
    <input type="email" name="email" required><br>

    <label for="mot_de_passe">Mot de passe :</label>
    <input type="password" name="mot_de_passe" required><br>

    <input type="submit" value="Se connecter">
</form>
</body>
</html>