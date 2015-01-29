<?php 
session_start();
$_loginPage = "imgLogin.php";
if(!isset($_SESSION['usuarioID'])) {
    header("Location: " . $_loginPage . "?errmsg=9" );
	die();
}
?>
