<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
date_default_timezone_set("Asia/Ho_chi_Minh");
session_start();


if (isset($_GET['fetch_room'])) {

    //check avail data decode
    $chk_avail = json_decode($_GET['chk_avail'], true);

    //checkin checkout filter validation
    if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {

        $today_date = new DateTime(date("Y-m-d"));
        $checkin_date = new DateTime($chk_avail['checkin']);
        $checkout_date = new DateTime($chk_avail['checkout']);

        if ($checkin_date == $checkout_date) {
            echo "<h3 class='text-center text-danger h-font'>Invalid Date Entered</h3>";
            exit;
        } else if ($checkout_date < $checkin_date) {
            echo "<h3 class='text-center text-danger h-font'>Invalid Date Entered</h3>";
            exit;
        } else if ($checkin_date < $today_date) {
            echo "<h3 class='text-center text-danger h-font'>Invalid Date Entered</h3>";
            exit;
        }
    }

    //guest data decode
    $guest = json_decode($_GET['guest'], true);
    $adult = ($guest['adult'] != '') ? $guest['adult'] : 0;
    $children = ($guest['children'] != '') ? $guest['children'] : '';

    //facilities data decode
    $facilities_list = json_decode($_GET['facilities_list'], true);


    //dem so phong
    $count_room = 0;
    $output =  "";

    // fetching setting table to check website is shutdown or not
    $settings_q = "SELECT * FROM `settings` WHERE `sr_no`=1";
    $settings_r = mysqli_fetch_assoc(mysqli_query($con, $settings_q));

    $room_res = select("SELECT * FROM `room` WHERE `adult`>=? AND `children`>=? AND `status`=?  AND `removed`=? ", [$adult, $children, 1, 0], 'iiii');

    while ($room_data = mysqli_fetch_assoc($room_res)) {

        //check room avail logic
        if ($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '') {
            $tb_query = "SELECT COUNT(*) AS `total_booking` FROM `booking_order` 
            WHERE booking_status= ? AND room_id = ? 
            AND check_out > ? AND check_in < ?";

            $values = ['booked', $room_data['id'], $chk_avail['checkin'], $chk_avail['checkout']];
            $tb_fetch = mysqli_fetch_assoc(select($tb_query, $values, 'siss'));


            if (($room_data['quantity'] - $tb_fetch['total_booking']) == 0) {
                continue;
            }
        }


        //get facilities of room with filter
        $fac_count = 0;

        $fac_q = mysqli_query($con, "SELECT f.name, f.id FROM `facilities` f
         INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id
         WHERE rfac.room_id = '$room_data[id]' ");

        $facilities_data = "";
        while ($fac_row = mysqli_fetch_assoc($fac_q)) {

            if (in_array(($fac_row['id']), $facilities_list['facilities'])) {
                $fac_count++;
            }

            $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-warp'>
             $fac_row[name]
         </span>";
        }

        if (count($facilities_list['facilities']) != $fac_count) {
            continue;
        }



        // get feature of room
        $fea_q = mysqli_query($con, "SELECT f.name FROM `feature` f
            INNER JOIN `room_feature` rfea ON f.id = rfea.feature_id
            WHERE rfea.room_id = '$room_data[id]' ");

        $feature_data = "";
        while ($fea_row = mysqli_fetch_assoc($fea_q)) {
            $feature_data .= "<span class='badge rounded-pill bg-light text-dark text-warp'>
                $fea_row[name]
            </span>";
        }



        // get thumb
        $room_thumb = ROOM_IMG_PATH . "thumbnail.jpg";
        $thumb_q = mysqli_query($con, "SELECT * FROM `room_image`
            WHERE `room_id`='$room_data[id]'
            AND `thumb`='1' ");

        if (mysqli_num_rows($thumb_q) > 0) {
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOM_IMG_PATH . $thumb_res['image'];
        }

        $book_btn = "";
        if (!$settings_r['shutdown']) {
            $login = 0;
            if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                $login = 1;
            }
            $book_btn = " <button onclick='checkLoginToBook($login,$room_data[id])'  class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2'>Book Now</button>";
        }

        //show room

        $output .= "
            <div class='card mb-4 border-0 shadow'>
                <div class='row g-0 p-3 align-items-center'>
                    <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
                        <img src='$room_thumb' class='img-fluid rounded'>
                    </div>
                    <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                        <h5 class='mb-2'>$room_data[name]</h5>
                        <div class='feature mb-3'>
                            <h6 class='mb-1'>Feature</h6>
                            $feature_data
                        </div>
                        <div class='facilities mb-3'>
                            <h6 class='mb-1'>Facilities</h6>
                            $facilities_data
                        </div>
                        <div class='guest mb-3'>
                            <h6 class='mb-1'>Guest</h6>
                            <span class='badge rounded-pill bg-light text-dark'>
                                $room_data[adult] Adults
                            </span>
                            <span class='badge rounded-pill bg-light text-dark'>
                                $room_data[children] Children
                            </span>
                        </div>
                    </div>
                    <div class='col-md-2 mt-lg-0 mt-md-0 mt-4 text-center'>
                        <h6 class='mb-4'> $room_data[price]$ per night</h6>
                        $book_btn 
                        <a href='room_detail.php?id=$room_data[id]' class='btn btn-sm w-100 btn-outline-dark shadow-none'>More Details</a>
                    </div>
                </div>
            </div>
        ";
        $count_room++;
    }

    if ($count_room > 0) {
        echo $output;
    } else {
        echo "<h3 class='text-center text-danger h-font'>No rooms to show!</h3>";
    }
}
