<?php
session_start();	
session_destroy();							//Destroy session and go to login page
header('Location:index.php');
exit;
?>