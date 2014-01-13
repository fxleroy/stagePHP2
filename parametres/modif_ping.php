<?php
$ancienne = $_POST['ancienne'];
$nouvelle = $_POST['nouvelle'];
$parametre = $_POST['parametre'];
$val = $_POST['val'];

echo $val;
if(empty($nouvelle)){
	echo "nouvelle vide, on desactive";
	$page = file_get_contents( 'ping.conf' ) ;
	$page = str_replace( $parametre.$ancienne,"; ".$parametre, $page ) ;
	file_put_contents('ping.conf', $page );
	header('Location: ../administration.php');
	exit();
}elseif(($ancienne!="vide") && ($val!="vide")){
	echo "on modifie : ".$ancienne;
	$page = file_get_contents( 'ping.conf' ) ;
	$page = str_replace( $parametre.$ancienne,$parametre.$nouvelle, $page ) ;
	file_put_contents('ping.conf', $page );
	header('Location: ../administration.php');
}else{
	echo "on active";
	$page = file_get_contents( 'ping.conf' ) ;
	$page = str_replace( "; ".$parametre,$parametre.$nouvelle, $page ) ;
	file_put_contents('ping.conf', $page );
	header('Location: ../administration.php');
}
?>