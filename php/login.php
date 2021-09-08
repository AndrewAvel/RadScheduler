<?php

    $data = json_decode(file_get_contents('php://input'));

    $response = null;

    require_once 'connect.php';

    try {
    
        $sql =
            'SELECT username, category
             FROM user
             WHERE username=:username AND password=:password;';

        $stmt_args = [
            'username' => $data->username,
            'password' => $data->password
        ];

        $stmt = $pdo->prepare($sql);
        $stmt->execute($stmt_args);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() != 1) {
            echo json_encode($response);
            exit();
        }

        while($row = $stmt->fetch()) {
            $response = [
                'username' => $row['username'],
                'category' => $row['category'],
            ];
        }

        session_start();

        $_SESSION['username'] = $response['username'];
        $_SESSION['category'] = $response['category'];
        $_SESSION['password'] = $data->password;
    
    } catch (PDOException $e) {

        die("Could not connect ot the database $dbname: " . $e->getMessage());
    
    }

    echo json_encode($response);
