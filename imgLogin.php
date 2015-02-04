<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/pwdFunctions.php'); ?>
<?php require('_includes/functionsMySQL.php'); ?>
<?php 
	if (isset($_GET['accesscheck'])) {
	  $_SESSION['PrevUrl'] = $_GET['accesscheck'];
	}
	// check language or set English as default and get lang terms
	$_lang = "en";
	if (isset($_GET['lng'])) {
		$_lang = $_GET['lng'];
	}
	if ($_lang !== "en" && $_lang !== "es") { $_lang = "en"; }	
	$_langFile = json_decode(file_get_contents("lang/".$_lang.".json"), true);
		foreach ($_langFile as $key => $val){
			switch ($key) {
				case "otherLang":
					$_otherLang = $val;
					break;
				case "contact":
					$_contact = $val;
					break;
				case "login":
					$_login = $val;
					break;
				case "loginGreet":
					$_loginGreet = $val;
					break;
				case "enter":
					$_enter = $val;
					break;
				case "regError":
					$_regError = $val;
					break;
				case "supError":
					$_supError = $val;
					break;
				case "kwrdError":
					$_kwrdError = $val;
					break;
				case "eMailError":
					$_eMailError = $val;
					break;
				case "sesEnd":
					$_sesEnd = $val;
					break;
				case "noImgTxt":
					$_imagesCnt = $val;
					break;
				case "firstName":
					$_firstName = $val;
					break;
				case "lastName1":
					$_lastName1 = $val;
					break;
				case "lastName2":
					$_lastName2 = $val;
					break;
				case "password":
					$_password = $val;
					break;
			}
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
				$_formIntro = $_regError;
				break;
			case 8:
				$_formIntro = $_supError;
				break;
			case 7:
				session_start();
				unset($_SESSION['auth']);
				unset($_SESSION['usuarioID']);
				$_SESSION = array();
				//session_destroy(); 
				$_formIntro = $_sesEnd;				
		}
	}
	// Check if is a log-in or sign-up
	
	 $_formHead = $_login;
	 $_formIntro .= $_loginGreet;
	if (isset($_POST['eMail'])) {
	  $logineMail=$_POST['eMail'];
	  $loginPassword=$_POST['clave'];
	  
	  $LoginRS__query=sprintf("SELECT usuarioID, nombre, apellidoP, nivel, hash FROM Usuario WHERE eMail=%s AND activo = 1",
		GetSQLValueString($logineMail, "text"));
	
	  if ($loginRow = mysqli_fetch_assoc(mysqli_query($imgLink, $LoginRS__query))) {
			$retHash = $loginRow['hash'];
			$pwdTest = compareHashToPwd($loginPassword, $retHash);  
			 if ($pwdTest) {
				if (!isset($_SESSION)) { session_start(); }
				//session_start();
				$_SESSION["auth"] = true;
				$_SESSION['usuarioID'] = $loginRow['usuarioID'];
				$_SESSION['nombre'] = $loginRow['nombre'];
				$_SESSION['apellidoP'] = $loginRow['apellidoP'];
				$_SESSION['nivel'] = $loginRow['nivel'];
				$_SESSION['lang'] = $_lang;
				die("<script>location.href = '" . $MM_redirectLoginSuccess . "'</script>");
				// header("Location: " . $MM_redirectLoginSuccess );
			} else {
				 //header("Location: " . $MM_redirectLoginFailed );
				 $errorTxt = $_kwrdError;
			}
	   } else {
		   $errorTxt = $_eMailError;
	   }
	 $msg = '<span class="warnMsg">'.$errorTxt.'</span><br/>';  
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
<div class="navbar-wrapper">
      <!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
      <div class="container">

        <div class="navbar navbar-inverse navbar-static-top topNavegation" role="navigation">
         <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand imgBrand" href="index.html">imagen<strong>IR</strong></a>
          </div>
          
           <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav navbar-right">
              <li id="contacto"><a href="#"><?php echo $_contact; ?></a></li>
          </ul>
        </div><!--/.nav-collapse -->
    </div><!-- /.navbar -->

      </div> <!-- /.container -->
    </div>
    <div class="container">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
		
      <div class="panel panel-default">
        <div class="panel-heading">
		    <strong><?php echo $_formHead; ?> </strong>
      </div>
        <div class="panel-body">
          <form id="loginVM" name="loginVM" method="POST" action="<?php echo $loginFormAction; ?>">
			<p class="help-block"><?php echo $msg . $_formIntro; ?></p>			
        <div class="hideType">
          <input type="text" name="nombre" id="nombre" class="form-control" placeholder=<?php echo $_firstName; ?>>
          <input type="text" name="apellidoP" id="apellidoP" class="form-control" placeholder=<?php echo $_lastName1; ?>>
          <input type="text" name="apellidoM" id="apellidoM" class="form-control" placeholder=<?php echo $_lastName2; ?>>
          </div>
         <input type="text" name="eMail" id="eMail" class="form-control" placeholder="eMail" autofocus>
        <input type="password" name="clave" id="clave" class="form-control" placeholder=<?php echo $_password; ?>>
          <div class="form-group">
              <button type="submit" class="btn btn-default btn-primary btn-block"><?php echo $_enter; ?></button>
          </div>
        </form>
      </div>
  </div>
</div>
  </div>
</div>
</body>
</html>
