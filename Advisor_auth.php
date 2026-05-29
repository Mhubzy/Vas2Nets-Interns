<?php
session_start();

//Not logged in at all, send to login page
if(empty($_SESSION['user_id'])) {
    header("Location: ../Login.php");
    exit;
}

//Logged in but not advisor, send back to their own dashboard
if(($_SESSION['role'] ?? '') !== 'advisor') {
    header("Location: ../Login.php");
    exit;
}

?>
