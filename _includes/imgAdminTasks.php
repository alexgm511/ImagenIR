<?php require('auth_imgAdmin.php'); ?>
<?php require_once('imagenMySQL.php'); ?>
<?php require('functionsMySQL.php'); ?>
<?php require('pwdFunctions.php'); ?>

<?php 
	if (isset($_GET['accesscheck'])) {
	  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
	}
	session_start();
	if ($_SESSION['nivel'] != 2) {
		unset($_SESSION['auth']);
		unset($_SESSION['usuarioID']);
		$_SESSION = array();
		//session_destroy(); 
		$_formIntro = "Sesion terminada.<br/>";				
		header("Location: imgLogin.php?errmsg=8");
	}
?>
<?php 
		$_action = $_POST['imgTask'];
		switch ($_action) {
			case "newUser":
				if (empty($_POST['nombre']) || empty($_POST['apellidoP']) || empty($_POST['eMail']) || empty($_POST['clave'])) {
					echo "<div class='alert alert-danger'>Hay que llenar todos los campos.</div>";
				} else {					
					// Check for existing eMail in database
					$LoginRS__query=sprintf("SELECT * FROM `Usuario` WHERE eMail = %s",
						GetSQLValueString($_POST['eMail'], "text"));
			
					if ($row = mysqli_fetch_assoc(mysqli_query($imgLink, $LoginRS__query))) {
						echo "<div class='alert alert-danger'>Ese eMail ya esta en uso por " . $row['nombre'] . " " .$row['apellidoP'] . ".</div>";				
							
					} else {
						// console.log("We are ready to insert.");
						$logineMail = $_POST['eMail'];
						$loginPassword=$_POST['clave'];	
						// Convert password into hash to store
						$loginHash = generatePwdHash($logineMail, $loginPassword);
			
						$LoginRS__query=sprintf("INSERT INTO Usuario (nombre, apellidoP, apellidoM, nivel, eMail, hash) VALUES(%s,%s,%s,%s,%s,%s)",
							GetSQLValueString($_POST['nombre'], "text"),
							GetSQLValueString($_POST['apellidoP'], "text"),
							GetSQLValueString($_POST['apellidoM'], "text"),
							GetSQLValueString($_POST['nivel'], "int"),
							GetSQLValueString($_POST['eMail'], "text"),
							GetSQLValueString($loginHash, "text"));
								
						if (mysqli_query($imgLink, $LoginRS__query)) {
							// Add the first folder to the user
							$_newID = mysqli_insert_id($imgLink);
							
							$Carpeta_query=sprintf("INSERT INTO Carpetas (usuarioID, carpetaID, parent, pos, nombre, orden) VALUES(%s, 1, 0, 1, %s, 1)",
							//mysqli_insert_id($imgLink),
							$_newID,
							GetSQLValueString($_POST['apellidoP'], "text"));
							
							mysqli_query($imgLink, $Carpeta_query);
							
							echo "<div class='alert alert-success' data-newid='".$_newID."'>El usuario fue ingresado.</div>";
						}else {
							echo "<div class='alert-danger'>El registro no fu&eacute; captado. Por favor int&eacute;ntelo de nuevo. </div>";
						} // end of carpeta insert
					} // end of insert					
				}
				break; 
			// seconc case here
			case "delUser":
				$action__query=sprintf("DELETE FROM `Carpetas` WHERE usuarioID = %s",
				GetSQLValueString($_POST['usuarioID'], "int"));
				if (mysqli_query($imgLink, $action__query) === true) {
					$action__query=sprintf("DELETE FROM `Usuario` WHERE usuarioID = %s",
						GetSQLValueString($_POST['usuarioID'], "int"));
					if (mysqli_query($imgLink, $action__query) === true) {
						echo "<div class='alert alert-success'>El usuario fue eliminado.</div>";
					} else {
						echo "<div class='alert-danger'>Error al eliminar el usuario.</div>";
					}
				} else {
					echo "<div class='alert-danger'>Error al eliminar las carpetas relacionadas con el usuario.</div>";
				}
			break;				
			case "chgPwd":
				$loginPassword=$_POST['clave'];
				$logineMail=$_POST['eMail'];
				// Convert password into hash to store
				$loginHash = generatePwdHash($logineMail, $loginPassword);
				$action__query=sprintf("UPDATE `Usuario` SET hash = %s WHERE usuarioID = %s",
				GetSQLValueString($loginHash, "text"),
				GetSQLValueString($_POST['usuarioID'], "int"));
				if (mysqli_query($imgLink, $action__query) === true) {
					echo "<div class='alert alert-success'>El cambio se hizo con exito.</div>";
				} else {
					echo "<div class='alert-danger'>Error al hacer el cambio.</div>";
				}
				
			break;				
			case "editName":
				// Only add fields that have new info 
				$_gotVal = false;
				if (!empty($_POST['nombre'])) {
					$_action_text = sprintf("nombre = %s",
					GetSQLValueString($_POST['nombre'], "text"));
					$_gotVal = true;
				}
				if (!empty($_POST['apellidoP'])) {
					if ($_gotVal) { $_action_text .= ", "; } //add comma in between fields
					$_action_text .= sprintf("apellidoP = %s",
					GetSQLValueString($_POST['apellidoP'], "text"));
					$_gotVal = true;
				}
				if (!empty($_POST['apellidoM'])) {
					if ($_gotVal) { $_action_text .= ", "; } //add comma in between fields
					$_action_text .= sprintf("apellidoM = %s",
					GetSQLValueString($_POST['apellidoM'], "text"));
					$_gotVal = true;
				}
				if (!empty($_POST['nivel'])) {
					if ($_gotVal) { $_action_text .= ", "; } //add comma in between fields
					$_action_text .= sprintf("nivel = %s",
					GetSQLValueString($_POST['nivel'], "int"));
					$_gotVal = true;
				}
				// Only post if there was a field changed
				if ($_gotVal) {
					$_userID = GetSQLValueString($_POST['usuarioID'], "int");
					$action__query = "UPDATE `Usuario` SET "
							.$_action_text." WHERE usuarioID = ".$_userID;
							
					/*echo "<div class='alert-danger'>".$action__query."</div>";		
					$action__query=sprintf("UPDATE `Usuario` SET nombre = %s,
								apellidoP = %s, apellidoM = %s, nivel = %s
								WHERE usuarioID = %s", 
					GetSQLValueString($_POST['nombre'], "text"),
					GetSQLValueString($_POST['apellidoP'], "text"),
					GetSQLValueString($_POST['apellidoM'], "text"),
					GetSQLValueString($_POST['nivel'], "int"),
					GetSQLValueString($_POST['usuarioID'], "int")); */
					if (mysqli_query($imgLink, $action__query) === true) {
						echo "<div class='alert alert-success'>El cambio se hizo con exito.</div>";
					} else {
						echo "<div class='alert-danger'>Error al hacer el cambio.</div>";
					}
				} else {
					echo "<div class='alert-danger'>No hubo cambios que hacer.</div>";
				}
				
			break;				
		} // end of switch
//	} // end of isset
  

?>
