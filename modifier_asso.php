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