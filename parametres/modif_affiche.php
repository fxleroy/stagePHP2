<?php
require ("../connexion/connexionbdd.php");
if( isset($_POST['ancienne']) && $_POST['nouvelle'] && $_POST['parametre'])
{
	$ancienne = $_POST['ancienne'];
	$parametre = $_POST['parametre'];
	$nouvelle = $_POST['nouvelle'];

	$page = file_get_contents( 'affiche_parametre.conf' ) ;
	$page = str_replace( $ancienne,$parametre.$nouvelle, $page ) ;
	file_put_contents( 'affiche_parametre.conf', $page ) ;
	if (isset($_POST['echec'])){
		$requete = mysql_query("SELECT * FROM `operateurs` ");
		$requete = mysql_query("UPDATE `operateurs` SET `prochaine_alerte` = '$nouvelle'");
	}
	header('Location: ../affiche.php');

}else {
	header('Location: ../affiche.php');

}

?>