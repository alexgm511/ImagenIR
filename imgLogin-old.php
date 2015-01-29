<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/pwdFunctions.php'); ?>
<?php require('_includes/functionsMySQL.php'); ?>

<?php 
	if (isset($_GET['accesscheck'])) {
	  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
	}
	$_formIntro = "";
	$loginFormAction = $_SERVER['PHP_SELF'];
	$msg = "";
    $MM_redirectLoginSuccess = "img1-5-0.php";
    $MM_redirectLoginFailed = "imgLogin.php";
	// Check for error in calling the page
	if (isset($_GET['errmsg'])) {
		switch ($_GET['errmsg']) {
			case 9:
				$_formIntro = "Hay que registrarse para entrar en el sitio. <br/>";
				break;
			case 8:
				$_formIntro = "Solo supervisores pueden a&ntilde;adir usuarios. <br/>";
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
	
	 $_formHead = "Ingreso Clientes";
	 $_formIntro .= "Por favor ingrese su eMail y clave de acceso.";
	if (isset($_POST['eMail'])) {
	  $logineMail=$_POST['eMail'];
	  $loginPassword=$_POST['clave'];
	  
	  $LoginRS__query=sprintf("SELECT usuarioID, nombre, apellidoP, nivel, hash FROM Usuario WHERE eMail=%s AND activo = 1",
		GetSQLValueString($logineMail, "text"));
	
	  if ($loginRow = mysqli_fetch_assoc(mysqli_query($imgLink, $LoginRS__query))) {
			$retHash = $loginRow['hash'];
			$pwdTest = compareHashToPwd($loginPassword, $retHash);  
			 if ($pwdTest) {
				 session_start();
				$_SESSION["auth"] = true;
				$_SESSION['usuarioID'] = $loginRow['usuarioID'];
				$_SESSION['nombre'] = $loginRow['nombre'];
				$_SESSION['apellidoP'] = $loginRow['apellidoP'];
				$_SESSION['nivel'] = $loginRow['nivel'];
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

?>

<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>imagenIR</title>
<script type="text/javascript" src="_includes/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="_includes/sign-in.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<link type="text/css" rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
<link href="_css/imgLogin.css" rel="stylesheet" type="text/css">

</head>

<body>

    <div class="container">
    <div class="row">
        <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 bodyBackgr">.
		<nav class="navbar navbar-inverse topNav" role="navigation">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex2-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">imagen<strong>IR</strong></a>
        </div>
        <div class="collapse navbar-collapse navbar-ex2-collapse">
          <button type="button" class="btn btn-default btn-xs navbar-btn pull-right btnContacto">Contacto</button>
        </div>
      </nav>
      <div class="panel panel-default">
        <div class="panel-heading">
		    <strong><?php echo $_formHead; ?> </strong>
      </div>
        <div class="panel-body">
          <form id="loginVM" name="loginVM" method="POST" action="<?php echo $loginFormAction; ?>">
			<p class="help-block"><?php echo $msg . $_formIntro; ?></p>			
        <div class="hideType">
          <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre" autofocus>
          <input type="text" name="apellidoP" id="apellidoP" class="form-control" placeholder="Apellido Paterno" autofocus>
          <input type="text" name="apellidoM" id="apellidoM" class="form-control" placeholder="Apellido Materno" autofocus>
          </div>
         <input type="text" name="eMail" id="eMail" class="form-control" placeholder="eMail" autofocus>
        <input type="password" name="clave" id="clave" class="form-control" placeholder="Clave">
          <div class="form-group">
              <button type="submit" class="btn btn-default btn-primary btn-block">Enviar</button>
          </div>
        </form>
      </div>
  </div>
</div>
  </div>
</div>
</body>
</html>
