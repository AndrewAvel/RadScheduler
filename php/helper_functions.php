<?php

    // USE FOR ROUTINE SCHEDULING : returns a schedule for the routine examination (max_duration in minutes)
    function calculate_appointment_datetime($radiologist, $recommended_datetime = null, $max_duration, $duration) {

        $previous_end = get_date_start_time($recommended_datetime);
        
        $schedules = $radiologist['schedules'];

        // If no other appointments are scheduled for this radiologist
        if (count($schedules) === 0) {
            return get_appointment($previous_end, $duration, $max_duration);
        } else {
            $previous_end = ($schedules[0]['end_time']);
        }

        for ($i = 0; $i < count($schedules); $i++) {
            $current_start = $schedules[$i]['start_time'];

            if ($i === count($schedules) - 1) {
                $current_start = get_date_end_time($recommended_datetime);
            }

            if (3 * $max_duration <= calc_time_diff($previous_end, $current_start)) {

                // construct new schedule
                return get_appointment($previous_end, $duration, $max_duration);
            }

            
            if (is_a($previous_end, 'DateTime')) {
                $previous_end->format('Y-m-d H:i:s');
            } else {
                $previous_end = $schedules[$i]['end_time'];
            }

        }

        return null;
    }


    // given two datetime-formated strings 'before' and 'after' returns diff in minutes
    function calc_time_diff($before, $after) {
        return (( strtotime( $after ) - strtotime( $before ) ) / 60);
    }

    // returns datetime-formated string for today at 08:00:00
    // if datetime_str is provided then it will return that day at 08:00:00
    function get_date_start_time($datetime_str = null) {

        if (!$datetime_str) {
            $today_start_time = new DateTime($datetime_str);
        } else {
            $today_start_time = new DateTime(date("Y-m-d"));
        }

        $today_start_time->setTime(8,0,0);

        return $today_start_time;
    }

    // returns datetime-formated string for today at 20:00:00
    // if datetime_str is provided then it will return that day at 08:00:00
    function get_date_end_time($datetime_str = null) {
        if (!$datetime_str) {
            $today_end_time = new DateTime($datetime_str);
        } else {
            $today_end_time = new DateTime(date("Y-m-d"));
        }

        $today_end_time->setTime(20,0,0);

        return $today_end_time->format('Y-m-d H:i:s');
    }

    // returns date-formated string given datetime-formated string
    function get_date($datetime_str) {
        return $datetime_str->format('Y-m-d');
    }

    // returns new appointment by given $current_start 
    function get_appointment($previous_end, $duration, $max_duration) {

        if ($previous_end instanceof DateTime) {
            $previous_end_str = $previous_end->format('Y-m-d H:i:s');
        }

        if (!is_string($previous_end)) {
            $previous_end_str = $previous_end->format('Y-m-d H:i:s');
        } else {
            $previous_end_str = $previous_end;
        }

        $start_time_real = strtotime($previous_end_str) + 3 * $max_duration * 60;
        $end_time_real = $start_time_real + ($duration * 60);

        $start_time = date('Y-m-d H:i:s', $start_time_real);
        $end_time = date('Y-m-d H:i:s', $end_time_real);

        return [
            'start_time' => $start_time,
            'end_time' => $end_time
        ];
    }
