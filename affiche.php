<?php
session_start();
$utilisateur = 	$_SESSION['user'];
require("connexion/connexionbdd.php");

$requete=mysql_query("select * from utilisateurs where utilisateur=\"$utilisateur\"");
if(mysql_num_rows($requete)==0)
{
	header("Location:index.html");
}
$droit_membre=mysql_result($requete,0,"droit");
if ($droit_membre == "admin"){
	echo '<a href="administration.php">Administration</a>';
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" type="image/ico"
	href="http://www.ebsd.net/favicon.ico" />

<title>Page principale</title>
<style type="text/css" title="currentStyle">
@import "media/css/demo_page.css";
@import "media/css/demo_table.css";
</style>

<script language="javascript" type="text/javascript"
	src="media/js/jquery.js"></script>
<script type="text/javascript" language="javascript"
	src="media/js/jquery.dataTables.js"></script>
<script language="javascript" type="text/javascript"
	src="media/js/jquery.flot.js"></script>
<!--[if IE]><script language="javascript" type="text/javascript"
	src="media/js/excanvas.pack.js"></script><![endif]-->
	
<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {				
				$('#example').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"sAjaxSource": "server_processing.php"
				} );
			} );
		</script>
</head>
<body id="dt_example">
<div id="container"><?php 
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
?>

<table>
	<tr>
		<th>Nombre de ping a afficher : <?php echo $para['dernier']; ?></th>
	</tr>
	<tr>
		
		<th>
		<form method="post" action="parametres/modif_affiche.php"><input
			type="hidden" name="ancienne"
			value='<?php echo "dernier = ".$para['dernier'] ?>'?></input> <input
			type="hidden" name="parametre" value='<?php echo "dernier = "?>'></input>
		<input type="text" name="nouvelle"> <input type="submit"
			value="Envoyer"></form>
		</th>
	</tr>
</table>

<div class="full_width big"><i>EBSD</i> : historique de ping</div>
<h1>Historique</h1>
<div id="dynamic">
<table cellpadding="0" cellspacing="0" border="0" class="display"
	id="example">
	<thead>
		<tr>
			<th width="20%">Op&eacute;rateur</th>
			<th width="25%">Ping</th>
			<th width="25%">Ping (moy)</th>
			<th width="25%">Perte</th>
			<th width="15%">Pertes (moy)</th>
			<th width="15%">Routes</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<th width="20%">Op&eacute;rateur</th>
			<th width="25%">Ping</th>
			<th width="25%">Ping (moy)</th>
			<th width="25%">Perte</th>
			<th width="15%">Pertes (moy)</th>
			<th width="15%">Routes</th>
		</tr>
	</tfoot>
</table>
</div>

<table>
	<tr>

	<?php
	require ("connexion/connexionbdd.php");

	$requete = mysql_query("SELECT * FROM `operateurs` ORDER by `operateur`");
	while ($row = mysql_fetch_assoc($requete)) {
		echo'<th><a href="test.php?operateur='.$row['operateur'].'" class="load">'.$row['operateur'].'</a></th>';
	}
	?>
	</tr>
</table>

<script type="text/javascript">
$(document).ready(function () {  
  $("a.load")
  .click(function() {
  $("#myid").load(this.href);
    return false;
  });
});
</script>
<div id="myid"></div>
</div>
</body>
</html>
