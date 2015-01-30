<?php require('_includes/auth_imgAdmin.php'); ?>
<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/functionsMySQL.php'); ?>

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
	
  $_formHead = "Usuarios de ImagenIR";
  $_tableBody = "";
	// Return a list of all Usuarios and number of images for each one in database
	$usuarioRS__query=sprintf("SELECT u.usuarioID, u.nombre,  u.apellidoP,  u.apellidoM,  u.eMail, u.nivel, COUNT( i.imagenID ) AS Images FROM  `Usuario` u LEFT OUTER JOIN `Imagenes` i ON u.usuarioID = i.usuarioID GROUP BY u.usuarioID;");
	
	if ($result = mysqli_query($imgLink, $usuarioRS__query)) {
		/* fetch associative array */
		$_allRows = array();
		while ($row = mysqli_fetch_assoc($result)) {
			$_allRows[] = $row;
		} 
	}

?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>imagenIR Admin</title>
    <link type="text/css" rel="stylesheet" href="_css/imgAdmin.css">
    <script type="text/javascript" src="_includes/jquery-1.9.0.min.js"></script>
    <script type="text/javascript" src="_includes/imgAdminScripts.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <link type="text/css" rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />
    <script type="text/javascript" src="_includes/underscore.js"></script>
    <script type="text/javascript">
        var usuarioID = <?php echo $_SESSION['usuarioID']; ?>;
		var allUsuarios = <?php echo json_encode($_allRows, JSON_NUMERIC_CHECK); ?>;
    </script>

</head>
<body>
    
    <div class="navbar navbar-inverse navbar-fixed-top topNavegation">
      <div class="navbar-inner">
      <!-- Wrap the .navbar in .container to center it within the absolutely positioned parent. -->
      <div class="container">

         <!-- Brand and toggle get grouped for better mobile display -->
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand imgBrand" href="#">imagen<strong>IR</strong></a>
          </div>
          
           <!-- Collect the nav links, forms, and other content for toggling -->
          <div class="collapse navbar-collapse navbar-ex1-collapse">
            <ul class="nav navbar-nav">
              <li class="active"><a href="#">Inicio</a></li>
              <li><a href="index.html">Cerrar</a></li>
              <li><a href="#contact">Contacto</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div> <!-- /.container -->
      </div><!-- /.navbar-inner -->
    </div><!-- /.navbar -->

    
   <div class="container">
	   
	<div class="row">
      <div class="adminTasks">
        <h3 class="text-muted">Administrador: <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellidoP'] ?></h3>
        <button class="btn btn-default" id="usrList">Lista de usuarios</button>
        <button class="btn btn-default" id="usrNew">Nuevo Usuario</button>
        <a href="img1-5-0.php" class="btn btn-primary active" role="button">Volver</a>
      </div>   
     </div>   

	<div class="row">&nbsp;
    </div>   

	<div class="panel panel-default" id="usrInput">
        <div class="panel-heading">
            <button type="button" class="close">&times;</button>
		    <strong><?php echo $_formHead; ?> </strong>
      </div>
        <div class="panel-body">
          <form id="imgAdmTasks" name="imgAdmTasks" method="POST" autocomplete="off" action="">
          	<input type="hidden" name="imgTask" value="">
          	<input type="hidden" name="usuarioID" value="">
          	<input type="hidden" name="chgFields" value="">
			<div class="msgs"></div>			
            <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Nombre" autofocus>
            <input type="text" name="apellidoP" id="apellidoP" class="form-control" placeholder="Apellido Paterno" autofocus>
            <input type="text" name="apellidoM" id="apellidoM" class="form-control" placeholder="Apellido Materno" autofocus>
            <select name="nivel" id="nivel" class="form-control">
            	<option value="1">Usuario</option>
                <option value="2">Supervisor</option>
			</select>
            <input type="text" name="eMail" id="eMail" class="form-control" placeholder="eMail" autofocus>
            <input type="password" name="clave" id="clave" class="form-control" placeholder="Contrase&ntilde;a">
            <div class="form-group">
              <button type="submit" name="submit" class="btn btn-default btn-primary btn-block">Enviar</button>
             </div>
          </form>
        </div>
      </div> 
        <div class="panel panel-default" id="tblUsrs">
            <div class="panel-heading">
            	<button type="button" class="close">&times;</button>
                <strong><?php echo $_formHead; ?> </strong>
          </div>
            <div class="panel-body">
              <div class="msgs"></div>
             <table class="table table-bordered usrTable">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>eMail</th>
                    <th>Nivel</th>
                    <th>Im&aacute;genes</th>
                    <th>Fecha Tope</th>
                    <th><span class="glyphicon glyphicon-pencil"></span></th>
                    <th><span class="glyphicon glyphicon-remove"></span></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>      
 
         <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
              </div>
              <div class="modal-body">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="modConfirm">Continuar</button>
              </div>
            </div>
          </div>
        </div>
	
    
	<div class="row">
    	<div class="panel panel-default footerInfo">
        	<div class="panel-body">
        		<p class="text-center"><small>Derechos reservados ImagenIR</small></p>
            </div>
      <!-- end .footer --></div>
      </div>
      
    </div> <!-- /.container -->
   
</body>
</html>