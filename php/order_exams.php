<?php

    $response = null;

    // Authentication
    session_start();
    if (!isset($_SESSION['username']) || !isset($_SESSION['password']) || !isset($_SESSION['category'])) {
        echo json_encode($response);
        exit();
    }

    // Connect to DB.

    require_once 'connect.php';

    $data = json_decode(file_get_contents('php://input'));

    $response = null;

    try {

        // Retrieve the order_exam record.
    
        $sql = "SELECT * FROM order_exam WHERE id = " . $data->orderExamId . ";";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $order_exam = $stmt->fetch();

        $response['order_exam'] = $order_exam;

        // Retrieve the exam record

        $sql = "SELECT * FROM exam WHERE id = '" . $order_exam['exam_id'] . "';";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $exam = $stmt->fetch();

        $response['exam'] = $exam;

        // Retrieve the order record.

        $sql = "SELECT * FROM order_2 WHERE id = '" . $order_exam['order_id'] . "';";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $order = $stmt->fetch();

        $response['order'] = $order;

        // Retrieve the patient record.

        $sql = "SELECT * FROM patient WHERE id = '" . $order['patient_id'] . "';";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $patient = $stmt->fetch();

        $response['patient'] = $patient;

    } catch (PDOException $e) {

        die("Could not connect ot the database $dbname: " . $e->getMessage());
    
    }

    echo json_encode($response);
