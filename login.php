<?php
session_start();
require("connexion/connexionbdd.php");

//RECUPERATION DES PARAMETRE POOSTES DANS INDEX.HTML
$pseudo_membre = $_POST['pseudo_membre'];
$passe_membre = md5($_POST['passe_membre']);

$requete=mysql_query("select * from utilisateurs where utilisateur=\"$pseudo_membre\" and mdp=\"$passe_membre\"");
if(mysql_num_rows($requete)==0)
{
	header("Location:index.html");
}
else
{	
	//PÄSSAGE DES PARAMETRE DANS LES SESSIONS
	$_SESSION['user'] = $pseudo_membre;
	header("Location:affiche.php");
}
?>