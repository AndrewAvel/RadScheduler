<?php

    $data = json_decode(file_get_contents('php://input'));

    $response = null;

   

    require_once 'connect.php';

    // 1. isolate patient
    $patient = $data->patient;

    // 2. isolate order (fk -> patient_id)
    $order = $data->order;

    $recommended_date = $order->recommendedDate;
    //$response = var_dump($recommended_date);

    // 3. isolate exams_array
    $selected_exams = $order->selectedExams;

    // 4. construct the current schedules of the radiologists -> requires sql query
    //      a. get radiologists from db
    //      b. get a list of order_exams (schedules) for each radiologist
    //      c. order the radiologists by number of order_exams
    //      d. check the difference of number of exams per radiologist
    //      e. assign appropriate number of order_exams to each radiologist
    //      f. in each assignment, if the priority is urgent add the exam ASAP
    //      g. in each assignment, if the priority is routine add the exam with 90 min gap

    // 5. analyse the schedules and find best order_exam assignment per exam

    require_once 'connect.php';
    require_once 'helper_functions.php';

    try {

        //      a. get radiologists from db
    
        $sql = 'SELECT * FROM user WHERE category = "radiologist";';

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        while ($row = $stmt->fetch()) {
            $radiologists[] = [
                'id' => $row['id'],
                'username' => $row['username'],
                'category' => $row['category'],
                'schedules' => [],
                'num_schedules' => 0
            ];
        }

        //      b. get a list of order_exams (schedules) for each radiologist
        
        foreach ($radiologists as $radiologist) {

            $radiologist_id = $radiologist['id'];

            $sql = "
                SELECT start_time, end_time
                FROM order_exam
                WHERE radiologist_id = $radiologist_id
                ORDER BY start_time;";

            $stmt = $pdo->query($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            while ($row = $stmt->fetch()) {
                array_push($radiologist['schedules'], [
                    'start_time' => $row['start_time'],
                    'end_time' => $row['end_time']
                ]);
            }

            $radiologist['num_schedules'] = count($radiologist['schedules']);
        }

        // INSERT PATIENT IF HE DOESN'T ALREADY EXIST

        // CHECK IF PATIENT EXISTS

        $sql =
            "SELECT id
             FROM patient
             WHERE ssn = '" . $patient->ssn . "';";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() === 1) {
            // Retrieve id
            $row =$stmt->fetch();
            $response = [
                'patient-id' => $row['id']
            ];

            $patient_id = $row['id'];

        } else {

            // INSERT PATIENT
            
            $sql = 
                "INSERT INTO patient (first_name, last_name, father_name, mother_name, address, ssn)
                 VALUES ('" .
                    $patient->firstName . "', '" .
                    $patient->lastName . "', '" .
                    $patient->fatherName . "', '" .
                    $patient->motherName . "', '". 
                    $patient->address . "', '". 
                    $patient->ssn . "');";

            $pdo->exec($sql);

            // Retrieve new patient's id

            $sql = "SELECT id FROM patient WHERE ssn = '" . $patient->ssn . "';";

            $stmt = $pdo->query($sql);
            $stmt->setFetchMode(PDO::FETCH_ASSOC);

            if ($stmt->rowCount() === 1) {
                // Retrieve id
                $row =$stmt->fetch();
                $response = [
                    'patient-id' => $row['id']
                ];

                $patient_id = $row['id'];
            }
        }



        

        // INSERT ORDER
        $sql = "INSERT INTO order_2 (patient_id, reason, recommended_date, priority) VALUES (" .
                $patient_id . ", '" .
                $order->reason . "', '" . 
                $order->recommendedDate . "', '" .
                $order->priority . "');";


        $response = $sql;

        $pdo->exec($sql);

        // Retrieve new order's id

        $sql =
            "SELECT MAX(ID) as id FROM order_2";

        $stmt = $pdo->query($sql);
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() === 1) {
            // Retrieve id
            $row =$stmt->fetch();

            $order_id = $row['id'];
        }

        


        $order_exams = [];
        //  for each radiologist try to find the appointent date
        //  radiologists are sorted in ascending order by workload
        //  thus the most fair way to assign the appointment to the radiologist
        //  is to assign it to the first radiologsti found to be available.
        $response = [];
        foreach ($selected_exams as $exam) {

            // order the radiologists by number of exams
            $work_load = array_column($radiologists, 'num_schedules');
            array_multisort($work_load, SORT_ASC, $radiologists);

            for ($i = 0; $i < count($radiologists); $i++) {

                $new_appointment = calculate_appointment_datetime($radiologists[$i], $recommended_date, 90, $exam->duration);

                array_push($order_exams, [
                    'order_id' => $order_id,
                    'exam_id' => $exam->id,
                    'radiologist_id' => $radiologists[$i]['id'],
                    'start_time' => $new_appointment['start_time'],
                    'end_time' => $new_appointment['end_time']
                ]);

                if ($radiologists[$i]['num_schedules'] = array_push($radiologists[$i]['schedules'], $new_appointment)) {

                    $response[] = [
                        'exam_name' => $exam->name,
                        'radiologist'=> $radiologists[$i],
                        'start_time' => $new_appointment['start_time'],
                        'end_time' => $new_appointment['end_time']
                    ];

                    break;
                }
            }
        }

        

        // INSERT THE ORDER_EXAMs

        // Construct the order_exam object

        foreach ($order_exams as $order_exam) {
            $sql = "INSERT INTO order_exam (order_id, exam_id, radiologist_id, start_time, end_time) VALUES (" .
                $order_exam['order_id'] . ", '" .
                $order_exam['exam_id'] . "', '" . 
                $order_exam['radiologist_id'] . "', '" .
                $order_exam['start_time'] . "', '" .
                $order_exam['end_time'] . "');";

            $pdo->exec($sql);

            

            //$response = $sql;
        }

        // insert it

        

    } catch (PDOException $e) {

        die("Could not connect to the database $dbname: " . $e->getMessage());
    
    }

    echo json_encode($response);
