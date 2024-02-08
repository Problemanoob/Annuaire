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

// Fonction de déconnexion
function deconnexion() {
    session_unset();
    session_destroy();
    header("Location: annuaire_asso.php");
    exit();
}

// Traitement de la déconnexion si le formulaire est soumis
if (isset($_POST["deconnexion"])) {
    deconnexion();
}

// Vérification de la session pour déterminer si l'utilisateur a le droit de créer un compte
$emailAdmin = "SJOLY@gers.fr";
$emailAdmin2 = "GROBILLIARD@gers.fr";
$showCreationButton = ($_SESSION["email"] === $emailAdmin || $_SESSION["email"] === $emailAdmin2);

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

try {
    $mysqlConnection = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $user, $pwd);
} catch (PDOException $error) {
    echo 'Échec de la connexion : ' . $error->getMessage();
}
// HTML et affichage des boutons en fonction des autorisations
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
<form method="post" action="">
    <input type="submit" name="deconnexion" value="Déconnexion" class="deconnexion-btn">
</form>
<?php
if ($showCreationButton) {
    echo "<a href='inscription_admin.php' class='creation-btn'>Création de compte</a>";
}
echo "<br>";
echo "<a class='redirect' href='profil.php'>Profil</a>";
echo "<br>";
echo "<a class='redirect' href='ajout_asso.php'>Ajouter une association</a>";
echo "<h2>Administration de l'annuaire des associations</h2>";
echo "<form method='post' action='admin_annuaire_asso.php'>";
echo "<table border='1'>";
echo "<tr>";
echo "<th></th>";
echo "<th>Nom</th>";
echo "<th>Acronyme</th>";
echo "<th>Numéro de téléphone</th>";
echo "<th>Numéro de fax</th>";
echo "<th>Courriel</th>";
echo "<th>Site Internet</th>";
echo "</tr>";

// Récupération des données depuis la base de données
$sqlQuery = "SELECT * FROM association;";
$result = $connexion->query($sqlQuery);

// Affichage des données dans un tableau
while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><input type='checkbox' name='delete_ids[]' value='" . $row['id'] . "'></td>";
    echo "<td>" . $row['nom'] . "</td>";
    echo "<td>" . $row['acronyme'] . "</td>";
    echo "<td>" . $row['num_tel'] . "</td>";
    echo "<td>" . $row['num_fax'] . "</td>";
    echo "<td>" . $row['email'] . "</td>";
    echo "<td>" . $row['site'] . "</td>";
    echo "<td><a href='modifier_asso_form.php?id=" . $row['id'] . "'>Modifier</a></td>";
    echo "</tr>";
}

// Fin du tableau et bouton de suppression
echo "</table>";
echo "<input type='submit' name='delete' value='Supprimer sélectionnés'>";
echo "</form>";

// Suppression des enregistrements sélectionnés
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $deleteIds = isset($_POST['delete_ids']) ? $_POST['delete_ids'] : array();

    if (!empty($deleteIds)) {
        $deleteIdsStr = implode(",", $deleteIds);
        $sqlDelete = "DELETE FROM association WHERE id IN ($deleteIdsStr)";
        if (mysqli_query($connexion, $sqlDelete)) {
            echo "<p>Enregistrement supprimé avec succès.</p>";
            $logUser = $_SESSION['email'];
            $logType = 'Suppression';
            $sqlInsertLog = "INSERT INTO logs (utilisateur, type_log) VALUES ('$logUser','$logType')";
            mysqli_query($connexion, $sqlInsertLog);
        } else {
            echo "<p>Erreur lors de la suppression : " . mysqli_error($connexion) . "</p>";
        }
    } else {
        echo "<p>Aucun enregistrement sélectionné pour la suppression.</p>";
    }
}
?>