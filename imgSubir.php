<?php require('_includes/auth_admin.php'); ?>
<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/functionsMySQL.php'); ?>

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
	$_FORMTYPE = 2;
	 if (($_FORMTYPE == 2) ) {
	 $_formHead = "<h3>Subir Im&aacute;genes</h3>";
	 $_formStyle = "<style type='text/css'> .hideType { display:block; } .formLayout label {width: 18em;} </style>";
	 $_formIntro .= "Por favor ingrese la informaci&oacute;n sobre la im&aacute;gen.";
	 //$_FORMTYPE = 2;
	// Sign-up type of form
	if (isset($_POST['titulo'])) {
		$imgTitulo=$_POST['titulo'];
		$imgFecha=date('Y-m-d H:i:s',strtotime($_POST['fecha']));
		$IR_img_url="IRimagenes/".$imgTitulo.".jpg";
		$IR_img_url_chica="IRimagenes/".$imgTitulo."_tn.jpg";
		$IR_data="IRdata/".$imgTitulo.".csv";
		if($_POST['img_url'] == 'yes'){
			$foto = preg_replace('IR','DC', $imgTitulo);
			$img_url="DCimagenes/".$foto.".jpg";
			$img_url_chica="DCimagenes/".$foto."_tn.jpg";
		} else {
    		$img_url= null;
			$img_url_chica=null;
		}
		$notas = (trim($_POST['notas']) == "") ? null: $_POST['notas'];
		$img_max = (trim($_POST['img_max']) == "") ? null: $_POST['img_max'];
		$img_min = (trim($_POST['img_min']) == "") ? null: $_POST['img_min'];
		$atm_temp = (trim($_POST['atm_temp']) == "") ? null: $_POST['atm_temp'];
		$rel_humedad = (trim($_POST['rel_humedad']) == "") ? null: $_POST['rel_humedad'];
		$transmision = (trim($_POST['transmision']) == "") ? null: $_POST['transmision'];
		$refl_ap_temp = (trim($_POST['refl_ap_temp']) == "") ? null: $_POST['refl_ap_temp'];
		$distancia = (trim($_POST['distancia']) == "") ? null: $_POST['distancia'];
		$emisiv = (trim($_POST['emisiv']) == "") ? null: $_POST['emisiv'];
		$opt_ext_temp = (trim($_POST['opt_ext_temp']) == "") ? null: $_POST['opt_ext_temp'];
		$opt_ext_trans = (trim($_POST['opt_ext_trans']) == "") ? null: $_POST['opt_ext_trans'];
		$cambio = date("Y-m-d H:i:s");
		
		$usuario = $_SESSION['usuarioID'];
	
		$LoginRS__query=sprintf("INSERT INTO Imagenes (usuarioID, titulo, fecha, IR_img_url, IR_img_url_chica, IR_data, img_url, img_url_chica, notas, img_max, img_min, atm_temp, rel_humedad, transmision, refl_ap_temp, distancia, emisiv, opt_ext_temp, opt_ext_trans, cambio) VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)",
			GetSQLValueString($usuario, "int"),
			GetSQLValueString($imgTitulo, "text"),
			GetSQLValueString($imgFecha, "date"),
			GetSQLValueString($IR_img_url, "text"),
			GetSQLValueString($IR_img_url_chica, "text"),
			GetSQLValueString($IR_data, "text"),
			GetSQLValueString($img_url, "text"),
			GetSQLValueString($img_url_chica, "text"),
			GetSQLValueString($notas, "text"),
			GetSQLValueString($img_max, "long"),
			GetSQLValueString($img_min, "long"),
			GetSQLValueString($atm_temp, "int"),
			GetSQLValueString($rel_humedad, "long"),
			GetSQLValueString($transmision, "long"),
			GetSQLValueString($refl_ap_temp, "int"),
			GetSQLValueString($distancia, "int"),
			GetSQLValueString($emisiv, "long"),
			GetSQLValueString($opt_ext_temp, "int"),
			GetSQLValueString($opt_ext_trans, "int"),
			GetSQLValueString($cambio, "date"));
		echo $LoginRS__query;
		  $LoginRS = mysql_query($LoginRS__query, $ImagenIR) or die(mysql_error());
		  if (!$LoginRS) {
			  	$_curForm = 2;
				$_formIntro = "El registro no fu&eacute; captado. Por favor int&eacute;ntelo de nuevo. <br/>";
		  }
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
  <div class="containerMed">
<div class="header"> 
      		<div id="logo"></div>
            <div class="hedInfoBar"><span class="topBtn">Contacto</span></div>
      <!-- end .header --></div>
<div class="mainContentSm">
	<div class="spacer"></div>
  <div class="formLayout" style="width:450px">
    <?php echo $_formHead; ?> 
    <div class="formBlock" style="width:430px">
      <form id="loginVM" name="loginVM" method="POST" action="<?php echo $loginFormAction; ?>">
        <p class="intro"><?php echo $msg . $_formIntro; ?></p>
        	<input type="hidden" id="formType" name="formType" value="<?php echo $_curForm; ?>" />
        <label for="titulo">Im&aacute;gen infra-roja</label>
        <input type="text" name="titulo" id="titulo" /><br/>
        <label for="fecha">Fecha</label>
        <input type="text" name="fecha" id="fecha" /><br/>
        <label for="img_url">Foto asociada</label>
        <input type="checkbox" name="img_url" id="img_url" value="yes" /><br/>
        <label for="notas">Notas</label>
        <input type="text" name="notas" id="notas" /><br/>
        <label for="img_max">Temperatura m&aacute;xima</label>
        <input type="text" name="img_max" id="img_max" /><br/>
        <label for="img_min">Temperatura m&iacute;nima</label>
        <input type="text" name="img_min" id="img_min" /><br/>
        <label for="atm_temp">Temperatura atmosf&eacute;rica</label>
        <input type="text" name="atm_temp" id="atm_temp" /><br/>
        <label for="rel_humedad">Humedad relativa</label>
        <input type="text" name="rel_humedad" id="rel_humedad" /><br/>
        <label for="transmision">Transmisi&oacute;n</label>
        <input type="text" name="transmision" id="transmision" /><br/>
        <label for="refl_ap_temp">Temperatura aparente reflejada</label>
        <input type="text" name="refl_ap_temp" id="refl_ap_temp" /><br/>
        <label for="distancia">Distancia</label>
        <input type="text" name="distancia" id="distancia" /><br/>
        <label for="emisiv">Emisividad</label>
        <input type="text" name="emisiv" id="emisiv" /><br/>
        <label for="opt_ext_temp">opt_ext_temp</label>
        <input type="text" name="opt_ext_temp" id="opt_ext_temp" /><br/>
        <label for="opt_ext_trans">opt_ext_trans</label>
        <input type="text" name="opt_ext_trans" id="opt_ext_trans" /><br/>
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
