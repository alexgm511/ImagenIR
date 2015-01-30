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
	if (isset($_GET['imgID'])) {
		$_imgID = $_GET['imgID'];
	}
	
	$imgRS__query=sprintf("SELECT IR_data FROM Imagenes WHERE usuarioID=%s AND imagenID=%s",
	GetSQLValueString($_SESSION['usuarioID'], "int"),
	GetSQLValueString($_imgID, "int"));
	
	$imgRS = mysql_query($imgRS__query, $ImagenIR) or die(mysql_error());
	$row = mysql_fetch_row($imgRS);
	$_imgIRdataPath = $row[0]; 
	if (strlen($_imgIRdataPath)) {
		$imgFile = file_get_contents("_images/".$_imgIRdataPath);
		if ($imgFile === false || strlen($imgFile) == 0) {
			echo "Empty";
		} else {
			echo $imgFile;
		}
//
//		// Get IR_data info and add to array
//		echo "<div class='imgData ".$_imgID."'>";
//		$row = 0;
//		$handle = @fopen("_images/".$_imgIRdataPath, "r");
//		if ($handle) {
//			while (($buffer = fgets($handle, 4096)) !== false) {
//				echo "<div class='imgDataRow ".$row."'>";
//				$pieces = explode(",", $buffer);
//				foreach ($pieces as $piece) {
//					echo " ".$piece;
//				}
//				echo "</div>";
//				$row++;
//			}
//			if (!feof($handle)) {
//				echo "Error: unexpected fgets() fail\n";
//			}
//			fclose($handle);
//		}
//		echo "</div>";
	} else {
		echo "NoFile";
	}
?>