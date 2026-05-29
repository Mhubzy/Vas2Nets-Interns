<?php
session_start();

//Not logged in at all, send to login page
if(empty($_SESSION['user_id'])) {
    header("Location: ../Login.php");
    exit;
}

//Logged in but not admin, send back to their own dashboard
if(($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../Login.php");
    exit;
}

?>
