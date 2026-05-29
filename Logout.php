<?php
session_start(); //Access the current session

session_unset(); //Remove all session variables

session_destroy(); //Destroy all session variables

//Send them back to the front door(index.php)
header('location: Login.php');
exit;



?>