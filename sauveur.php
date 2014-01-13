<?php
require ("connexion/connexionbdd.php");
$array_tmp = file('parametres/alerte.conf');
foreach($array_tmp as $v)
{
	if ((substr(trim($v),0,1)!=';') && (substr_count($v,'=')>=1))
	{//La ligne ne doit pas commencer par un ';' et doit contenir au moins un signe '='.
		$pos = strpos($v, '=');
		$config1[trim(substr($v,0,$pos))] = trim(substr($v, $pos+1));
	}
}
unset($array_tmp);

echo $config1['echec_avant_sms'];
$transfert = $config1['echec_avant_sms'];

$requete = mysql_query("UPDATE operateurs SET echec_avant_sms='".$transfert."' WHERE echec_avant_sms='desactive'");
header('Location: administration.php');
?>