<?php
$ServerName = "localhost";
$Username = "root";
$Password = "";
$NameBD = "ser";
$mysqli=new mysqli($ServerName, $Username, $Password, $NameBD); 

$mysqli->set_charset("utf8");

	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}
?>