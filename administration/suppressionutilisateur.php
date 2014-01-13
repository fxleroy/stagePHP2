<?php
require ("../connexion/connexionbdd.php");

$utilisateur = $_GET["utilisateur"] ;
$suppression_utilisateur = mysql_query("DELETE FROM utilisateurs WHERE utilisateur='$utilisateur'");

header('Location: ../administration.php');
?>