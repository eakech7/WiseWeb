<?php
//resume existing session
session_start();

//end session
session_destroy();

header("location:login.php");
exit();





?>