<?php
session_start();

$hostname = "10.3.20.169";
$user = "user";
$pwd = ".NfMFY/ePKC/MX*7";
$database = "annuaire";
$connexion = mysqli_connect($hostname, $user, $pwd, $database);
mysqli_set_charset($connexion, "utf8");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($connexion, $_POST["email"]);
    $mot_de_passe = mysqli_real_escape_string($connexion, $_POST["mot_de_passe"]);

    $sqlQuery = "SELECT * FROM utilisateur WHERE email = '$email'";
    $result = mysqli_query($connexion, $sqlQuery);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        if (password_verify($mot_de_passe, $row['mdp'])) {
            $_SESSION["email"] = $email;
            header("Location: admin_annuaire_asso.php");
            exit();
        } else {
            $erreur_message = "Les informations d'identifications ne correspondent pas. Réessayez.";
        }
    } else {
        $erreur_message = "Les informations d'identifications ne correspondent pas. Réessayez.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire de Connexion</title>
</head>
<body>

<h2>Connexion</h2>

<?php
if (isset($erreur_message)) {
    echo "<p style='color: red;'>$erreur_message</p>";
}
?>

<form method="post" action="">
    <label for="email">Email :</label>
    <input type="email" name="email" required><br>

    <label for="mot_de_passe">Mot de passe :</label>
    <input type="password" name="mot_de_passe" required><br>

    <input type="submit" value="Se connecter">
</form>
<button class='login-btn' onclick="window.location.href='inscription_admin.php'">Toujours pas de compte ? Créez-vous en un !</button>
</body>
</html>