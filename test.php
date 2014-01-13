<?php
// page appelé par affiche.php


date_default_timezone_set('Europe/Paris');
//récupération des parametres du fichier affiche_parametre.conf
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

$nombre = $para['dernier']; // -> nombre d'occurences demandé
//$nombre = 50;
$operateur = $_GET["operateur"]; // operateur demandé
//$operateur = "free";

//selection des données a afficher dans le graphique JQuery
require ("connexion/connexionbdd.php");
$requete = mysql_query("SELECT * FROM `$operateur` ORDER BY `$operateur`.`id` DESC LIMIT 0,$nombre");
$varping = "[";

//on converti les dates de ping en timestamp unix et mise en forme
while ($row = mysql_fetch_assoc($requete)) {
	$conversion = strtotime($row['time']);
	$conversion += 7200;
	$conversion *= 1000;
	$moyenne = $row['moyenne'];
	$varping = $varping .' '.'['. $conversion .','. $moyenne. ']'.', ';
};	$varping = $varping . ']';


//On se connecte à la base de donnée pour récupérer tous les résultats des pertes
require ("connexion/connexionbdd.php");
$requete = mysql_query("SELECT * FROM `$operateur` ORDER BY `$operateur`.`id` DESC LIMIT 0,$nombre");
$varperte = "[";

//on converti les dates de ping en timestamp unix et mise en forme pour l'utilisation avec flot
while ($row = mysql_fetch_assoc($requete)) {
	$conversion = strtotime($row['time']);
	$conversion += 7200;
	$conversion *= 1000;
	$perte = $row['perte'];
	$varperte = $varperte .' '.'['. $conversion .','. $perte. ']'.', ';
};  $varperte = $varperte . ']';
?>

<body>
<h1>Historique du ping pour le serveur <span class="operateur"><?php echo "$operateur" ?>
</span></h1>

<div id="placeholder" style="width: 600px; height: 300px;"></div>

<p>Un peu de blabla</p>

<!-- script pour arricher le graphique Flot lorsque l'on clique sur l'operateur -->
<script language="javascript" type="text/javascript">
$(function () {
	
var Ping = <?php echo $varping ?>;
var Pertes = <?php echo $varperte ?>; 

    $.plot($("#placeholder"),
           [ { data: Ping, label: "Temps en ms" },
             { data: Pertes, label: "Pourcentage de perte", yaxis: 2 }],
           { xaxis: { mode: 'time' },
             yaxis: { min: 0 },
             y2axis: { tickFormatter: function (v, axis) { return v.toFixed(axis.tickDecimals) +"%" }},
             legend: { position: 'sw' } });
});
</script>
</body>
</html>
