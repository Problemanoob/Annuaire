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

echo "<h2>Modifier une association</h2>";
echo "<table border='1'>";
$sqlQuery = "SELECT * FROM association;";
$result = $connexion->query($sqlQuery);

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><div class='asso'>
        Nom : " . $row['nom'] . "<br>
        Acronyme : " . $row['acronyme'] . "<br>
        Numéro de téléphone : " . $row['num_tel'] . "<br>
        Numéro de fax : " . $row['num_fax'] . "<br>
        Email : " . $row['email'] . "<br>
        Site Internet : " . $row['site'] . "<br>";
    echo "<a href='modifier_asso_form.php?id=" . $row['id'] . "'>Modifier</a>";
    echo "</div></td>";
    echo "</tr>";
}

echo "</table>";
?>