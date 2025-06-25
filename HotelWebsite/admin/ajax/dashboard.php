<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['booking_analytics'])) {

    $frm_data = filtration($_POST);

    $condition = "";
    if ($frm_data['period'] == 1) {
        $condition = "WHERE datentime BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
    } else if ($frm_data['period'] == 2) {
        $condition = "WHERE datentime BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
    } else if ($frm_data['period'] == 3) {
        $condition = "WHERE datentime BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
    }


    $result = mysqli_fetch_assoc(mysqli_query($con, "SELECT 

        COUNT(CASE WHEN booking_status!='pending' AND booking_status!='payment failed' THEN 1 END) AS `total_booking`,
        SUM(CASE WHEN booking_status!='pending' AND booking_status!='payment failed'  THEN `trans_amt` END) AS `total_amt`,

        COUNT(CASE WHEN booking_status='booked' AND arrival=1 THEN 1 END) AS `active_booking`,
        SUM(CASE WHEN booking_status='booked' AND arrival=1 THEN `trans_amt` END) AS `active_amt`,
        
        COUNT(CASE WHEN booking_status='cancelled' AND refund=1 THEN 1 END) AS `cancelled_booking`,
        SUM(CASE WHEN booking_status='cancelled' AND refund=1 THEN `trans_amt` END) AS `cancelled_amt`
        FROM `booking_order` $condition"));

    $result['total_amt'] = $result['total_amt'] ?? 0;
    $result['cancelled_amt'] = $result['cancelled_amt'] ?? 0;
    $result['active_amt'] = $result['active_amt'] ?? 0;

    $output = json_encode($result);

    echo $output;
}


if (isset($_POST['user_analytics'])) {

    $frm_data = filtration($_POST);

    $condition2 = "";
    if ($frm_data['period'] == 1) {
        $condition2 = "WHERE datentime BETWEEN NOW() - INTERVAL 30 DAY AND NOW()";
    } else if ($frm_data['period'] == 2) {
        $condition2 = "WHERE datentime BETWEEN NOW() - INTERVAL 90 DAY AND NOW()";
    } else if ($frm_data['period'] == 3) {
        $condition2 = "WHERE datentime BETWEEN NOW() - INTERVAL 1 YEAR AND NOW()";
    }

    $total_review = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS `count` 
        FROM `rating_review` $condition2  "));

    $total_queries = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(sr_no) AS `count` 
        FROM `user_queries`  $condition2  "));

    $total_new_reg = mysqli_fetch_assoc(mysqli_query($con, "SELECT COUNT(id) AS `count` 
        FROM `user_cred`  $condition2"));

    $output = [
        'total_queries' => $total_queries['count'],
        'total_review' => $total_review['count'],
        'total_new_reg' => $total_new_reg['count']

    ];

    $output = json_encode($output);
    echo $output;
}
