<?php 
session_start();
$_loginPage = "sign-up.php";
if(!isset($_SESSION['usuarioID'])) {
    header("Location: " . $_loginPage . "?errmsg=9" );
} 
?>
<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/functionsMySQL.php'); ?>
<?php 
	mysql_select_db($database_ImagenIR, $ImagenIR);
	if (!empty($_GET)) {
		// each point is included in URL query line as pt0, pt1, pt2, etc.
		// while stmt tests to the last number included.
		$_ctr = 0;
		$_pt = "pt{$_ctr}";
		$_arPoint = array();
		$_first = true;
		while (isset($_GET[$_pt])) {
			$_arPoint = explode(',', $_GET[$_pt]);
			if ($_first) {  // delete all points to insert new ones
				$tempSpotDel_query = sprintf("DELETE FROM Puntos WHERE imagenID = %s",
				GetSQLValueString($_arPoint[0], "int"));
				$result = mysql_query($tempSpotDel_query, $ImagenIR) or die(mysql_error());
				$_first = false;
			}
			
			$tempSpot_query = sprintf("INSERT INTO Puntos(imagenID, puntoID, posX, posY, temp) VALUES (%s,%s,%s,%s,%s)",
			GetSQLValueString($_arPoint[0], "int"),
			GetSQLValueString($_arPoint[1], "int"),
			GetSQLValueString($_arPoint[2], "int"),
			GetSQLValueString($_arPoint[3], "int"),
			GetSQLValueString($_arPoint[4], "double"));
			$result = mysql_query($tempSpot_query, $ImagenIR) or die(mysql_error());
			$_ctr++;
			$_pt = "pt{$_ctr}";
		}
	} else {
		$_retString = "No points to be stored.";
	}
	$_retString = "{$_ctr} puntos guardados.";
	echo $_retString;
?>