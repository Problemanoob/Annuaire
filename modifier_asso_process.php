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

if (!isset($_SESSION["email"])) {
    header("Location: connexion_admin.php");
    exit();
}
$hostname = $_ENV['HOSTNAME'];
$user = $_ENV['DB_SUPER_USER'];
$pwd = $_ENV['DB_PWD_SUPUSER'];
$database = $_ENV['DATABASE'];
$connexion = mysqli_connect($hostname, $user, $pwd, $database);
mysqli_set_charset($connexion, "utf8");

try {
    $mysqlConnection = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $user, $pwd);
} catch (PDOException $error) {
    echo 'Échec de la connexion : ' . $error->getMessage();
}

echo "<a class=\"redirect\" href=\"admin_annuaire_asso.php\">Retour</a>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? $_POST['id'] : null;
    $nom = isset($_POST['nom']) ? mysqli_real_escape_string($connexion, $_POST['nom']) : null;
    $acronyme = isset($_POST['acronyme']) ? mysqli_real_escape_string($connexion, $_POST['acronyme']) : null;
    $num_tel = isset($_POST['num_tel']) ? mysqli_real_escape_string($connexion, $_POST['num_tel']) : null;
    $num_fax = isset($_POST['num_fax']) ? mysqli_real_escape_string($connexion, $_POST['num_fax']) : null;
    $email = isset($_POST['email']) ? mysqli_real_escape_string($connexion, $_POST['email']) : null;
    $site = isset($_POST['site']) ? mysqli_real_escape_string($connexion, $_POST['site']) : null;

    if ($id !== null && is_numeric($id) && $nom !== null) {
        $sqlUpdate = "UPDATE association SET nom='$nom', acronyme='$acronyme', num_tel='$num_tel', num_fax='$num_fax', email='$email', site='$site' WHERE id=$id;";
        $logUser = $_SESSION['email'];
        $logType = 'Modification';
        $sqlInsertLog = "INSERT INTO logs (utilisateur, type_log) VALUES ('$logUser','$logType')";
        mysqli_query($connexion, $sqlInsertLog);

        if (mysqli_query($connexion, $sqlUpdate)) {
            echo "<p>Mise à jour réussie.</p>";
        } else {
            echo "<p>Erreur lors de la mise à jour : " . mysqli_error($connexion) . "</p>";
        }
    } else {
        echo "<p>Identifiant ou nom invalide.</p>";
    }
} else {
    echo "<p>Mauvaise méthode de requête.</p>";
}

?>
