<?php
require ("../../bin/php/php-5.3.5/PEAR/PEAR/Ping.php");
require ("../../bin/php/php-5.3.5/PEAR/PEAR/Traceroute.php");
require ("connexion/connexionbdd.php");



//on vide les 2 variables qui vont contenir les problemes pdt le ping pour l'envoi du mail ou sms
//et initialisation variables
$mail_perte = "";
$mail_route = "";
$envoi_mail= "non";
$envoi_sms= "non";

$echecsms = 9999;
$as  = 9999;

//RECUPERATION DES PARAMETRE STOCKES DANS PING.CONF
$array_tmp = file('parametres/ping.conf');
foreach($array_tmp as $v)
{
	if ((substr(trim($v),0,1)!=';') && (substr_count($v,'=')>=1))
	{
		$pos = strpos($v, '=');
		$config[trim(substr($v,0,$pos))] = trim(substr($v, $pos+1));
	}
}
unset($array_tmp);

//RECUPERATION DES PARAMETRE STOCKES DANS ALERTE.CONF
$array_tmp = file('parametres/alerte.conf');
foreach($array_tmp as $v)
{
	if ((substr(trim($v),0,1)!=';') && (substr_count($v,'=')>=1))
	{
		$pos = strpos($v, '=');
		$alerte[trim(substr($v,0,$pos))] = trim(substr($v, $pos+1));
	}
}
unset($array_tmp);


//ON RECUPERE LES PARAMETRE STOCKE DANS LA TABLE OPERATEURS ET ON FAIT UNE BOUCLE AUTOUR DE L'OPERATEUR
$recuperation_adresse_ip = mysql_query("SELECT * FROM `stageebsd09`.`operateurs` ORDER by operateur DESC");
while($resultat=mysql_fetch_object($recuperation_adresse_ip))
{
	$operateur= $resultat->operateur;
	$ip= $resultat->ip;
	$ip2 = $resultat->ip2;
	$ping = Net_Ping::factory();

	if(PEAR::isError($ping)) {
		//SI IL Y A UNE ERREUR ON L'AFFICHE
		echo $ping->getMessage();
	} else {
		//SINON ON PING ET ON RECUPERE LE MESSAGE
		$ping->setArgs($config);
		$response = $ping->ping($ip);
			
		$avg  = $response->getAvg();
		$perte  = $response->getLoss();

		if (!isset($avg)) {
			//SI L'HOTE EST INJOIGNABLE, ON PREND EN PARAMETRE LA 2IEME APDRESSE IP ET ON EFFECTUE UN DEUXIEME PING
			$ping->setArgs($config);
			$response2 = $ping->ping($ip2);

			$avg  = $response2->getAvg();
			$perte  = $response2->getLoss();
			$ip = $ip2;
		}
		echo $ip."<br/>";
		echo "Moyenne : $avg ms"."<br />";
		echo "Perte : $perte en %"."<br />";
			
			
		//recuperation du nombre de ping pour calculer la moyenne
		$array_tmp = file('parametres/affiche_parametre.conf');
		foreach($array_tmp as $v)
		{
			if ((substr(trim($v),0,1)!=';') && (substr_count($v,'=')>=1))
			{//La ligne ne doit pas commencer par un ';' et doit contenir au moins un signe '='.
				$pos = strpos($v, '=');
				$para[trim(substr($v,0,$pos))] = trim(substr($v, $pos+1));
			}
		}
		unset($array_tmp);
		$limit = $para['dernier'];
		$calcul_x_ping = "";
		$calcul_x_perte = "";

		//ON RECUPERE LES X DERNIERE PING EN ACCORD AVEC LA LIMITE DONNEE PAR L'UTILISATEUR, ET ON CALCUL LA MOYENNE
		$recupe_x_ping = mysql_query("SELECT * FROM `stageebsd09`.$operateur ORDER BY id DESC LIMIT 0,$limit");
		while($resultatxping=mysql_fetch_object($recupe_x_ping))
		{

			$pingx = $resultatxping->moyenne;
			$calcul_x_ping = $calcul_x_ping + $pingx;
			$pertex = $resultatxping->perte;
			$calcul_x_perte = $calcul_x_perte + $pertex;

		}
		$num_rows = mysql_num_rows($recupe_x_ping);
		$moyenne = round($calcul_x_ping/$num_rows,1);
		$moyenne_perte = round($calcul_x_perte/$num_rows,1);

		//ON MODIFIE LA VALEUR DE LA MOYENNE POUR L'OPERATEUR DANS LA TABLE OPERATEURS, ET ON AJOUTE LES RESULTAT DU PING DANS LA TABLE DE L'OPERATEUR
		$requete = mysql_query("UPDATE `stageebsd09`.`operateurs` SET ping='$avg', ping_moy='$moyenne', perte='$perte', perte_moy='$moyenne_perte' WHERE operateur='".$operateur."'");
		$requete1 = mysql_query("INSERT INTO `stageebsd09`.$operateur (`id`, `moyenne`, `perte`) VALUES (NULL, '".$avg."', '".$perte."');");
	}

	$host = $ip;
	$teln = gethostbyname($host);

	$switch = '91.197.136.100'; // adresse du switch
	$port = 2605; //23 car nous faisons du telnet !
	$fp = fsockopen($switch, $port);
	if(!$fp) {
		echo 'La connexion telnet au switch '.$switch.' a échoué';
	}
	fwrite($fp, "954312\r\n");
	fwrite($fp, "show ip bgp $teln \r\n");

	$tableau = Array();
	$line = '';
	$i = '';

	stream_set_timeout($fp, 0, 250000); // en microsecondes, dépend de votre équipement $i = 1;
	while ($i<20) //nb max de lignes à récupérer
	{
		$i++;
		$line = fgets($fp, 1024);

		if(preg_match('#[0-9]{5}#', $line)){
			$as = $line;
		}
	}
	echo " ASroute complete :$as";
	echo '<br/>';
	$asroute =0;
	$pieces = explode(" ", $as);

	for($i=3;$i<count($pieces);$i++){
		$asroute .= trim(str_replace(',', '', $pieces[$i])).' ';
		if(strstr($pieces[$i], ',')){
			$i = count($pieces);
		}
	}

	//ON RECUPERE LA DERNIERE ROUTE POUR L'OPERATEUR, POUR LA COMPARER A CELLE SAUVEGARDE DANS LA TABLE
	$recup_route = mysql_query("SELECT route FROM operateurs WHERE operateur='".$operateur."'");
	$r_route =mysql_fetch_row($recup_route);
	$route = $r_route[0];
	echo "ancien : $route";
	echo '<br/>';
	echo '<br/>';
	echo $route.'<br/>';
	echo $asroute.'<br/>';

	$nouveau_echec_sms = $alerte['echec_avant_sms'];
	$add = $alerte['adresse_mail'];

	//---------------------------------------------------------------
	//PROCEDURE REMPLISSAGE DU MAIL ET SMS : SOIT LE LOSS EST TROP ELEVE SOIT LA ROUTE EST DIFFERENTE
	if (($perte >= $alerte['taux_echec'] || $route != $asroute )) {

		//---------------------------
		//on remplie les 2 variables pour écrire le mail
		if(($perte >= $alerte['taux_echec'])){
			$mail_perte .= "$operateur avec un ping de : $avg et une perte de : $perte.\r\n";
		}else{
			$mail_route .= "Ancienne route : $route, nouvelle route $asroute.\r\n";
		}
		//on sauvegarde la route dans la table apres l'avoir comparé a l'ancienne
		$requete = mysql_query("UPDATE `stageebsd09`.`operateurs` SET route='$asroute' WHERE operateur='".$operateur."'");
	}

	//-----------------------------
	//on recupere la valeur de la prochaine alerte dans la table et on la décrémente
	$requete2 = mysql_query("SELECT prochaine_alerte FROM operateurs WHERE operateur='".$operateur."'");
	$row = mysql_fetch_row($requete2);
	$transfert = $row[0]-1;
	if($transfert == 0){
		//si on a atteind 0, on envoi un mail
		$envoi_mail = "oui";
		//on remet a la valeur initial l'alerte mail
		$nouveau_transfert = $alerte['echec'];
		$requete3 = mysql_query("UPDATE operateurs SET prochaine_alerte='".$nouveau_transfert."' WHERE operateur='".$operateur."'");

		//on decrement le compte a rebours avant l'envoi du sms et on decrement echec_avant_sms si on a pas desactivé la varaible
		$requete4 = mysql_query("SELECT echec_avant_sms FROM operateurs WHERE operateur='".$operateur."'");
		$row = mysql_fetch_row($requete4);
		if(preg_match("#^([a-z])$#", $row[0])){
			$echecsms = $row[0]-1;
			$requete5 = mysql_query("UPDATE operateurs SET prochaine_alerte='".$echecsms."' WHERE operateur='".$operateur."'");
		}
	}else{
		//ON DECREMENTE LA VALEUR PROCHAINE_ALERTE DE L'OPERATEUR
		$requete6 = mysql_query("UPDATE operateurs SET prochaine_alerte='".$transfert."' WHERE operateur='".$operateur."'");
	}
	//----------------------------
	//Envoi d'un sms si echec_sms atteind 0
	if(($echecsms == 0)&& (preg_match("#^([a-z])$#", $echecsms))){
		$envoi_sms = "oui";
		//et on desactive l'envoi de sms en ne mettant pas une valeur numerique
		$requete = mysql_query("UPDATE operateurs SET echec_avant_sms='desactive' WHERE operateur='".$operateur."'");
	}
}

if($envoi_mail == "oui"){
	//email
	$smtp_server = fsockopen("smtp.free.fr", 25, $errno, $errstr, 30);
	if(!$smtp_server){exit;}
	
	fwrite($smtp_server, "HELO\r\n");
	fwrite($smtp_server, "MAIL FROM:<serveurping@ebsd.net>\r\n");
	fwrite($smtp_server, "RCPT TO:<$add>\r\n");
	fwrite($smtp_server, "DATA\r\n");
	fwrite($smtp_server, "From: Me <ping@ebsd.net>\r\n");
	fwrite($smtp_server, "Subject:  Sans objet\r\n");
	fwrite($smtp_server, "To: $add\r\n");
	fwrite($smtp_server, "\r\nProblemes rencontres pendant le ping :\r\n$test ?\r\n$mail_perte 		\r\n$mail_route             Me.\r\n");
	fwrite($smtp_server, ".\r\nQUIT\r\n");

	//affichage de ce qu'on a envoyé dans le mail/sms
	echo $mail_perte;
	echo '<br/>';
	echo $mail_route;
	echo '<br/>';
}

if($envoi_sms == "oui"){
	$user = $alerte['user'];
	$pass = $alerte['pass'];
	$sms = $alerte['adresse_sms'];

	//header('Location: http://www.smsextrapro.com/HttpSend/HttpSend.php?Login='.$user.'&Psw='.$pass.'&DestNum='.$user.'&Signature=Ping&Message='.$message.'&Type=1');
}
?>