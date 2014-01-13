<?php
require ("../connexion/connexionbdd.php");

$utilisateur = $_POST['utilisateur'];
$mdp = md5($_POST['mdp']);
$droit = $_POST['droit'];

$requete=mysql_query("select * from utilisateurs where utilisateur=\"$utilisateur\"");

if(mysql_num_rows($requete)==0)
{
	$ajout_utilisateur = mysql_query("INSERT  INTO `stageebsd09`.`utilisateurs` (`utilisateur`, `mdp`, `droit`) VALUES ('".$utilisateur."', '".$mdp."', '".$droit."')");
}else{
	$update_user = mysql_query("UPDATE `stageebsd09`.`utilisateurs` SET mdp='$mdp', droit='$droit' WHERE utilisateur = '".$utilisateur."'");
}
header('Location: ../administration.php');
?>