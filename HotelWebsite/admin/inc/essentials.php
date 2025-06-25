

<?php

//frontend purpose data 

define('SITE_URL', 'http://127.0.0.1/MyProject/');
define('ABOUT_IMG_PATH', SITE_URL . 'img/about/');
define('CAROUSEL_IMG_PATH', SITE_URL . 'img/carousel/');
define('FACILITIES_IMG_PATH', SITE_URL . 'img/facilities/');
define('ROOM_IMG_PATH', SITE_URL . 'img/room/');
define('USER_IMG_PATH', SITE_URL . 'img/user/');



// backend upload process needs this data!!!

define('UPLOAD_IMAGE_PATH', $_SERVER['DOCUMENT_ROOT'] . '/MyProject/img/');
define('ABOUT_FOLDER', 'about/');
define('CAROUSEL_FOLDER', 'carousel/');
define('FACILITIES_FOLDER', 'facilities/');
define('ROOM_FOLDER', 'room/');
define('USER_FOLDER', 'user/');

// sendgrid api key
define('SENDGRID_API_KEY', "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");
define('SENDGRID_EMAIL', "trungkiencp2@gmail.com");
define('SENDGRID_NAME', "Viva la Vida");


function adminLogin()
{
    session_start();
    if (!(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin'] == true)) {
        redirect('index.php');
    }
}

function redirect($url)
{
    echo "<script>
            window.location.href='$url';
        </script>";
    exit;
}

function alert($type, $msg)
{
    $bs_class = ($type == "success") ? "alert-success" : "alert-danger";
    echo <<<alert
            <div class="alert $bs_class alert-dismissible fade show custom-alert" role="alert">
                <strong class="me-3">$msg</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        alert;
}
function uAlert($type, $msg)
{
    $bs_class = ($type == "success") ? "alert-success" : "alert-danger";
    echo <<<alert
            <div class="alert $bs_class alert-dismissible fade show setting-alert" role="alert">
                <strong class="me-3">$msg</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        alert;
}

function uploadImage($image, $folder)
{
    // Các loại MIME hợp lệ cho tệp hình ảnh
    $valid_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

    // Lấy loại MIME của tệp tải lên
    $img_mime = $image['type'];

    // Kiểm tra nếu loại MIME không nằm trong danh sách hợp lệ
    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img'; // Trả về 'inv_img' nếu loại tệp không hợp lệ
    }
    // Kiểm tra kích thước tệp (giới hạn dưới 50MB)
    else if (($image['size'] / (1024 * 1024)) > 500) {
        return 'inv_size'; // Trả về 'inv_size' nếu kích thước tệp vượt quá giới hạn
    } else {
        // Lấy phần mở rộng của tệp (vd: jpg, png)
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        // Tạo tên mới cho tệp với tiền tố 'IMG_' và số ngẫu nhiên
        $rname = 'IMG_' . random_int(11111, 99999) . ".$ext";

        // Đường dẫn lưu tệp hình ảnh
        $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;

        // Di chuyển tệp tải lên vào thư mục đích
        if (move_uploaded_file($image['tmp_name'], $img_path)) {
            return $rname; // Trả về tên tệp nếu thành công
        } else {
            return 'upd_failed'; // Trả về 'upd_failed' nếu di chuyển tệp thất bại
        }
    }
}

function deleteImage($image, $folder)
{
    if (unlink(UPLOAD_IMAGE_PATH . $folder . $image)) {
        return true;
    } else {
        return false;
    }
}

function uploadSVGImage($image, $folder)
{
    // Các loại MIME hợp lệ cho tệp hình ảnh
    $valid_mime = ['image/svg+xml'];

    // Lấy loại MIME của tệp tải lên
    $img_mime = $image['type'];

    // Kiểm tra nếu loại MIME không nằm trong danh sách hợp lệ
    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img'; // Trả về 'inv_img' nếu loại tệp không hợp lệ
    }
    // Kiểm tra kích thước tệp (giới hạn dưới 50MB)
    else if (($image['size'] / (1024 * 1024)) > 500) {
        return 'inv_size'; // Trả về 'inv_size' nếu kích thước tệp vượt quá giới hạn
    } else {
        // Lấy phần mở rộng của tệp (vd: jpg, png)
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        // Tạo tên mới cho tệp với tiền tố 'IMG_' và số ngẫu nhiên
        $rname = 'IMG_' . random_int(11111, 99999) . ".$ext";

        // Đường dẫn lưu tệp hình ảnh
        $img_path = UPLOAD_IMAGE_PATH . $folder . $rname;

        // Di chuyển tệp tải lên vào thư mục đích
        if (move_uploaded_file($image['tmp_name'], $img_path)) {
            return $rname; // Trả về tên tệp nếu thành công
        } else {
            return 'upd_failed'; // Trả về 'upd_failed' nếu di chuyển tệp thất bại
        }
    }
}

function uploadUserImage($image)
{
    $valid_mime = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];

    $img_mime = $image['type'];

    if (!in_array($img_mime, $valid_mime)) {
        return 'inv_img';
    } else {

        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);

        $rname = 'IMG_' . random_int(11111, 99999) . ".jpeg";


        $img_path = UPLOAD_IMAGE_PATH . USER_FOLDER . $rname;

        if ($ext == 'png' || $ext == 'PNG') {
            $img = imagecreatefrompng($image['tmp_name']);
        } else if ($ext == 'webp' || $ext == 'WEBP') {
            $img = imagecreatefromwebp($image['tmp_name']);
        } else {
            $img = imagecreatefromjpeg($image['tmp_name']);
        }

        if (imagejpeg($img, $img_path, 75)) {
            return $rname;
        } else {
            return 'upd_failed';
        }
    }
}


?>