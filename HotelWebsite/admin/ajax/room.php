
<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['add_room'])) {
    $feature = filtration(json_decode($_POST['feature']));
    $facilities = filtration(json_decode($_POST['facilities']));

    $frm_data = filtration($_POST);
    $flag = 0;

    $q1 = "INSERT INTO `room`( `name`, `area`, `price`, `quantity`,`adult`,`children`,`description`) VALUES (?,?,?,?,?,?,?)";
    $values = [$frm_data['name'], $frm_data['area'], $frm_data['price'], $frm_data['quantity'], $frm_data['adult'], $frm_data['children'], $frm_data['desc']];

    if (insert($q1, $values, 'siiiiis')) {
        $flag = 1;
    }

    $room_id = mysqli_insert_id($con);

    $q2 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q2)) {

        foreach ($facilities as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $room_id, $f);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query can not be prepare - insert');
    }

    $q3 = "INSERT INTO `room_feature`(`room_id`, `feature_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q3)) {

        foreach ($feature as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $room_id, $f);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query can not be prepare - insert');
    }

    if ($flag) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['get_all_room'])) {
    $res = select("SELECT * FROM `room` WHERE `removed`=?", [0], 'i');
    $i = 1;
    $data = "";
    while ($row = mysqli_fetch_assoc($res)) {
        if ($row['status'] == 1) {
            $status = "<button onclick='toggle_status($row[id],0)' class='btn btn-dark btn-sm shadow-none'>Active</button>";
        } else {
            $status = "<button onclick='toggle_status($row[id],1)' class='btn btn-warning btn-sm shadow-none'>Inactive</button>";
        }
        $data .= "
            <tr class='align-middle'>
                <td>$i</td>
                <td>$row[name]</td>
                <td>$row[area]</td>
                <td>
                    <span class = 'badge rounded-pill bg-light text-dark'>
                        Adult Max: $row[adult]
                    </span>
                    <br>
                    <span class = 'badge rounded-pill bg-light text-dark'>
                        Children Max: $row[children]
                    </span>
                </td>
                <td>$row[price]$ per night</td>
                <td>$row[quantity]</td>
                <td>$status</td>
                <td>
                    <button type='button' onclick='edit_details($row[id])' class='btn btn-primary shadow-none btn-sm' data-bs-toggle='modal' data-bs-target='#edit-room'>
                        <i class='bi bi-pencil-square'></i> 
                    </button>
                    <button type='button' onclick=\"room_image($row[id],'$row[name]')\" class='btn btn-info shadow-none btn-sm' data-bs-toggle='modal' data-bs-target='#room-image'>
                        <i class='bi bi-images'></i> 
                    </button>
                    <button type='button' onclick='remove_room($row[id])' class='btn btn-danger shadow-none btn-sm'>
                        <i class='bi bi-trash'></i> 
                    </button>
                </td>
            </tr>
        ";
        $i++;
    }
    echo $data;
}

if (isset($_POST['get_room'])) {
    $frm_data = filtration($_POST);

    $res1 = select("SELECT * FROM `room` WHERE `id`=?", [$frm_data['get_room']], 'i');
    $res2 = select("SELECT * FROM `room_feature` WHERE `room_id`=?", [$frm_data['get_room']], 'i');
    $res3 = select("SELECT * FROM `room_facilities` WHERE `room_id`=?", [$frm_data['get_room']], 'i');

    $roomdata = mysqli_fetch_assoc($res1);
    $feature = [];
    $facilities = [];

    if (mysqli_num_rows($res2) > 0) {
        while ($row = mysqli_fetch_assoc($res2)) {
            array_push($feature, $row['feature_id']);
        }
    }
    if (mysqli_num_rows($res3) > 0) {
        while ($row = mysqli_fetch_assoc($res3)) {
            array_push($facilities, $row['facilities_id']);
        }
    }

    $data = ["roomdata" => $roomdata, "feature" => $feature, "facilities" => $facilities];
    $data = json_encode($data);
    echo $data;
}

if (isset($_POST['toggle_status'])) {
    $frm_data = filtration($_POST);
    $q = "UPDATE `room` SET `status`=? WHERE `id`=?";
    $v = [$frm_data['value'], $frm_data['toggle_status']];

    if (update($q, $v, 'ii')) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['edit_room'])) {
    $feature = filtration(json_decode($_POST['feature']));
    $facilities = filtration(json_decode($_POST['facilities']));

    $frm_data = filtration($_POST);
    $flag = 0;

    $q1 = "UPDATE `room` SET `name`=?,`area`=?,`price`=?,`quantity`=?,`adult`=?,`children`=?,`description`=? WHERE `id`=?";

    $values = [$frm_data['name'], $frm_data['area'], $frm_data['price'], $frm_data['quantity'], $frm_data['adult'], $frm_data['children'], $frm_data['desc'], $frm_data['room_id']];

    if (update($q1, $values, 'siiiiisi')) {
        $flag = 1;
    }

    $del_feature = delete("DELETE FROM `room_feature` WHERE `room_id` = ?", [$frm_data['room_id']], 'i');
    $del_facilities = delete("DELETE FROM `room_facilities` WHERE `room_id` = ?", [$frm_data['room_id']], 'i');

    if (!($del_facilities && $del_feature)) {
        $flag = 0;
    }
    $q2 = "INSERT INTO `room_facilities`(`room_id`, `facilities_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q2)) {

        foreach ($facilities as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $frm_data['room_id'], $f);
            mysqli_stmt_execute($stmt);
        }
        $flag = 1;
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query can not be prepare - insert');
    }

    $q3 = "INSERT INTO `room_feature`(`room_id`, `feature_id`) VALUES (?,?)";

    if ($stmt = mysqli_prepare($con, $q3)) {

        foreach ($feature as $f) {
            mysqli_stmt_bind_param($stmt, 'ii', $frm_data['room_id'], $f);
            mysqli_stmt_execute($stmt);
        }
        $flag = 1;
        mysqli_stmt_close($stmt);
    } else {
        $flag = 0;
        die('query can not be prepare - insert');
    }

    if ($flag) {
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['add_image'])) {
    $frm_data = filtration($_POST);

    $img_r = uploadImage($_FILES['image'], ROOM_FOLDER);

    if ($img_r == 'inv_img') {
        echo $img_r;
    } else if ($img_r == 'inv_size') {
        echo $img_r;
    } else if ($img_r == 'upd_failed') {
        echo $img_r;
    } else {
        $q = "INSERT INTO `room_image`(`room_id`, `image`) VALUES (?,?)";
        $values = [$frm_data['room_id'], $img_r];
        $res = insert($q, $values, 'is');
        echo $res;
    }
}

if (isset($_POST['get_room_image'])) {
    $frm_data = filtration($_POST);
    $res = select("SELECT * FROM `room_image` WHERE `room_id`=?", [$frm_data['get_room_image']], 'i');

    $path = ROOM_IMG_PATH;

    while ($row = mysqli_fetch_assoc($res)) {

        if ($row['thumb'] == 1) {
            $thumb_btn = "<i class='bi bi-check-lg text-light bg-success px-2 py-1 rounded fs-5'></i>";
        } else {
            $thumb_btn = " <button onclick='thumb_image($row[sr_no], $row[room_id])' class='btn btn-secondary btn-sm shadow-none'>
                        <i class='bi bi-check-lg'></i></button> ";
        }

        echo <<<data
            <tr class='align-middle'>
                <td><img src='$path$row[image]' class = 'img-fluid'></td>
                <td>$thumb_btn</td>
                <td>
                    <button onclick='rem_image($row[sr_no], $row[room_id])' class='btn btn-danger btn-sm shadow-none'>
                        <i class='bi bi-trash'></i>
                    </button>
                </td>
            </tr>
        data;
    }
}

if (isset($_POST['rem_image'])) {

    $frm_data = filtration($_POST);
    $values = [$frm_data['image_id'], $frm_data['room_id']];

    $pre_q = "SELECT * FROM `room_image` WHERE `sr_no`=? AND `room_id`=?";
    $res = select($pre_q, $values, 'ii');
    $img = mysqli_fetch_assoc($res);

    if (deleteImage($img['image'], ROOM_FOLDER)) {
        $q = "DELETE FROM `room_image` WHERE `sr_no`=? AND `room_id`=?";
        $res = delete($q, $values, 'ii');
        echo $res;
    } else {
        echo 0;
    }
}

if (isset($_POST['thumb_image'])) {
    $frm_data = filtration($_POST);

    $pre_q = "UPDATE `room_image` SET `thumb`=? WHERE `room_id`= ?";
    $pre_v = [0, $frm_data['room_id']];
    $pre_res = update($pre_q, $pre_v, 'ii');

    $q = "UPDATE `room_image` SET `thumb`=? WHERE `sr_no`=? AND `room_id`=? ";
    $v = [1, $frm_data['image_id'], $frm_data['room_id']];
    $res = update($q, $v, 'iii');

    echo $res;
}

if (isset($_POST['remove_room'])) {
    $frm_data = filtration($_POST);

    $res1 = select("SELECT * FROM `room_image` WHERE `room_id`=?", [$frm_data['room_id']], 'i');

    while ($row = mysqli_fetch_assoc($res1)) {
        deleteImage($row['image'], ROOM_FOLDER);
    }

    $res2 = delete("DELETE FROM `room_image` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    $res3 = delete("DELETE FROM `room_feature` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    $res4 = delete("DELETE FROM `room_facilities` WHERE `room_id`=?", [$frm_data['room_id']], 'i');
    $res5 = update("UPDATE `room` SET `removed`=? WHERE `id`=?", [1, $frm_data['room_id']], 'ii');

    if ($res2 || $res3 || $res4 || $res5) {
        echo 1;
    } else {
        echo 0;
    }
}




?>