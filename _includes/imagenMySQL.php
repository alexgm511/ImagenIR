<?php
# Type="MYSQL"
# HTTP="true"
//$hostname_ImagenIR = "mysql";
$hostname_ImagenIR = "localhost";
$database_ImagenIR = "alexgm_ImagenIR";
$username_ImagenIR = "alexgm_IIRadmin";
$password_ImagenIR = "Infrarojo56";
$ImagenIR = mysql_pconnect($hostname_ImagenIR, $username_ImagenIR, $password_ImagenIR) or trigger_error(mysql_error(),E_USER_ERROR); 

$imgLink = mysqli_connect($hostname_ImagenIR, $username_ImagenIR, $password_ImagenIR, $database_ImagenIR);

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
	console.log(mysqli_connect_error());
    exit();
}

?>