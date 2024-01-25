<?php
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = mysqli_real_escape_string($connexion, $_POST["nom"]);
    $prenom = mysqli_real_escape_string($connexion, $_POST["prenom"]);
    $email = mysqli_real_escape_string($connexion, $_POST["email"]);
    $mot_de_passe = mysqli_real_escape_string($connexion, $_POST["mot_de_passe"]);

    $lien_verification = md5(uniqid(rand(1,10000), true));

    $mot_de_passe_hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);

    $sqlInsert = "INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, etat_verification, lien_verification) VALUES ('$nom', '$prenom', '$email', '$mot_de_passe_hash', 'en attente', '$lien_verification')";

    if (mysqli_query($connexion, $sqlInsert)) {
        $sujet = "Vérification d'inscription";
        $message = "Cliquez sur le lien suivant pour vérifier votre inscription : 10.3.20.169/verification.php?lien=$lien_verification";
        $headers = "From: UCONORT@gers.fr";

        mail($email, $sujet, $message, $headers);

        header("Location: inscription_reussie.php");
        exit();
    } else {
        echo "Erreur lors de l'inscription : " . mysqli_error($connexion);
    }
}
?>