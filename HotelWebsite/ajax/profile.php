<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
date_default_timezone_set("Asia/Ho_chi_Minh");



if (isset($_POST['info_form'])) {
    $frm_data = filtration($_POST);
    session_start();

    $u_exist = select(
        "SELECT * FROM `user_cred` WHERE `phonenum` = ? AND `id`!=? LIMIT 1",
        [$frm_data['phonenum'], $_SESSION['uID']],
        "ss"
    );

    if (mysqli_num_rows($u_exist) != 0) {
        echo 'phone_already';
        exit;
    }

    $query = "UPDATE `user_cred` SET `name`=?,`address`=?,`phonenum`=?,`pincode`=?,`dob`=?
         WHERE `id`=? LIMIT 1";
    $values = [
        $frm_data['name'],
        $frm_data['address'],
        $frm_data['phonenum'],
        $frm_data['pincode'],
        $frm_data['dob'],
        $_SESSION['uID']
    ];

    if (update($query, $values, 'ssssss')) {
        $_SESSION['uName'] = $frm_data['name'];
        echo 1;
    } else {
        echo 0;
    }
}


if (isset($_POST['profile_form'])) {

    session_start();
    $img = uploadUserImage($_FILES['profile']);
    if ($img == 'inv_img') {
        echo 'inv_img';
        exit;
    } else if ($img == 'upd_failed') {
        echo 'upd_failed';
        exit;
    }

    //fetching old img and delete
    $u_exist = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uID']], "s");
    $u_fetch = mysqli_fetch_assoc($u_exist);

    deleteImage($u_fetch['profile'], USER_FOLDER);

    $query = "UPDATE `user_cred` SET `profile`=? WHERE `id`=? LIMIT 1";

    $values = [$img, $_SESSION['uID']];

    if (update($query, $values, 'ss')) {
        $_SESSION['uPic'] = $img;
        echo 1;
    } else {
        echo 0;
    }
}

if (isset($_POST['pass_form'])) {

    $frm_data = filtration($_POST);

    session_start();
    $u_exist = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$_SESSION['uID']], "s");
    if (!$u_exist || mysqli_num_rows($u_exist) == 0) {
        echo 'no_user';
        exit;
    }

    $u_fetch = mysqli_fetch_assoc($u_exist);

    // Kiểm tra mật khẩu hiện tại
    if (!password_verify($frm_data['current_pass'], $u_fetch['password'])) {
        echo 'invalid_current_pass'; // Mật khẩu cũ không khớp
        exit;
    }

    if ($frm_data['new_pass'] != $frm_data['confirm_pass']) {
        echo 'mismatch';
        exit;
    }

    $enc_pass = password_hash($frm_data['new_pass'], PASSWORD_BCRYPT);

    $query = "UPDATE `user_cred` SET `password`=? WHERE `id`=? LIMIT 1";

    $values = [$enc_pass, $_SESSION['uID']];

    if (update($query, $values, 'ss')) {
        echo 1;
    } else {
        echo 0;
    }
}
