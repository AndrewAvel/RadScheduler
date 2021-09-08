<?php

    $response = null;

    require_once 'connect.php';

    try {
    
        $sql = 'SELECT id, name, duration FROM exam;';

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        while ( $row = $stmt->fetch() ) {
            $response[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'duration' => $row['duration']
            ];
        }

    } catch (PDOException $e) {

        die("Could not connect ot the database $dbname: " . $e->getMessage());
    
    }

    echo json_encode($response);
