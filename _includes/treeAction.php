<?php 
session_start();
$_loginPage = "imgLogin.php";
if(!isset($_SESSION['usuarioID'])) {
    header("Location: " . $_loginPage . "?errmsg=9" );
} 
?>
<?php require_once('imagenMySQL.php'); ?>
<?php require('functionsMySQL.php'); ?>
<?php 
	if (isset($_POST['action'])) {
		$_action = $_POST['action'];
		switch ($_action) {
			case "renameImg":
				$_id = (int) substr($_POST['id'],2);
				$action__query=sprintf("UPDATE `Imagenes` SET nombre = %s WHERE imagenID = %s",
				GetSQLValueString($_POST['nombre'], "text"),
				GetSQLValueString($_id, "int"));
				break;
			case "renameFld":
				$_id = (int) substr($_POST['id'],2);
				$action__query=sprintf("UPDATE `Carpetas` SET nombre = %s WHERE carpetaID = %s",
				GetSQLValueString($_POST['nombre'], "text"),
				GetSQLValueString($_id, "int"));
				break;				
			case "remove":
				$_id = (int) substr($_POST['id'],2);
				$action__query=sprintf("DELETE FROM `Carpetas` WHERE usuarioID = %s AND carpetaID = %s",
				GetSQLValueString($_POST['usuarioID'], "int"),
				GetSQLValueString($_id, "int"));
				break;				
			case "create":
				$_id = (int) substr($_POST['id'],2);
				$_parentId = (int) substr($_POST['parent'],2);
				$action__query=sprintf("INSERT INTO `Carpetas` (usuarioID, carpetaID, nombre, parent, pos) VALUES(%s,%s,%s,%s,%s)",
				GetSQLValueString($_POST['usuarioID'], "int"),
				GetSQLValueString($_id, "int"),
				GetSQLValueString($_POST['nombre'], "text"),
				GetSQLValueString($_parentId, "int"),
				GetSQLValueString($_POST['pos'], "int"));
				break;				
			case "moveFld":
				$_id = (int) substr($_POST['id'],2);
				$_parentId = (int) substr($_POST['parent'],2);
				$action__query=sprintf("UPDATE `Carpetas` SET parent = %s WHERE usuarioID = %s AND carpetaID = %s",
				GetSQLValueString($_parentId, "int"),
				GetSQLValueString($_POST['usuarioID'], "int"),
				GetSQLValueString($_id, "int"));
				break;				
			case "moveImg":
				$_id = (int) substr($_POST['id'],2);
				$_parentId = (int) substr($_POST['parent'],2);
				$action__query=sprintf("UPDATE `Imagenes` SET parent = %s WHERE usuarioID = %s AND imagenID = %s",
				GetSQLValueString($_parentId, "int"),
				GetSQLValueString($_POST['usuarioID'], "int"),
				GetSQLValueString($_id, "int"));
				break;				
		}		

		mysql_select_db($database_ImagenIR, $ImagenIR);
		$imgRS = mysql_query($action__query, $ImagenIR) or die(mysql_error());
		
		if ($_action == "create" || $_action == "moveFld") {
			$_orderArr = $_POST['ordenArr'];
			$_cnt = 0;
			foreach ($_orderArr as $o) {
				$_id = (int) substr($o,2);
				$_cnt++;
				$orden__query=sprintf("UPDATE `Carpetas` SET orden = %s WHERE usuarioID = %s AND carpetaID = %s",
				GetSQLValueString($_cnt, "int"),
				GetSQLValueString($_POST['usuarioID'], "int"),
				GetSQLValueString($_id, "int"));
				mysql_select_db($database_ImagenIR, $ImagenIR);
				$imgRS = mysql_query($orden__query, $ImagenIR) or die(mysql_error());
			}
		}

		if ($_action == "moveImg") {
			$_orderArr = $_POST['ordenImgArr'];
			$_cnt = 0;
			foreach ($_orderArr as $o) {
				$_id = (int) substr($o,2);
				$_cnt++;
				$orden__query=sprintf("UPDATE `Imagenes` SET pos = %s WHERE usuarioID = %s AND imagenID = %s",
				GetSQLValueString($_cnt, "int"),
				GetSQLValueString($_POST['usuarioID'], "int"),
				GetSQLValueString($_id, "int"));
				mysql_select_db($database_ImagenIR, $ImagenIR);
				$imgRS = mysql_query($orden__query, $ImagenIR) or die(mysql_error());
			}
		}
		
		echo "";
		  
	} else {
		echo "Error con la base de datos.";
	}
?>
