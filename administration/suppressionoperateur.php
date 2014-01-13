<?php
require ("../connexion/connexionbdd.php");

$operateur = $_GET["operateur"] ;
$suppression_table_operateur = mysql_query("DROP TABLE $operateur");
$suppression_operateur = mysql_query("DELETE FROM operateurs WHERE operateur='$operateur'");

header('Location: ../administration.php');
?>