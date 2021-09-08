<?php


    session_start();


    require_once 'config.php';


    if (isset($_SESSION['category'])) {
        
        session_destroy();
        unset($_SESSION['username']);
        unset($_SESSION['password']);
        unset($_SESSION['category']);
        $response = ['login' => false];
    } else {
        $response = null;
    }
    

    header('Content-Type: application/json');
    echo json_encode($response);
