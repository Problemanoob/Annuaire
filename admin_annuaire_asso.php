<?php
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: connexion_admin.php");
    exit();
}
function deconnexion() {
    session_unset();
    session_destroy();
    header("Location: annuaire_asso.php");
    exit();
}
if (isset($_POST["deconnexion"])) {
    deconnexion();
}
/*************Connexion BDD */

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

/*************Connexion BDD */
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
<form method="post" action="">
    <input type="submit" name="deconnexion" value="Déconnexion" class="deconnexion-btn">
</form>
<?php
echo "<a class=\"redirect\" href=\"ajout_asso.php\">Ajouter</a>";
echo "<h2>Administration de l'annuaire des associations</h2>";
echo "<form method='post' action='admin_annuaire_asso.php'>";
echo "<table border='1'>";
echo "<tr>";
echo "<th></th>";
echo "<th>Nom</th>";
echo "<th>Acronyme</th>";
echo "<th>Numéro de téléphone</th>";
echo "<th>Numéro de fax</th>";
echo "<th>Email</th>";
echo "<th>Site Internet</th>";
echo "</tr>";

$sqlQuery = "SELECT * FROM association;";
$result = $connexion->query($sqlQuery);

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

echo "</table>";
echo "<input type='submit' name='delete' value='Supprimer sélectionnés'>";
echo "</form>";

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