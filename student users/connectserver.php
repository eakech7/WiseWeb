<?php
//connect to database server
$server_name = "localhost";
$user_account ="root";
$password = "";

//open connection to server: mysqli_connect()
$connect = mysqli_connect($server_name, $user_account, $password);

//confirm if connection is successful
if ($connect){
	echo "Connection successful.";
} else {
	//echo "Not successful";
	//display connection error: mysqli_connect_error()
	echo mysqli_connect_error($connect);
}
//test connection by performing a query
$query = "CREATE DATABASE WiseWeb";
//perform query to db server: mysqli_query()
$run_query = mysqli_query($connect, $query);

//check if query performs successfully
if ($run_query){
	echo "<br>Database created successfully.";
} else {
	echo mysqli_error($connect);
}

//close connection: mysqli_close()
mysqli_close($connect);


?>