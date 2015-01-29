<?php require('_includes/auth_imgAdmin.php'); ?>
<?php require_once('_includes/imagenMySQL.php'); ?>
<?php require('_includes/functionsMySQL.php'); ?>
<?php 
	  $_hasImages = 0;
	  mysql_select_db($database_ImagenIR, $ImagenIR);
	  $_usuario = GetSQLValueString($_SESSION['usuarioID'], "int");
	  $_nivel = GetSQLValueString($_SESSION['nivel'], "int");

	  
	  $imgRS__query=sprintf("SELECT imagenID, titulo, nombre, fecha, IR_img_url, IR_img_url_chica, IR_data, img_url, img_url_chica, notas, img_max AS 'Temperatura-Max', img_min AS 'Temperatura-Min', temp_base AS 'Threshold', atm_temp AS 'Temp-Atmosferica', rel_humedad AS 'Humedad-Relativa', emisiv AS Emisividad, parent, pos FROM `Imagenes` WHERE usuarioID=".$_usuario." ORDER BY parent, pos");
	
	  $imgRS = mysql_query($imgRS__query, $ImagenIR) or die(mysql_error());
	  $_hasImages = mysql_num_rows($imgRS);
	  
?>
<?php
	$_noImgs = "";
	if ($_hasImages > 0){
	$_imgInfoArr = array();
	$_imgPropArr = array();
	$_imgTableArr = array();
	$_imgInfoArr2 = array();
	$_imgIDs = array();
	$_imgIRdata = array();
	$_imgPoints = array();

	  while ($imgRow = mysql_fetch_assoc($imgRS)) {
		  // Calculate the Delta between base temp and High temp
		$_rangeTest = $imgRow['Temperatura-Max'] - $imgRow['Threshold'];
		if ( $_rangeTest > 20 ) {
			$_rango = 2;
		} else if ( $_rangeTest > 10 ) {
			$_rango = 1;
		} else {
			$_rango = 0;
		}

		$_imgInfo = "<table cellspacing='0'>";
       // Find all the keys (column names) from the array $my_array
          $columns = array_keys($imgRow);
       // Find all the values from the array $my_array
          $values = array_values($imgRow);
		// Count the fields
	    $fieldCount = count($values);
   	    for($i = 0; $i <$fieldCount; $i++) {
			if ($columns[$i] == "imagenID") {
				$_imgID = $values[$i];
				$_imgIDs[] = $values[$i];
				$_imgInfoArr[$_imgID][$columns[$i]] = $values[$i];
			} elseif (($columns[$i] == "titulo") || ($columns[$i] == "nombre") || 
				($columns[$i] == "fecha") || ($columns[$i] == "parent") || 
				($columns[$i] == "pos") || ($columns[$i] == "notas") ||
				($columns[$i] == "IR_img_url") || ($columns[$i] == "IR_img_url_chica") ||
				($columns[$i] == "img_url") || ($columns[$i] == "img_url_chica")) {
				$_imgInfoArr[$_imgID][$columns[$i]] = $values[$i];
			} elseif ($columns[$i] == "Threshold") {
				$_imgInfoArr[$_imgID]["Rango"] = $_rango;
				$_imgInfoArr[$_imgID][$columns[$i]] = $values[$i];
			} elseif ($columns[$i] == "IR_data") {
				$_imgIRdataPath = $values[$i];
				$_imgInfoArr[$_imgID][$columns[$i]] = $values[$i];
			} else {
			  $_imgInfo .= "<tr>";
			  $_imgInfo .= "<td>".$columns[$i]."</td>";
			  $_imgInfo .= "<td align='right'>".$values[$i]."</td>";
			  $_imgInfo .= "</tr>";
			  $_imgPropArr[$_imgID][$columns[$i]] = $values[$i];
			}
		}
		$_imgInfo .= "</table>";
		$_imgTableArr[$_imgID] = $_imgInfo;
		// Get IR_data info and add to array
		// This version assumes a one line string of numbers
/*		$row = 0;
		$_imgIRdata[$_imgID][$row] = explode("\n", file_get_contents("_images/".$_imgIRdataPath));
		// Commented out version assumes multi line number file.
		$handle = @fopen("_images/".$_imgIRdataPath, "r");
		if ($handle) {
			while (($buffer = fgets($handle, 4096)) !== false) {
				$pieces = explode(",", $buffer);
				$_imgIRdata[$_imgID][$row] = $pieces;
				$row++;
			} 
			if (!feof($handle)) {
				echo "Error: unexpected fgets() fail\n";
			}
			fclose($handle);
		}  */
	  }
    } else {
		$_noImgs = "No Hay ";
	}

	mysql_free_result($imgRS);
	$_SESSION['_imgInfoArr'] = $_imgInfoArr;
	$_SESSION['imgPropArr'] = $_imgPropArr;
	$_SESSION['imgTableArr'] = $_imgTableArr;
	//$_SESSION['imgIRdata'] = $_imgIRdata;
	
	// get folder names and positions
	$folderRS__query="SELECT carpetaID, parent, pos, nombre, orden FROM `Carpetas` WHERE usuarioID=".$_usuario." ORDER BY orden";
	  $folderRS = mysql_query($folderRS__query, $ImagenIR) or die(mysql_error());
	  $_folderCnt = 0;
	  $_idMax = 0;
	while( $folderRow = mysql_fetch_assoc( $folderRS)){
		$_folders[] = $folderRow; // Inside while loop
		$_folderCnt++;
		if($folderRow['carpetaID'] > $_idMax) {
			$_idMax = $folderRow['carpetaID'];
		}
	}
	mysql_free_result($folderRS);
	$_SESSION['folders'] = $_folders;
	$_SESSION['folderCnt'] = $_folderCnt;
	$_SESSION['usuarioID'] = $_usuario;
	$_SESSION['endings'] = array();
	$_jsonString = '';
	
// process folders
	function buildStart($type, $id, $nombre, $children, $state, $_sibling, $_range) {
		$typeId = $type."_";
		$end = "}";
		$_startPiece = '{"data": "'.$nombre.'", "attr": {"id" : "'.$typeId.$id.'"';
		if ($type == "i") {
			$_startPiece .= ', "rel":"imgIR", "data-range":'.$_range.'}}';
			return $_startPiece;
		} else {
			$_startPiece .= '}';
			//endings for folders only
			if ($children > 0) {
				$end = "]".$end;
			}
			if ($_sibling) {
				$end .= ",";
			}
			$_SESSION['endings'][$id] = $end;
		}
		if ($state == 1) {
			$_startPiece .= ', "state": "open"';
		}
		
		if ($children > 0) {
			$_startPiece .= ', "children": [';
		} 
		return $_startPiece;
	}
	
	$_first = true;
	$_jsonString = '';
	$_tailEnd = '';
	$_curPar = -1;
	$_parents = array();
	foreach ($_folders as $f){
		//check if folder is empty or not
		$_sibling = false;
		$_fldChildren = 0;
		$_imgChildren = 0;
		$_state = 0;
		if (is_array($_imgInfoArr)) {
			foreach ($_imgInfoArr as $j) {
				if ($j['parent']==$f['carpetaID']) {
					$_imgChildren++;
				}
			}
		}
		foreach ($_folders as $ff) {
			if ($_first) {
				$_parents[$ff['carpetaID']] = $ff['parent'];
			}
			if ($ff['parent']==$f['carpetaID']) {
				$_fldChildren++;
			}
			if ($ff['parent']==$f['parent'] && $ff['orden']>$f['orden']) {
				$_sibling = true;
			}
		}
		$_child = $_fldChildren+$_imgChildren;
		if ($_first) {
			$_root = $f['carpetaID'];
			$_state = 1;
			$_first = false;
		} 
		if ($_curPar == $f['parent'] ) { 
			$_jsonString .= ', ';
		}
				
		$_jsonString .= buildStart("f",$f['carpetaID'],$f['nombre'],$_child,$_state,$_sibling,$j['Rango']);
		if ($_imgChildren > 0) {		
			$_curPar = $f['carpetaID'];
			$_firstChild = true;
			$_imgCnt = 0;
			foreach ($_imgInfoArr as $j) {
				// find images
				if ($j['parent']==$f['carpetaID']) {
					$_images = true;
					$_firstChild = false;
					if ($_imgCnt > 0) {
						$_jsonString .= ', ';
					}
					$_imgCnt++;
					$_child = 0;
					$_state = 0;
					$_jsonString .= buildStart("i",$j['imagenID'],$j['nombre'],$_child,$_state,$_sibling,$j['Rango']);
				} 
			}
		}
		if ($_fldChildren == 0) {
			$_cId = $f['carpetaID'];
			while (count($_SESSION['endings']) > 0) {
				$_end = $_SESSION['endings'][$_cId];
				$_jsonString .= $_end;
				if (substr($_end,-1) == ',' || $_cId == $_root) {
					break;
				}
				$_cId = $_parents[$_cId];
			}
		}
	}
	
	// look for any points related to usuario's images
	$ptRS__query="SELECT p.imagenID, p.puntoID, p.posX, p.posY, p.temp FROM `Puntos` AS p INNER JOIN `Imagenes` AS i ON i.imagenID = p.imagenID WHERE i.usuarioID=".$_usuario." ORDER BY p.imagenID, p.puntoID";
	  $ptRS = mysql_query($ptRS__query, $ImagenIR) or die(mysql_error());

	while( $ptRow = mysql_fetch_assoc( $ptRS)){
		$_imgPoints[] = $ptRow; // Inside while loop
	}
	mysql_free_result($ptRS);
	$_SESSION['imgPointData'] = $_imgPoints;
	
?>      
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>imagenIR</title>
<link type="text/css" rel="stylesheet" href="_css/img1.css">
<script type="text/javascript" src="_includes/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="_includes/jquery.jstree.js"></script>
<script type="text/javascript">
	var folders = <?php echo $_idMax; ?>;
	var usuarioID = <?php echo $_SESSION['usuarioID']; ?>;
	var treeFolders =  <?php echo $_jsonString; ?>;
</script>
<script type="text/javascript" src="_includes/imgScripts.js"></script>
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
<link type="text/css" rel="stylesheet" href="bootstrap/css/bootstrap.min.css" />

<script type="text/javascript" src="_includes/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="_includes/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="_includes/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="_includes/jqplot.pointLabels.min.js"></script>
<link rel="stylesheet" type="text/css" href="_css/jquery.jqplot.min.css" />

<!--Load the AJAX API-->
<!--script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
  // Load the Visualization API and the linechart package.
      google.load("visualization", "1", {packages:["corechart"]});
      google.setOnLoadCallback(drawChart);
</script-->

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
    </div><!-- /.navbar -->

      </div> <!-- /.container -->
    </div>

  <div class="container">
    <div class="panel panel-info panelCliente">
        <div class="panel-heading">
          <strong>Cliente: <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellidoP']?></strong>
      		<?php if ($_nivel == 2) {
				echo("<div class='imgAdmin pull-right'><form class='form-inline' method='POST' action='imgAdmin.php'><input type='hidden' name='admin' value='2' /><button type='submit' class='btn btn-default btn-xs'>Administrar</button></form></div>");
			}
            ?>
         </div>
    </div>

	<!-- Image listing and Display -->
  	<div class="row">
        <div class="imgPreview col-lg-2 col-md-3 col-sm-5 col-xs-5">
        </div>
    	<div id="Imagenes" class="col-lg-3 col-md-5 col-sm-7 col-xs-7">
            <ul class="nav nav-pills setState">
                <li class="active stateImg"><a>Im&aacute;genes</a></li>
                <li class="stateGraf"><a>Gr&aacute;ficos</a></li>
            </ul>
            <h4><?php echo $_noImgs;?>Im&aacute;genes Disponibles</h4>
            <button type="button" class="btn btn-primary btn-xs" id="toggleTree" data-toggle="button">Abrir todas</button>
             <div id="treeList">
        <!-- end #treeList --></div>
        </div>
        <div class="col-lg-7 col-md-9 col-sm-11 col-xs-11 imgDisplay">
            <!-- Image Display div -->
            <div class="imgDisplayTemplate">
                <div class="panel panel-default">
                  <div class="panel-heading imgHead">
                    <button type="button" class="btn btn-default btn-xs pull-right imgClose">
                      <span class="glyphicon glyphicon-remove"></span> Cerrar
                    </button>
                    <h4 class="panel-title">Title</h4>
                  </div>
                  <div class="panel-body imgBody">
                    <div class="row">
                        <div class="col-md-4 col-sm-4 col-xs-4 imgInfoBlock">
                            Stats
                        </div>
                        <div class="col-md-3 col-sm-3 col-xs-3 imgAnalyze">
                            <button type="button" class="btn btn-primary analizar">Analizar</button>
                            <h2><span class="label label-default showTemp"></span></h2>
                            <button type="button" class="btn btn-default btn-xs TempSpotPost">
                            <span class="glyphicon glyphicon-save"></span> Guardar Puntos</button>
                        </div>                    
                        <div class="col-md-2 col-sm-2 col-xs-2 imgThresholdChg">
                            Threshold
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 imgImage">
                        Image
                        </div>
                    </div>
                  </div>
                </div>
                
                
              </div>    
              <!-- End Image Display div -->

			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 chartDisplayTemplate">
              <!-- Chart Display div -->
            <!-- div class="chartDisplayTemplate" -->
                <!-- div class="col-md-4 col-sm-4 col-xs-4 imgPreviewGr" -->
                </div-->
                <div class="col-md-12 col-sm-12 col-xs-12 chartMain">
                    <div class="panel panel-default">
                      <div class="panel-heading chartHead">
                        <button type="button" class="btn btn-default btn-xs pull-right chartClose">
                          <span class="glyphicon glyphicon-remove"></span> Cerrar
                        </button>
                        <button type="button" class="btn btn-default btn-xs pull-right genReport">
                          <span class="glyphicon glyphicon-print"></span> Reporte
                        </button>&nbsp;
                        <h4 class="panel-title">Gr&aacute;ficos</h4>
                      </div>
                      <div class="panel-body chartBody">
                      	<button type="button" class="btn btn-small btn-primary makeCharts">Generar gr&aacute;ficos</button>
                            <table class="table table-bordered imgTabla">
                                <thead>
                                  <tr>
                                    <th>ID</th>
                                    <th>Sitio</th>
                                    <th>Im&aacute;gen</th>
                                    <th>Fecha</th>
                                    <th>Temp-Max</th>
                                    <th>Temp-Min</th>
                                    <th><span class="glyphicon glyphicon-remove"></span></th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                              </table>
                         </div> 
                        <div id="chartBlock">
                        </div>
                        </div>
                </div>
              </div>    
              <!-- End Chart Display div -->
              
              
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 reportTemplate">
                <div class="col-md-12 col-sm-12 col-xs-12 reportMain">
                    <div class="panel panel-default">
                      <div class="panel-heading reportHead">
                        <button type="button" class="btn btn-default btn-xs pull-right reportClose">
                          <span class="glyphicon glyphicon-remove"></span> Cerrar
                        </button>
                        <button type="button" class="btn btn-default btn-xs pull-right reportPrint">
                          <span class="glyphicon glyphicon-print"></span> Imprimir
                        </button>
                        <h4 class="panel-title"></h4>
                      </div>
                      <div class="panel-body reportBody">
                        <div id="reportImgDC">
                        </div>
                        <div class="row">&nbsp;</div>
                        <div id="repChartBlock">
                        </div>
                        <div class="row">&nbsp;</div>
                            <table class="table table-bordered reportTabla">
                                <thead>
                                  <tr>
                                    <th>Nombre y Fecha</th>
                                    <th>Im&aacute;gen</th>
                                    <th>Datos</th>
                                  </tr>
                                </thead>
                                <tbody>
                                </tbody>
                              </table>
                         </div> 
                        </div>
                </div>
              </div>    
              <!-- End Report Display div -->
        </div>
        
    </div>

      
      <div class="mainContent">
      <div class="imgPanels">
		<?php 
		foreach ($_imgIDs as $_id) {
			echo "<div class='imgInfoPanel ".$_imgInfoArr[$_id]['imagenID']."'>";
			//echo "<div class='close'><img src='_images/cerrar.png' alt='Cerrar imagen' /></div>";
			echo "<div class='imgInfoHed'>";
			echo "<div class='tnImage'>";
			echo "<img id='".$_imgInfoArr[$_id]['imagenID']."' src='_images/".$_imgInfoArr[$_id]['IR_img_url_chica']."' width='90' height='68' /><br/>";
			echo "</div>";
			echo "<h4>".$_imgInfoArr[$_id]['nombre']."</h4>";
			echo "<p class='intro'>".(date_format(date_create($_imgInfoArr[$_id]['fecha']), "d-m-Y, g:i a"))."</p>";
			echo "</div>\n";
			echo "<div class='imgInfoBlock'>".$_imgTableArr[$_id]."</div>";
			echo "<div class='DCimgInfo'>";
			echo "<img src='_images/".$_imgInfoArr[$_id]['img_url']."' width='192' height='144' />";
			echo "</div>";
			echo "<div class='imgThreshold'>";
			//echo "<form role='form'><div class='form-group'><label for='txtThreshold' class='col-sm-4'>Threshold</label><div class='col-sm-4'><input type='text' class='form-control input-sm' id='txtThreshold' value='".$_imgInfoArr[$_id]['Threshold']."'></div></div><button type='button' class='btn btn-primary btn-xs'>Cambiar</button></form>";
			echo "<p><strong>Threshold</strong><input type='text' class='form-control input-sm' id='impThreshold' value='".$_imgInfoArr[$_id]['Threshold']."' /> <button type='button' id='chgThreshold' class='btn btn-primary btn-xs'>Cambiar</button></p>";
			echo "</div>";
			echo "</div>\n";
		}
        ?>
      </div>
      <div class="imgDataBlock">
		<?php 
      	foreach ($_imgPoints as $_p) {
			echo "<div class='pt";
			$_first = true;
			foreach ($_p as $_v) {
				if ($_first) {
					echo "{$_v}'>";
					$_first = false;
				} else {
					echo ",";
				}
				echo "{$_v}";
			}
			echo "</div>\n";
        }
        ?>
      </div>
      
      <!-- end .mainContent --></div>
	<div class="row">
    	<div class="panel panel-default footerInfo">
        	<div class="panel-body">
        		<p class="text-center"><small>Derechos reservados ImagenIR</small></p>
            </div>
      <!-- end .footer --></div>     </div>
  <!-- end .container --></div>
</body>
</html>