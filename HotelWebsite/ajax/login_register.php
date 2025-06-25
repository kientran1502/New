<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');
require("../inc/sendgrid/sendgrid-php.php");
date_default_timezone_set("Asia/Ho_chi_Minh");


function send_mail($uemail, $token, $type)
{
    if (!filter_var($uemail, FILTER_VALIDATE_EMAIL)) {
        return "invalid_email";
    }

    if ($type == "email_confirmation") {
        $page = 'email_confirm.php';
        $subject = "Account Verification Link";
        $content = "confirm your email";
    } else {
        $page = 'index.php';
        $subject = "Account Reset Link";
        $content = "reset your account";
    }

    $email = new \SendGrid\Mail\Mail();
    $email->setFrom(SENDGRID_EMAIL, SENDGRID_NAME);
    $email->setSubject($subject);
    $email->addTo($uemail);
    $email->addContent(
        "text/html",
        "
            Click the link to $content: <br>
            <a href='" . SITE_URL . "$page?$type&email=$uemail&token=$token" . "'>
                CLICK ME
            </a>
        "
    );
    $sendgrid = new \SendGrid(SENDGRID_API_KEY);

    try {
        $sendgrid->send($email);
        return 1;
    } catch (Exception $e) {
        return 0;
    }
}

if (isset($_POST['register'])) {

    $data = filtration($_POST);

    //kiem tra giua pass va cpass

    if ($data['pass'] != $data['cpass']) {
        echo 'pass_mismatch';
        exit;
    }

    //kiem tra user ton tai 
    $u_exist = select("SELECT * FROM `user_cred` WHERE `email` = ? OR `phonenum` = ? LIMIT 1", [$data['email'], $data['phonenum']], "ss");

    if (mysqli_num_rows($u_exist) != 0) {
        $u_exist_fetch = mysqli_fetch_assoc($u_exist);
        echo ($u_exist_fetch['email'] == $data['email']) ? 'email_already' : 'phone_already';
        exit;
    }

    //upload user img to server

    $img = uploadUserImage($_FILES['profile']);
    if ($img == 'inv_img') {
        echo 'inv_img';
        exit;
    } else if ($img == 'upd_failed') {
        echo 'upd_failed';
        exit;
    }

    //send confirm Links
    $token = bin2hex(random_bytes(16));
    $result = send_mail($data['email'], $token, "email_confirmation");

    if ($result !== 1) {
        echo $result; // In lỗi chi tiết
        exit;
    }

    $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);

    $query = "INSERT INTO `user_cred`(`name`, `email`, `address`, `phonenum`, `pincode`, `dob`, `profile`, `password`
    , `token`) VALUES (?,?,?,?,?,?,?,?,?)";

    $values = [$data['name'], $data['email'], $data['address'], $data['phonenum'], $data['pincode'], $data['dob'], $img, $enc_pass, $token];

    if (insert($query, $values, 'sssssssss')) {
        echo 1;
    } else {
        echo 'ins_failed';
    }
}

if (isset($_POST['login'])) {

    $data = filtration($_POST);

    //kiem tra user ton tai 
    $u_exist = select(
        "SELECT * FROM `user_cred` WHERE `email` = ? OR `phonenum` = ? LIMIT 1",
        [$data['email_mob'], $data['email_mob']],
        "ss"
    );

    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email_mob';
    } else {
        $u_fetch = mysqli_fetch_assoc(($u_exist));
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            if (!password_verify($data['pass'], $u_fetch['password'])) {
                echo 'invalid_pass';
            } else {
                session_start();
                $_SESSION['login'] = true;
                $_SESSION['uID'] = $u_fetch['id'];
                $_SESSION['uName'] = $u_fetch['name'];
                $_SESSION['uPic'] = $u_fetch['profile'];
                $_SESSION['uPhone'] = $u_fetch['phonenum'];
                echo 1;
            }
        }
    }
}

if (isset($_POST['forgot_pass'])) {

    $data = filtration($_POST);

    $u_exist = select(
        "SELECT * FROM `user_cred` WHERE `email` = ? LIMIT 1",
        [$data['email']],
        "s"
    );
    if (mysqli_num_rows($u_exist) == 0) {
        echo 'inv_email';
    } else {
        $u_fetch = mysqli_fetch_assoc(($u_exist));
        if ($u_fetch['is_verified'] == 0) {
            echo 'not_verified';
        } else if ($u_fetch['status'] == 0) {
            echo 'inactive';
        } else {
            //sned reset link to exif_thumbnail
            $token = bin2hex((random_bytes(16)));
            if (!send_mail($data['email'], $token, 'account_recovery')) {
                echo 'mail_failed';
            } else {
                $date = date("Y-m-d");
                $query = mysqli_query($con, "UPDATE `user_cred` SET `token`='$token',`t_expire`='$date' 
                    WHERE `id`='$u_fetch[id]'");

                if ($query) {
                    echo 1;
                } else {
                    echo 'up_failed';
                }
            }
        }
    }
}

if (isset($_POST['recover_user'])) {
    $data = filtration($_POST);
    $enc_pass = password_hash($data['pass'], PASSWORD_BCRYPT);
    $query = "UPDATE `user_cred` SET `password`=? ,`token`= ?,`t_expire`=?
        WHERE `email`= ? AND `token`=? ";
    $values = [$enc_pass, null, null, $data['email'], $data['token']];

    if (update($query, $values, 'sssss')) {
        echo 1;
    } else {
        echo 'failed';
    }
}
