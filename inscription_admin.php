<?php
// Démarrage de la session
session_start();

// Inclusion de l'autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Chemin vers le fichier .env (situé en dehors du répertoire web)
$dotenvFile = __DIR__ . DIRECTORY_SEPARATOR . '\..\..\private\.env';

// Chargement des variables d'environnement à partir du fichier .env
if (file_exists($dotenvFile)) {
    $lines = file($dotenvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Lecture des lignes du fichier .env pour définir les variables d'environnement
    foreach ($lines as $line) {
        list($key, $value) = explode('=', $line, 2);
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }
}

// Récupération des informations de connexion à la base de données depuis les variables d'environnement
$hostname = $_ENV['HOSTNAME'];
$user = $_ENV['DB_SUPER_USER'];
$pwd = $_ENV['DB_PWD_SUPUSER'];
$database = $_ENV['DATABASE'];

// Connexion à la base de données MySQL
$connexion = mysqli_connect($hostname, $user, $pwd, $database);
// Définition du jeu de caractères UTF-8 pour la connexion
mysqli_set_charset($connexion, "utf8");

try {
    // Connexion à la base de données MySQL avec PDO pour les requêtes préparées
    $mysqlConnection = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $user, $pwd);
} catch (PDOException $error) {
    // Affichage d'un message d'erreur en cas d'échec de la connexion
    echo 'Échec de la connexion : ' . $error->getMessage();
}

// Traitement du formulaire d'inscription lors de la réception d'une requête POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données du formulaire et échappement des caractères spéciaux
    $nom = mysqli_real_escape_string($connexion, $_POST["nom"]);
    $prenom = mysqli_real_escape_string($connexion, $_POST["prenom"]);
    $email = mysqli_real_escape_string($connexion, $_POST["email"]);
    $mot_de_passe = mysqli_real_escape_string($connexion, $_POST["mot_de_passe"]);

    // Génération d'un lien de vérification unique
    $lien_verification = md5(uniqid(rand(1,10000), true));

    // Hachage du mot de passe
    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    // Construction de la requête SQL pour l'insertion des données dans la table utilisateur
    $sqlInsert = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, etat_verification, lien_verification) VALUES ('$nom', '$prenom', '$email', '$mot_de_passe_hash', 'en attente', '$lien_verification')";

    // Exécution de la requête SQL d'insertion des données
    if (mysqli_query($connexion, $sqlInsert)) {
        // Envoi d'un e-mail de vérification à l'utilisateur
        $sujet = "Vérification d'inscription";
        $message = "Cliquez sur le lien suivant pour vérifier votre inscription : 10.3.20.169/verification.php?lien=$lien_verification";
        $headers = "From: UCONORT@gers.fr";

        mail($email, $sujet, $message, $headers);

        // Redirection vers une page de succès après l'inscription
        header("Location: inscription_reussie.php");
        exit();
    } else {
        // Affichage d'un message d'erreur en cas d'échec de l'inscription
        echo "Erreur lors de l'inscription : " . mysqli_error($connexion);
    }
}
?>