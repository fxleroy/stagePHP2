<?php
require ("../connexion/connexionbdd.php");

if( isset($_POST['ancienne']) && $_POST['nouvelle'] && $_POST['parametre'])
{
	$ancienne = $_POST['ancienne'];
	$parametre = $_POST['parametre'];
	$nouvelle = $_POST['nouvelle'];

	//lecture du fichier de configuration d'alerte
	$page = file_get_contents( 'alerte.conf' ) ;
	$page = str_replace( $ancienne,$parametre.$nouvelle, $page ) ;
	file_put_contents( 'alerte.conf', $page ) ;
	
	//remise du compteur d'alerte pour cheque opérateur au seuil maximal
	if(isset($_POST['echecmail'])){$requete = mysql_query("UPDATE `operateurs` SET `prochaine_alerte` = '$nouvelle'");};
	if(isset($_POST['echecsms'])){$requete = mysql_query("UPDATE `operateurs` SET `echec_avant_sms` = '$nouvelle'");};
	header('Location: ../administration.php');
}else {
	header('Location: ../administration.php');
}
?>