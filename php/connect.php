<?php

    require_once 'config.php';

    try {

        $connection_string = "mysql:host=$host;dbname=$dbname";

        $pdo = new PDO($connection_string, $username, $password);

    } catch (PDOException $e) {

        die("Could not connect ot the database $dbname: " . $e->getMessage());

    }
