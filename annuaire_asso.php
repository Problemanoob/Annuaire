<?php
$hostname = "10.3.20.169";
$user = "user";
$pwd = ".NfMFY/ePKC/MX*7";
$database = "annuaire";
$connexion = mysqli_connect($hostname, $user, $pwd, $database);
mysqli_set_charset($connexion, "utf8");

try {
    $mysqlConnection = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $user, $pwd);
} catch (PDOException $error) {
    echo 'Échec de la connexion : ' . $error->getMessage();
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
<?php
echo "<div class='header'>
<a class='redirect' href='index.php'>Retour</a>
<form id='search-form' method='post'>";
echo "<label for='tri'>Trier par : </label>";
echo "<select name='tri' id='tri'>";
echo "<option value='nom'>Nom (A-Z)</option>";
echo "<option value='nom_desc'>Nom (Z-A)</option>";
echo "</select>";

echo "<label for='search'>Recherche : </label>";
echo "<input type='text' name='search' id='search' placeholder='Rechercher par nom, acronyme, numéro de téléphone, etc...'>";
echo "<input type='submit' value='Trier/Rechercher'>";
echo "</form>";
echo "<button class='login-btn' onclick=\"window.location.href='connexion_admin.php'\">Se connecter</button>";
echo "</div>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['tri'])) {
        $tri = $_POST['tri'];
        switch ($tri) {
            case 'nom':
                $sqlQuery = "SELECT nom,acronyme,num_tel,num_fax,email,site FROM association";
                break;
            case 'nom_desc':
                $sqlQuery = "SELECT nom,acronyme,num_tel,num_fax,email,site FROM association ORDER BY nom DESC";
                break;
            default:
                $sqlQuery = "SELECT nom,acronyme,num_tel,num_fax,email,site FROM association";
                break;
        }

        if (isset($_POST['search']) && !empty($_POST['search'])) {
            $searchTerm = $_POST['search'];
            $sqlQuery .= " WHERE nom LIKE '$searchTerm%' OR acronyme LIKE '$searchTerm%' OR num_tel LIKE '$searchTerm%' OR num_fax LIKE '$searchTerm%' OR email LIKE '$searchTerm%' OR site LIKE '$searchTerm%'";
        }

        $sqlQuery .= ";";
    }
} else {
    $sqlQuery = "SELECT nom,acronyme,num_tel,num_fax,email,site FROM association;";
}
?>

<script>
document.getElementById('search-form').addEventListener('input', function() {
    var searchTerm = document.getElementById('search').value;
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200) {
            document.getElementById('result-container').innerHTML = xhr.responseText;
        }
    };
    xhr.open('POST', 'update_results.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('search=' + searchTerm);
});
</script>

<div id="result-container">
<?php
$result = $connexion->query($sqlQuery);
$count = 0;
echo "<table border='1'>";
while ($row = $result->fetch_row()) {
    echo "<tr>";
    echo "<td><div class='asso'>
        Nom : $row[0]<br>Acronyme : $row[1]<br>Numéro de téléphone : $row[2]
        <br>Numéro de fax : $row[3]<br>Email : $row[4]<br>Site Internet : <a href='$row[5]' target='_blank'>$row[5]</a>";
    echo "</td>";
    echo "</tr>";
    $count++;
}
echo "</table>";
echo "Il y a $count associations répertoriées.";
?>
</div>