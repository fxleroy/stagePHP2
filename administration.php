<?php
session_start();
$utilisateur = 	$_SESSION['user'];
require("connexion/connexionbdd.php");

$requete=mysql_query("select * from utilisateurs where utilisateur=\"$utilisateur\"");
$droit_membre=mysql_result($requete,0,"droit");

if(mysql_num_rows($requete)==0 OR ($droit_membre !== "admin"))
{
	header("Location:index.html");
}
$droit_membre=mysql_result($requete,0,"droit");
echo '<a href="affiche.php">Page principale<a>';
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="media/css/style_admin.css"
	media="screen" />
</head>
<body>

<?php
require ("connexion/connexionbdd.php");

//tableau des operateurs
$requete = mysql_query("SELECT * FROM `operateurs` ");
?>
<script src="media/js/form.js" type="text/javascript"></script>
<table>
	<tr>
		<table width="520">
			<caption>PAGE d'ADMINISTRATION</caption>
			<tr>
				<td colspan="2"></td>
			</tr>
		</table>
		<div></div>
		<table width="520" border="0" cellpadding="0" cellspacing="0">
			<tr onclick="closeDiv('q5');">
				<td width="20" height="20"
					style="background-image: url('media/images/splitter_left.gif')"></td>
				<td style="background-image: url('media/images/splitter_mid.gif')">
				<div class="pagebreak"><label>Operateurs &nbsp;</label></div>
				</td>
				<td id="td_q5" width="20"
					style="background-image: url('media/images/splitter_right_hide.gif')"></td>
			</tr>
			<tr>
				<td height="2"></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<div id="div_q5" style="display: none">
		<table width="520" cellpadding="5" cellspacing="0">
			<tr>
				<th><?php
				require ("connexion/connexionbdd.php");

				//tableau des operateurs
				$requete = mysql_query("SELECT * FROM `operateurs` ");
				?>

				<table class="montableau">
					<thead>
						<tr>
							<th>Opérateurs</th>
							<th>IP</th>
							<th>IP de secours</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					<?php
					while ($row = mysql_fetch_assoc($requete)) {
						echo'
			<tr>      
				<th class="operateur">'.$row['operateur'].'</th>       
				<td class="ip">'.$row['ip'].'</th>  
				<td class="ip2">'.$row['ip2'].'</th>       
				<td class="suppression"><a href="administration/suppressionoperateur.php?operateur='.$row['operateur'].'"><img src="media/images/croix-supprimer.gif" BORDER="0"></a></th>
			</tr>';
					}
					?>
					</tbody>
				</table>


				</th>
				<th>
				<form id="h_droit" method="post"
					action="administration/ajoutoperateur.php">
				<table class="montableau">
					<caption>Ajout/modification</caption>
					<tbody>
						<tr>
							<th>Nom</th>
							<td><input type="text" name="operateur"></td>
						</tr>
						<tr>
							<th>IP</th>
							<td><input type="text" name="ip"></td>
						</tr>
						<tr>
							<th>IP2</th>
							<td><input type="text" name="ip2"></td>
						</tr>
						<tr>
							<th colspan="2"><input type="submit" value="Envoyer"></th>
						</tr>
					</tbody>
				</table>
				</form>
				</th>
			</tr>
		</table>
		</div>
		<table width="520" border="0" cellpadding="0" cellspacing="0">
			<tr onclick="closeDiv('q13');">
				<td width="20" height="20"
					style="background-image: url('media/images/splitter_left.gif')"></td>
				<td style="background-image: url('media/images/splitter_mid.gif')">
				<div class="pagebreak"><label>Parametres d'alerte&nbsp;</label></div>
				</td>
				<td id="td_q13" width="20"
					style="background-image: url('media//images/splitter_right_hide.gif')"></td>
			</tr>
			<tr>
				<td height="2"></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<div id="div_q13" style="display: none">
		<table width="520" cellpadding="5" cellspacing="0">
			<tr>
				<th><?php
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
				?>
				<table class="montableau">
					<thead>
						<tr>
							<th></th>
							<th>Ancien</th>
							<th>Nouveau</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<th>Nombre d'echecs successifs</th>
							<td><?php if(isset($config1['echec'])){echo $config1['echec'] ;}else {echo"parametre non défini";} ?>
							</td>
							<td>
							<form method=post action="parametres/modif_alerte.php"><input
								type=hidden name="ancienne"
								value="<?php echo "echec = ".$config1['echec'] ?>"><input
								type=hidden name="echecmail" value=""><input
								type=hidden name="parametre"
								value="<?php echo "echec = "?>"></input><input
								type=text size="15" name="nouvelle"><input type="submit"
								value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Nombre de mails avant SMS</th>
							<td><?php if(isset($config1['echec_avant_sms'])){echo $config1['echec_avant_sms'] ;}else {echo"parametre non défini";} ?>
							</td>
							<td>
							<form method=post action="parametres/modif_alerte.php"><input
								type=hidden name="echecsms" value=""><input
								type=hidden name="ancienne"
								value="<?php echo "echec_avant_sms = ".$config1['echec_avant_sms'] ?>"><input
								type=hidden name="parametre"
								value="<?php echo "echec_avant_sms = "?>"></input><input
								type=text size="15" name="nouvelle"><input type="submit"
								value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>% d'échec autorisé</th>
							<td><?php if(isset($config1['taux_echec'])){echo $config1['taux_echec'] ;}else {echo"parametre non défini";} ?>
							</td>
							<td>
							<form method=post action="parametres/modif_alerte.php"><input
								type=hidden name="ancienne"
								value="<?php echo "taux_echec = ".$config1['taux_echec'] ?>"><input
								type=hidden name="parametre"
								value="<?php echo "taux_echec = "?>"></input><input
								type=text size="15" name="nouvelle"><input type="submit"
								value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Adresse mail</th>
							<td><?php if(isset($config1['adresse_mail'])){echo $config1['adresse_mail'] ;}else {echo"parametre non défini";} ?>
							</td>
							<td>
							<form method=post action="parametres/modif_alerte.php"><input
								type=hidden name="ancienne"
								value="<?php echo "adresse_mail = ".$config1['adresse_mail'] ?>"><input
								type=hidden name="parametre"
								value="<?php echo "adresse_mail = "?>"></input><input
								type=text size="15" name="nouvelle"><input type="submit"
								value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Adresse sms</th>
							<td><?php if(isset($config1['adresse_sms'])){echo $config1['adresse_sms'] ;}else {echo"parametre non défini";} ?>
							</td>
							<td>
							<form method=post action="parametres/modif_alerte.php"><input
								type=hidden name="ancienne"
								value="<?php echo "adresse_sms = ".$config1['adresse_sms'] ?>"><input
								type=hidden name="parametre"
								value="<?php echo "adresse_sms = "?>"></input><input
								type=text size="15" name="nouvelle"><input type="submit"
								value="Envoyer"></form>
							</td>
						</tr>
					</tbody>
				</table>
				</th>
			</tr>
		</table>
		</div>
		<table width="520" border="0" cellpadding="0" cellspacing="0">
			<tr onclick="closeDiv('q14');">
				<td width="20" height="20"
					style="background-image: url('media/images/splitter_left.gif')"></td>
				<td style="background-image: url('media/images/splitter_mid.gif')">
				<div class="pagebreak"><label>Ping et utilisateurs&nbsp;</label></div>
				</td>
				<td id="td_q14" width="20"
					style="background-image: url('media/images/splitter_right_hide.gif')"></td>
			</tr>
			<tr>
				<td height="2"></td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<div id="div_q14" style="display: none">
		<table width="520" cellpadding="5" cellspacing="0">
			<table class="montableau">
				<tr>
					<th><?php
					$array_tmp = file('parametres/ping.conf');
					foreach($array_tmp as $v)
					{
						if ((substr(trim($v),0,1)!=';') && (substr_count($v,'=')>=1))
						{//La ligne ne doit pas commencer par un ';' et doit contenir au moins un signe '='.
							$pos = strpos($v, '=');
							$config[trim(substr($v,0,$pos))] = trim(substr($v, $pos+1));
						}
					}
					unset($array_tmp);
					?>
					<table id="parametre">
						<caption>Parametres Ping</caption>
						<thead>
							<tr>
								<th></th>
								<th>Ancien</th>
								<th>Nouveau</th>
							</tr>
						</thead>
						<tr>
							<th>Timeout</th>
							<td><?php if(isset($config['timeout'])){echo $config['timeout'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['timeout'] ?&gt;"> <input
								type="hidden" name="parametre"
								value='&lt;?php echo "timeout = "?&gt;'> <input type="hidden"
								name="val"
								value="&lt;?php if(isset($config['timeout'])){echo $config['timeout'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Iface</th>
							<td><?php if(isset($config['iface'])){echo $config['iface'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['iface'] ?&gt;"> <input
								type="hidden" name="parametre"
								value='&lt;?php echo "iface = "?&gt;'> <input type="hidden"
								name="val"
								value="&lt;?php if(isset($config['iface'])){echo $config['iface'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>TTL</th>
							<td><?php if(isset($config['ttl'])){echo $config['ttl'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['ttl'] ?&gt;"> <input type="hidden"
								name="parametre" value='&lt;?php echo "ttl = "?&gt;'> <input
								type="hidden" name="val"
								value="&lt;?php if(isset($config['ttl'])){echo $config['ttl'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Count</th>
							<td><?php if(isset($config['count'])){echo $config['count'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['count'] ?&gt;"> <input
								type="hidden" name="parametre"
								value='&lt;?php echo "count = "?&gt;'> <input type="hidden"
								name="val"
								value="&lt;?php if(isset($config['count'])){echo $config['count'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Quiet</th>
							<td><?php if(isset($config['quiet'])){echo $config['quiet'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['quiet'] ?&gt;"> <input
								type="hidden" name="parametre"
								value='&lt;?php echo "quiet = "?&gt;'> <input type="hidden"
								name="val"
								value="&lt;?php if(isset($config['quiet'])){echo $config['quiet'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Size</th>
							<td><?php if(isset($config['size'])){echo $config['size'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['size'] ?&gt;"> <input
								type="hidden" name="parametre"
								value='&lt;?php echo "size = "?&gt;'> <input type="hidden"
								name="val"
								value="&lt;?php if(isset($config['size'])){echo $config['size'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Seq</th>
							<td><?php if(isset($config['seq'])){echo $config['seq'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['seq'] ?&gt;"> <input type="hidden"
								name="parametre" value='&lt;?php echo "seq = "?&gt;'> <input
								type="hidden" name="val"
								value="&lt;?php if(isset($config['seq'])){echo $config['seq'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
						<tr>
							<th>Deadline</th>
							<td><?php if(isset($config['deadline'])){echo $config['deadline'] ;}else {echo"parametre non défini"; $val="vide"; } ?>
							</td>
							<td>
							<form method="post" action="parametres/modif_ping.php"><input
								type="hidden" name="ancienne"
								value="&lt;?php echo $config['deadline'] ?&gt;"> <input
								type="hidden" name="parametre"
								value='&lt;?php echo "deadline = "?&gt;'> <input type="hidden"
								name="val"
								value="&lt;?php if(isset($config['deadline'])){echo $config['deadline'] ;}else {echo $val;} ?&gt;">
							<input type="text" size="15" name="nouvelle"> <input
								type="submit" value="Envoyer"></form>
							</td>
						</tr>
					</table>
					</th>
					<th>
					<table border="1">

						<tr>
							<th>
							<table class="montableau">
								<caption>Utilisateurs</caption>
								<thead>
									<tr>
										<th>Pseudo</th>
										<th>Droit</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
								<?php
								$requete = mysql_query("SELECT * FROM `utilisateurs` ");
								while ($row = mysql_fetch_assoc($requete)) {
									echo'
			<tr>      
				<th class="utilisateur">'.$row['utilisateur'].'</th>       
				<td class="droit">'.$row['droit'].'</th>   
				<td class="suppression"><a href="administration/suppressionutilisateur.php?utilisateur='.$row['utilisateur'].'">
				<img src="media/images/croix-supprimer.gif" BORDER="0"></a></th>
			</tr>';
								}
								?>
								</tbody>
							</table>
							</th>
						</tr>
						<tr>
							<th>
							<form method="post" action="administration/ajoututilisateur.php">
							<table class="montableau">
								<caption>Ajout modification</caption>
								<tbody>
									<tr>
										<td>Nom</td>
										<td><input type="text" size="14" name="utilisateur"></td>
									</tr>
									<tr>
										<td>Mdp</td>
										<td><input type="text" size="14" name="mdp"></td>
									</tr>
									<tr>
										<td>Droits</td>
										<td><select name="droit" />
											<option value="admin">Administrateur</option>
											<option value="user">Utilisateur</option>
										</select></td>
									</tr>
									<tr>
										<td colspan="2"><input type="submit" value="Envoyer"></td>
									</tr>
								</tbody>
							</table>
							</form>
							</th>
						</tr>
					</table>
					</th>
				</tr>
			</table>
		</table>
		</div>
		<div></div>
		<form action=""></form>
		<td></td>
		<td class="midright" width="10">&nbsp;&nbsp;&nbsp;</td>
	</tr>
	<tr>
		<td class="bottomleft" width="10" height="10">&nbsp;</td>
		<td class="bottommid">&nbsp;</td>
		<td class="bottomright" width="10" height="10">&nbsp;</td>
	</tr>
</table>
<script type="text/javascript">
	validate("q_form_92630853017");
</script>

<?php 
	$requete = mysql_query("SELECT * FROM `operateurs` WHERE echec_avant_sms='desactive'");
	$num_rows = mysql_num_rows($requete);
?>
	<input type="button" name="sauverudumonde" 
<?php
if($num_rows==0){echo "value=\"Ton heure n'est pas encore venue\" disabled=\"disabled\"";}else{echo "value=\"Au secours\"";}
?>
onclick="self.location.href='sauveur.php'">


	</body>
</html>