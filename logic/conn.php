<?php
$ServerName = "localhost";
$Username = "medalter_erp";
$Password = "Sx5*MsY3JcLqA$6C";
$NameBD = "medalter_serERP";
$mysqli=new mysqli($ServerName, $Username, $Password, $NameBD); 

$mysqli->set_charset("utf8");

	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}
?>