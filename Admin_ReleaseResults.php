<?php
session_start();

//Authentication Protection
require_once __DIR__ . '/../../Includes/Admin_auth.php';

//Load Admin class
require_once __DIR__ . '/../../classes/Admin.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../Admin/All_Results.php');
    exit();
}   

$adminModel = new Admin();
$action = $_POST['action'] ?? '';

if($action === 'release_results') {
    
    $results = $adminModel->releaseResults(true);
    
    if($results) {
        
        $_SESSION['success'] = [
            'Results have been released to students successfully. Students can now view their results on their dashboard.'
        ];

    } else {
        
        $_SESSION['error'] = [
            'An error occurred while releasing results. Please try again.'
        ];

    }

} elseif($action === 'hide_results') {
    
    $results = $adminModel->releaseResults(false);
    
    if($results) {

        $_SESSION['success'] = [
            'Results have been hidden from students successfully. Students can no longer view their results on their dashboard.'
        ];

    } else {

        $_SESSION['error'] = [
            'An error occurred while hiding results. Please try again.'
        ];
        
    }

}

header("Location: ../../Admin/All_Results.php");
exit;
?>
