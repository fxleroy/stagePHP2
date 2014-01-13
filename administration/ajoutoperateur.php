<?php
require ("../connexion/connexionbdd.php");

//recuperation des parametres d'alerte dans un array $alerte
$array_tmp = file('../parametres/alerte.conf');
foreach($array_tmp as $v)
{
	if ((substr(trim($v),0,1)!=';') && (substr_count($v,'=')>=1))
	{
		$pos = strpos($v, '=');
		$alerte[trim(substr($v,0,$pos))] = trim(substr($v, $pos+1));
	}
}
unset($array_tmp);

//récupération des parametres dans alerte.conf et avec le POST
$transfert = $alerte['echec'];
$operateur = $_POST['operateur'];
$ip = $_POST['ip'];
$ip2 = $_POST['ip2'];

$requete=mysql_query("select * from operateurs where operateur=\"$operateur\"");

if(mysql_num_rows($requete)==0)
{
	$creation_table_operateur = mysql_query("CREATE TABLE `$operateur` (`id` INT NOT NULL AUTO_INCREMENT, `moyenne` INT NOT NULL, `perte` INT NOT NULL, `time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`id`)) ENGINE = MyISAM;");
	$ajout_operateur = mysql_query("INSERT  INTO `operateurs` (`id`, `operateur`, `ip`, `ip2`, `ping`,`ping_moy`, `perte`, `perte_moy`, `route`, `prochaine_alerte`)
													   VALUES (NULL, '".$operateur."', '".$ip."', '".$ip2."', NULL, NULL, NULL, NULL, NULL, '".$transfert."')");
}else{
	$update_operateur = mysql_query("UPDATE `operateurs` SET ip='$ip', ip2='$ip2' WHERE operateur = '".$operateur."'");
}
header('Location: ../administration.php');
?>