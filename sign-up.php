<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/pwdFunctions.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}
?>
<?php 

	if (isset($_GET['accesscheck'])) {
	  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
	}
	
	$loginFormAction = $_SERVER['PHP_SELF'];
	$msg = "";
	$_curForm = 1;
    $MM_redirectLoginSuccess = "imagenes.php";
    $MM_redirectLoginFailed = "sign-up.php";
	// Check for error in calling the page
	if (isset($_GET['errmsg'])) {
		switch ($_GET['errmsg']) {
			case 9:
				$_formIntro = "Hay que registrarse para entrar en el sitio. <br/>";
				break;
			case 7:
				session_start();
				unset($_SESSION['auth']);
				unset($_SESSION['usuarioID']);
				$_SESSION = array();
				//session_destroy(); 
				$_formIntro = "Sesion terminada.<br/>";				
		}
	}
	// Check if is a log-in or sign-up
	$_FORMTYPE = 1;
	if (isset($_GET['frmtype'])) {
	  $_FORMTYPE = $_GET['frmtype'];
	}
	if (isset($_POST['formType'])) {
		$_FORMTYPE = $_POST['formType'];
		//$_FORMTYPE = 1;
	}
	 if (($_FORMTYPE == 2) ) {
	 $_formHead = "<h3>Formulario de Bienvenida</h3>";
	 $_formStyle = "<style type='text/css'> .hideType { display:block; } </style>";
	 $_formIntro .= "Por favor ingrese toda su informaci&oacute;n para inscribirse.";
	 //$_FORMTYPE = 2;
	// Sign-up type of form
	if (isset($_POST['nombre'])) {
	  $loginNombre=$_POST['nombre'];
	  $loginApeP=$_POST['apellidoP'];
	  $loginApeM=$_POST['apellidoM'];
	  $logineMail=$_POST['eMail'];
	  $loginePassword=$_POST['clave'];
	//  $MM_fldUserAuthorization = "";
	//  $MM_redirectLoginSuccess = "imagenes.php";
	//  $MM_redirectLoginFailed = "log-in.php";
	//  $MM_redirecttoReferrer = false;
	
	  // Convert password into hash to store
	  $loginHash = generatePwdHash($logineMail, $loginePassword);
	
	  mysql_select_db($database_ImagenIR, $ImagenIR);
	  
	  // Check for existing eMail in database
	  $LoginRS__query=sprintf("SELECT * FROM `Usuario` WHERE eMail = %s",
		GetSQLValueString($logineMail, "text"));
	
	  $LoginRS = mysql_query($LoginRS__query, $ImagenIR) or die(mysql_error());
	  
	  if (mysql_num_rows($LoginRS) != 0) {
		while ($row = mysql_fetch_assoc($LoginRS)) {
		$msg = "Ese eMail ya esta en uso por " . $row['nombre'] . 
		" " .$row['apellidoP'] . ".";
		}
	 } else {
		  $LoginRS__query=sprintf("INSERT INTO Usuario (nombre, apellidoP, apellidoM, email, hash) VALUES(%s,%s,%s,%s,%s)",
			GetSQLValueString($loginNombre, "text"),
			GetSQLValueString($loginApeP, "text"),
			GetSQLValueString($loginApeM, "text"),
			GetSQLValueString($logineMail, "text"),
			GetSQLValueString($loginHash, "text"));
		
		  $LoginRS = mysql_query($LoginRS__query, $ImagenIR) or die(mysql_error());
		  if ($LoginRS) {
			 session_start();
			 session_regenerate_id(true);
			$_SESSION["auth"] = true;
			$_SESSION['usuarioID'] = $loginRow['usuarioID'];
			 header("Location: " . $MM_redirectLoginSuccess );
		  }else {
			  	$_curForm = 2;
				$_formIntro = "El registro no fu&eacute; captado. Por favor int&eacute;ntelo de nuevo. <br/>";
		  }
	  }
	}
} else {
	
	 $_formHead = "<h3>Ingreso Clientes</h3>";
	 $_formIntro .= "Por favor ingrese su eMail y clave de acceso.";
	if (isset($_POST['eMail'])) {
	  $logineMail=$_POST['eMail'];
	  $loginPassword=$_POST['clave'];
	  
	  mysql_select_db($database_ImagenIR, $ImagenIR);
	  
	  $LoginRS__query=sprintf("SELECT usuarioID, nombre, apellidoP, hash FROM Usuario WHERE eMail=%s",
		GetSQLValueString($logineMail, "text"));
	
	  $LoginRS = mysql_query($LoginRS__query, $ImagenIR) or die(mysql_error());
	
	  $loginFoundUser = mysql_num_rows($LoginRS);
	   if ($loginFoundUser) {
			$loginRow = mysql_fetch_array($LoginRS);
			$retHash = $loginRow['hash'];
			$pwdTest = compareHashToPwd($loginPassword, $retHash);  
			 if ($pwdTest) {
				 session_start();
				$_SESSION["auth"] = true;
				$_SESSION['usuarioID'] = $loginRow['usuarioID'];
				$_SESSION['nombre'] = $loginRow['nombre'];
				$_SESSION['apellidoP'] = $loginRow['apellidoP'];
				 header("Location: " . $MM_redirectLoginSuccess );
			} else {
				 //header("Location: " . $MM_redirectLoginFailed );
				 $errorTxt = "Clave de accesso incorrecta. Asegure que el boton de may&uacute;sculas no est&eacute; presionado.";
			}
	   } else {
		   $errorTxt = "El eMail no se encuentra registrado.";
	   }
	 $msg = "<span class='warnMsg'>".$errorTxt."</span><br/>";  
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>imagenIR</title>
<script type="text/javascript" src="_includes/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="_includes/sign-in.js"></script>
<link href="_css/imagenes1.css" rel="stylesheet" type="text/css">
<?php echo $_formStyle; ?> 

</head>

<body>
<div id="bodyBackgr">
  <div class="containerSm">
<div class="header"> 
      		<div id="logo"></div>
            <div class="hedInfoBar"><span class="topBtn">Contacto</span></div>
      <!-- end .header --></div>
<div class="mainContentSm">
	<div class="spacer"></div>
  <div class="formLayout">
    <?php echo $_formHead; ?> 
    <div class="formBlock">
      <form id="loginVM" name="loginVM" method="POST" action="<?php echo $loginFormAction; ?>">
        <p class="intro"><?php echo $msg . $_formIntro; ?></p>
        	<input type="hidden" id="formType" name="formType" value="<?php echo $_curForm; ?>" />
        <div class="hideType">
          <label for="nombre">Nombres</label>
          <input type="text" name="nombre" id="nombre" /><br/>
          <label for="apellidoP">Apellido Paterno</label>
          <input type="text" name="apellidoP" id="apellidoP" /><br/>
          <label for="apellidoM">Apellido Materno</label>
          <input type="text" name="apellidoM" id="apellidoM" /><br/>
          </div>
        <label for="eMail">eMail</label>
        <input type="text" name="eMail" id="eMail" /><br/>
        <label for="contra">Clave</label>
        <input type="text" name="clave" id="clave" /><br/>
        <div class="submit">
          <input type="submit" value="Enviar" src="_images/1x1.gif" />
          </div>
        <br>
        </form>
      </div>
  </div>
	<div class="spacer"></div>
</div>
  </div>
  <div class="footer"></div>
</div>
</body>
</html>
<?php
mysql_free_result($LoginRS);
?>
