<?php
include("identifiants.php");
mysql_connect($adresse, $nom, $motdepasse) or die ('Erreur de conexion a la base');
mysql_select_db($database) or die  ('Erreur de connection a la table');
?>