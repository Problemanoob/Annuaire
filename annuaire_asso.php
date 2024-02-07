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

// Configuration de la connexion à la base de données
$hostname = $_ENV['HOSTNAME'];
$user = $_ENV['DB_USER'];
$pwd = $_ENV['DB_PWD_USER'];
$database = $_ENV['DATABASE'];

// Connexion à la base de données avec mysqli
$connexion = mysqli_connect($hostname, $user, $pwd, $database);

// Vérification de la connexion
if (!$connexion) {
    die("La connexion a échoué : " . mysqli_connect_error());
}

// Configuration de l'encodage des caractères
mysqli_set_charset($connexion, "utf8");

// Connexion à la base de données avec PDO
try {
    $mysqlConnection = new PDO('mysql:host=' . $hostname . ';dbname=' . $database, $user, $pwd);
} catch (PDOException $error) {
    echo 'Échec de la connexion : ' . $error->getMessage();
}
?>
<!-- Affichage d'une image et inclusion de styles et scripts -->
<div class="columnone">
    <img class="logoimage" src="logoimage.png"/>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@1/css/pico.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/autocomplete.js/dist/autocomplete.min.css">
<script src="https://cdn.jsdelivr.net/npm/autocomplete.js/dist/autocomplete.min.js"></script>

<style>
    .body {
  width: 100%;
}

.notfound {
  display: none;
}

#search {
  @apply p-1;
  @apply border-2;
  @apply border-solid;
  @apply border-gray-300;
  @apply w-95;
  @apply font-sans;
}

.styled-table {
  @apply border-collapse;
  @apply my-4;
  @apply text-sm;
  @apply font-sans;
  @apply w-full;
  @apply mx-auto;
  @apply shadow-md;
}

.styled-table thead tr {
  @apply bg-primary;
  @apply text-white;
  @apply text-left;
}

.styled-table th,
.styled-table td {
  @apply p-3;
}

.styled-table tbody tr {
  @apply border-b;
  @apply border-solid;
  @apply border-gray-300;
}

.styled-table tbody tr:nth-of-type(even) {
  @apply bg-gray-100;
}

.styled-table tbody tr:last-of-type {
  @apply border-b-2;
  @apply border-solid;
  @apply border-primary;
}

.styled-table tbody tr.active-row {
  @apply font-bold;
  @apply text-success;
}

.styled-table tbody tr:hover {
  @apply bg-gray-400;
}

td a {
  @apply no-underline;
  @apply text-black;
}

.logoimage {
  @apply w-150;
  @apply h-auto;
  @apply pl-23;
  @apply align-middle;
}

.columnone {
  @apply float-left;
  @apply w-20;
  @apply p-10;
  @apply h-100;
  @apply align-middle;
}

.columntwo {
  @apply float-left;
  @apply w-60;
  @apply p-10;
  @apply h-100;
  @apply align-middle;
}

.columnthree {
  @apply float-left;
  @apply w-auto;
  @apply p-10;
  @apply h-100;
  @apply align-middle;
}

.row:after {
  @apply clear-both;
  @apply content;
  @apply table;
}

#column-content {
  @apply relative;
}

@media screen and (max-width: 600px) {
  .column {
    @apply w-85;
  }
}

.search-criteria,
.hidden-fields {
  @apply hidden;
}

.adv-search-collapsible {
  @apply mt-5;
  @apply text-white;
  @apply cursor-pointer;
  @apply p-2;
  @apply w-30;
  @apply border-none;
  @apply text-center;
  @apply outline-none;
  @apply bg-primary;
  @apply font-bold;
}

.active,
.adv-search-collapsible:hover {
  @apply bg-gray-300;
}

.adv-search-content {
  @apply max-h-0;
  @apply w-full;
  @apply overflow-hidden;
  @apply transition;
  @apply duration-200;
  @apply bg-gray-100;
  @apply block;
}

.adv-search-collapsible:after {
  @apply content;
  @apply '\02795';
  @apply font-size-13;
  @apply text-white;
  @apply float-right;
  @apply ml-5;
}

.active:after {
  @apply content;
  @apply "\2796";
}

.adv-input-row {
  @apply w-full;
  @apply mx-auto;
  @apply pt-1;
  @apply pl-1;
  @apply inline-block;
  @apply align-middle;
}

.adv-search-input {
  @apply w-70;
  @apply mx-auto;
}

.adv-search-footer {
  @apply mx-auto;
  @apply w-100;
  @apply p-10;
}

.main-content {
  @apply mx-auto;
  @apply w-100;
  @apply p-10;
}

.search-container {
  @apply w-60;
  @apply mx-auto;
}

.result-container {
  @apply w-full;
}

#clean_up,
#search:disabled {
  @apply mt-5;
  @apply text-white;
  @apply cursor-pointer;
  @apply p-5;
  @apply w-full;
  @apply h-full;
  @apply border-none;
  @apply text-center;
  @apply outline-none;
  @apply font-bold;
}

#clean_up:disabled {
  @apply bg-gray-400;
  @apply cursor-not-allowed;
}
</style>

<?php
// Affichage du formulaire de recherche et des boutons
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

// Traitement du formulaire de recherche et tri
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

<!-- Script AJAX pour mettre à jour les résultats de la recherche -->
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

<!-- Conteneur pour afficher les résultats de la recherche -->
<div id="result-container">
    <?php
    // Exécution de la requête SQL et affichage des résultats
    $result = $connexion->query($sqlQuery);
    $count = 0;
    echo "<table border='1'>";
    while ($row = $result->fetch_row()) {
        echo "<tr>";
        echo "<td><div class='asso'>
        Nom : $row[0]<br>Acronyme : $row[1]<br>Numéro de téléphone : $row[2]
        <br>Numéro de fax : $row[3]<br>Courriel : $row[4]<br>Site Internet : <a href='$row[5]' target='_blank'>$row[5]</a>";
        echo "</td>";
        echo "</tr>";
        $count++;
    }
    echo "</table>";
    echo "Il y a $count associations répertoriées.";
    ?>
</div>

<!-- Script pour activer l'autocomplétion -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    var searchInput = document.getElementById('search');
    
    var autocomplete = new Autocomplete(searchInput, {
        search: function(input, done) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var suggestions = JSON.parse(xhr.responseText);
                    done(suggestions);
                }
            };
            
            xhr.open('GET', 'update_results.php?suggest=' + input, true);
            xhr.send();
        }
    });

    searchInput.addEventListener('change', function() {
        var searchTerm = this.value;
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
});
</script>