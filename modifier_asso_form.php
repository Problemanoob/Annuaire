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

echo "<a class=\"redirect\" href=\"index.php\">Retour</a>";

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($id !== null && is_numeric($id)) {
    $sqlQuery = "SELECT * FROM association WHERE id = $id;";
    $result = $connexion->query($sqlQuery);

    if ($result->num_rows > 0) {
        $association = $result->fetch_assoc();

        echo "<h2>Modifier l'association</h2>";
        echo "<form method='post' action='modifier_asso_process.php'>";
        echo "Nom: <input type='text' name='nom' value='" . $association['nom'] . "' required><br>";
        echo "Acronyme: <input type='text' name='acronyme' value='" . $association['acronyme'] . "'><br>";
        echo "Numéro de téléphone: <input type='text' name='num_tel' value='" . $association['num_tel'] . "'><br>";
        echo "Numéro de fax: <input type='text' name='num_fax' value='" . $association['num_fax'] . "'><br>";
        echo "Email: <input type='text' name='email' value='" . $association['email'] . "'><br>";
        echo "Site Internet: <input type='text' name='site' value='" . $association['site'] . "'><br>";
        echo "<input type='hidden' name='id' value='" . $association['id'] . "'>";
        echo "<input type='submit' value='Modifier'>";
        echo "</form>";
    } else {
        echo "<p>Association non trouvée.</p>";
    }
} else {
    echo "<p>Identifiant invalide.</p>";
}

?>