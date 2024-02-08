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

// Définir les emails des administrateurs
$emailAdmin = "SJOLY@gers.fr";
$emailAdmin2 = "GROBILLIARD@gers.fr";

// Vérifier si l'utilisateur n'est pas connecté en tant qu'administrateur
if (!isset($_SESSION["email"]) || ($_SESSION["email"] != $emailAdmin && $_SESSION["email"] != $emailAdmin2)) {
    header("Location: connexion_admin.php");
    exit(); // Arrêter l'exécution du script après la redirection
}

// Connexion à la base de données avec mysqli
$hostname = $_ENV['HOSTNAME'];
$user = $_ENV['DB_SUPER_USER'];
$pwd = $_ENV['DB_PWD_SUPUSER'];
$database = $_ENV['DATABASE'];

// Connexion à la base de données avec mysqli
$connexion = mysqli_connect($hostname, $user, $pwd, $database);

// Vérification de la connexion
if (!$connexion) {
    die("La connexion a échoué : " . mysqli_connect_error());
}

// Configuration de l'encodage des caractères
mysqli_set_charset($connexion, "utf8");

// Vérification du formulaire soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification des champs obligatoires
    if (empty($_POST['email']) || empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['mot_de_passe'])) {
        echo "Tous les champs sont obligatoires.";
    } else {
        // Nettoyage des données envoyées par le formulaire
        $email = htmlspecialchars($_POST['email']);
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $mot_de_passe = htmlspecialchars($_POST['mot_de_passe']);

        // Vérification si l'email existe déjà dans la base de données
        $query = "SELECT * FROM utilisateur WHERE courriel=?";
        $stmt = $connexion->prepare($query);
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            echo "Cet email est déjà utilisé.";
        } else {
            // Hashage du mot de passe
            $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

            // Insertion des données dans la table utilisateur
            $type_log = "Ajout utilisateur";
            $query = "INSERT INTO utilisateur (courriel, nom, prenom, mdp) VALUES (?, ?, ?, ?)";
            $stmt = $connexion->prepare($query);
            $stmt->execute([$email, $nom, $prenom, $mot_de_passe_hash]);
            $log_query = "INSERT INTO logs (utilisateur, type_log) VALUES (?, ?)";
            $log_stmt = $connexion->prepare($log_query);
            $log_stmt->execute([$_SESSION["email"], "Ajout utilisateur"]);

            echo "Inscription réussie.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="email">Courriel:</label><br>
        <input type="email" id="email" name="email"><br>
        <label for="nom">Nom:</label><br>
        <input type="text" id="nom" name="nom"><br>
        <label for="prenom">Prénom:</label><br>
        <input type="text" id="prenom" name="prenom"><br>
        <label for="mot_de_passe">Mot de passe:</label><br>
        <input type="password" id="mot_de_passe" name="mot_de_passe"><br><br>
        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>