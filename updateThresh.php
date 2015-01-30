<?php 
session_start();
$_loginPage = "imgLogin.php";
if(!isset($_SESSION['usuarioID'])) {
    header("Location: " . $_loginPage . "?errmsg=9" );
} 
?>
<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/functionsMySQL.php'); ?>
<?php 
	//mysql_select_db($database_ImagenIR, $ImagenIR);
		// get image ID and update Threshold field.
	if (isset($_POST['temp_base'])) {
		$_usuario = $_POST['usuarioID'];
		$_imgID = $_POST['imgID'];
		$_newVal = $_POST['temp_base']; 
		
		$threshUpdate_query = sprintf("UPDATE Imagenes SET temp_base = %s WHERE usuarioID = %s AND imagenID = %s",
			GetSQLValueString($_newVal, "double"),
			GetSQLValueString($_usuario, "int"),
			GetSQLValueString($_imgID, "int"));
			
		$_retString = $threshUpdate_query;
			
		if (mysqli_query($imgLink, $threshUpdate_query) === TRUE) {
			$_retString = "Threshold cambiado.";
		} else {
			$_retString = "Error en el cambio de Threshold.";
			echo mysqli_error($imgLink);
		}
	} else {
		$_retString = "No hay Threshold para guardar.";
	}
	//$_retString = "Threshold cambiado.";
	echo $_retString;
?>