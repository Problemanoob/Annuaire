<?php
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: connexion_admin.php");
    exit();
}
$hostname = "10.3.20.169";
$user = "admin";
$pwd = "Za.m-P8EXNooX9)W";
$database = "annuaire";
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