<?php
//connect to wiseweb
$connect = mysqli_connect('localhost', 'root', '', 'wiseweb');
if(!$connect){
	die (mysql_connect_error($connect));
}
?>