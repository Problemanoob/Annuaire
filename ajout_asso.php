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

echo "<a class=\"redirect\" href=\"admin_annuaire_asso.php\">Retour</a>";
echo "<div style='width: 100%;text-align: center;'>";
echo "<h2>Ajouter une nouvelle association</h2>";
echo "<form method='post' action='ajout_asso.php' style='width: 60%; margin: 0 auto;'>";
echo "Nom: <input type='text' name='nom' required><br>";
echo "Acronyme: <input type='text' name='acronyme'><br>";
echo "Numéro de téléphone: <input type='text' name='num_tel'><br>";
echo "Numéro de fax: <input type='text' name='num_fax'><br>";
echo "Email: <input type='email' name='email'><br>";
echo "Site Internet: <input type='text' name='site'><br>";
echo "<input type='submit' value='Ajouter'>";
echo "</form>";
echo "</div>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['nom'])) {
        $nom = $_POST['nom'];
        $acronyme = $_POST['acronyme'];
        $num_tel = $_POST['num_tel'];
        $num_fax = $_POST['num_fax'];
        $email = $_POST['email'];
        $site = $_POST['site'];

        $acronyme = empty($acronyme) ? '/' : $acronyme;
        $num_tel = empty($num_tel) ? '/' : $num_tel;
        $num_fax = empty($num_fax) ? '/' : $num_fax;
        $email = empty($email) ? '/' : $email;
        $site = empty($site) ? '/' : $site;

        $sqlInsert = "INSERT INTO association (nom, acronyme, num_tel, num_fax, email, site) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($connexion, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'ssssss', $nom, $acronyme, $num_tel, $num_fax, $email, $site);

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "<p>Enregistrement ajouté avec succès.</p>";

        $logUser = $_SESSION["email"];
        $logType = 'Ajout';
        $sqlInsertLog = "INSERT INTO logs (utilisateur, type_log) VALUES (?, ?)";
        
        $stmtLog = mysqli_prepare($connexion, $sqlInsertLog);
        mysqli_stmt_bind_param($stmtLog, 'ss', $logUser, $logType);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    } else {
        echo "<p>Le champ 'Nom' est obligatoire. Veuillez le remplir.</p>";
    }
}

echo "<h2>Liste des associations</h2>";
echo "<table border='1'>";
$sqlQuery = "SELECT nom,acronyme,num_tel,num_fax,email,site FROM association;";
$result = $connexion->query($sqlQuery);

while ($row = $result->fetch_row()) {
    echo "<tr>";
    echo "<td><div class='asso'>
        Nom : $row[0]<br>Acronyme : $row[1]<br>Numéro de téléphone : $row[2]
        <br>Numéro de fax : $row[3]<br>Email : $row[4]<br>Site Internet : $row[5]";
    echo "</td>";
    echo "</tr>";
}
echo "</table>";
?>