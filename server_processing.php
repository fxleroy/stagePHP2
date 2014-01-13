<?php
//page appellé par affiche.php
//genere les donnée a afficher


require ("connexion/connexionbdd.php");

$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
	$sLimit = "LIMIT ".mysql_real_escape_string( $_GET['iDisplayStart'] ).", ".
	mysql_real_escape_string( $_GET['iDisplayLength'] );
}

if ( isset( $_GET['iSortCol_0'] ) )
{
	$sOrder = "ORDER BY  ";
	for ( $i=0 ; $i<mysql_real_escape_string( $_GET['iSortingCols'] ) ; $i++ )
	{
		$sOrder .= fnColumnToField(mysql_real_escape_string( $_GET['iSortCol_'.$i] ))."
			 	".mysql_real_escape_string( $_GET['iSortDir_'.$i] ) .", ";
	}
	$sOrder = substr_replace( $sOrder, "", -2 );
}

$sWhere = "";
if ( $_GET['sSearch'] != "" )
{
	$sWhere = "WHERE operateur LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ".
		                "ping LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ".
						"ping_moy LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ".
		                "perte LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ".
						"perte_moy LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ".
		                "route LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%'";
}

$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS id, operateur, ping, ping_moy, perte, perte_moy, route
		FROM   operateurs
		$sWhere
		$sOrder
		$sLimit
	";
		$rResult = mysql_query( $sQuery) or die(mysql_error());

		$sQuery = "
		SELECT FOUND_ROWS()
	";
		$rResultFilterTotal = mysql_query( $sQuery) or die(mysql_error());
		$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
		$iFilteredTotal = $aResultFilterTotal[0];

		$sQuery = "
		SELECT COUNT(id)
		FROM   operateurs
	";
		$rResultTotal = mysql_query( $sQuery) or die(mysql_error());
		$aResultTotal = mysql_fetch_array($rResultTotal);
		$iTotal = $aResultTotal[0];

		$sOutput = '{';
		$sOutput .= '"sEcho": '.$_GET['sEcho'].', ';
		$sOutput .= '"iTotalRecords": '.$iTotal.', ';
		$sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
		$sOutput .= '"aaData": [ ';
		while ( $aRow = mysql_fetch_array( $rResult ) )
		{
			$sOutput .= "[";

			$sOutput .= '"'.addslashes($aRow['operateur']).'",';
			$sOutput .= '"'.addslashes($aRow['ping']).'",';
			$sOutput .= '"'.addslashes($aRow['ping_moy']).'",';
			if ( $aRow['perte'] == "0" )
			$sOutput .= '"-",';
			else
			$sOutput .= '"'.addslashes($aRow['perte']).'",';
			$sOutput .= '"'.addslashes($aRow['perte_moy']).'",';
			if ( $aRow['route'] == "0" )
			$sOutput .= '"-",';
			else
			$sOutput .= '"'.addslashes($aRow['route']).'"';
			$sOutput .= "],";
		}
		$sOutput = substr_replace( $sOutput, "", -1 );
		$sOutput .= '] }';

		echo $sOutput;

		function fnColumnToField( $i )
		{
			if ( $i == 0 )
			return "operateur";
			else if ( $i == 3 )
			return "ping";
			else if ( $i == 4 )
			return "ping_moy";
			else if ( $i == 5 )
			return "perte";
			else if ( $i == 6 )
			return "perte_moy";
			else if ( $i == 7 )
			return "route";
		}
		?>