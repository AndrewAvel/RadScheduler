<?php

    $response = null;

    // Authentication
    session_start();

    if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['category'])) {
        echo json_encode($response);
        exit();
    }

    // Connect to DB
    require_once 'connect.php';


    try {

        // Retrieve radiologist
    
        $sql = "SELECT * FROM user WHERE username = '" . $_SESSION['username'] . "';";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $radiologist = $stmt->fetch();

        $response['radiologist'] = $radiologist;

        // Retrieve order_exams for radiologist

        $sql = "SELECT * FROM order_exam WHERE radiologist_id = '" . $radiologist['id'] . "' ORDER BY start_time;";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        while ( $row = $stmt->fetch() ) {
            $order_exams[] = $row;
        }

        $response['order_exams'] = $order_exams;

        // ...
        
    } catch (PDOException $e) {

        die("Could not connect ot the database $dbname: " . $e->getMessage());
    
    }


    echo json_encode($response);
