<?php
// Démarrage de la session
session_start();

// Inclusion de l'autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Chemin vers le fichier .env
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

// Connexion à la base de données avec mysqli
$hostname = $_ENV['HOSTNAME'];
$user = $_ENV['DB_SUPER_USER'];
$pwd = $_ENV['DB_PWD_SUPUSER'];
$database = $_ENV['DATABASE'];
$connexion = mysqli_connect($hostname, $user, $pwd, $database);
mysqli_set_charset($connexion, "utf8");

// Connexion à la base de données avec PDO
try {
    $mysqlConnection = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $user, $pwd);
} catch (PDOException $error) {
    echo 'Échec de la connexion : ' . $error->getMessage();
}

// Affichage des boutons et du formulaire pour ajouter une nouvelle association
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

// Traitement du formulaire si soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['nom'])) {
        // Récupération des données du formulaire
        $nom = $_POST['nom'];
        $acronyme = $_POST['acronyme'];
        $num_tel = $_POST['num_tel'];
        $num_fax = $_POST['num_fax'];
        $email = $_POST['email'];
        $site = $_POST['site'];

        // Remplacement des champs vides par '/'
        $acronyme = empty($acronyme) ? '/' : $acronyme;
        $num_tel = empty($num_tel) ? '/' : $num_tel;
        $num_fax = empty($num_fax) ? '/' : $num_fax;
        $email = empty($email) ? '/' : $email;
        $site = empty($site) ? '/' : $site;

        // Requête d'insertion dans la base de données
        $sqlInsert = "INSERT INTO association (nom, acronyme, num_tel, num_fax, email, site) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = mysqli_prepare($connexion, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'ssssss', $nom, $acronyme, $num_tel, $num_fax, $email, $site);

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Affichage du succès et enregistrement d'un log
        echo "<p>Enregistrement ajouté avec succès.</p>";

        $logUser = $_SESSION["email"];
        $logType = 'Ajout';
        $sqlInsertLog = "INSERT INTO logs (utilisateur, type_log) VALUES (?, ?)";
        
        $stmtLog = mysqli_prepare($connexion, $sqlInsertLog);
        mysqli_stmt_bind_param($stmtLog, 'ss', $logUser, $logType);
        mysqli_stmt_execute($stmtLog);
        mysqli_stmt_close($stmtLog);
    } else {
        // Affichage d'une erreur si le champ 'Nom' est vide
        echo "<p>Le champ 'Nom' est obligatoire. Veuillez le remplir.</p>";
    }
}

// Affichage de la liste des associations
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