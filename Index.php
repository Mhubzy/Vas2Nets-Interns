<?php
session_start();

//If already logged in send to the student dashboard
if (!empty($_SESSION['user'])) {
    header('Location: Student_Dashboard.php');
    exit;
} else {
    header('Location: Login.php');
    exit;
}

?>