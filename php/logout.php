<?php   
 //logout.php  
 session_start();  
 session_destroy();  # To deconnect it's only necessary to kill the array so none of the keys would be usable   
 header("location:../index.php");  
 ?>  