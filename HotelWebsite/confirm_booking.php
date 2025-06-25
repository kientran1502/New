<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    require('inc/link.php');
    ?>
    <title><?php echo $settings_r['site_title'] ?> - Confirm Booking</title>

    <style>
        .resize-image {
            height: 240px;
            object-fit: contain;
        }
    </style>
</head>

<body class="bg-light">
    <!-- header -->
    <?php
    require('inc/header.php');

    $clientId = 'AWgPzkEO8ShdDdGoZxd2cv3sIDLLrfOLkSJv1I4LECYorl10bvokXWozHeKD5QwRlzWkua99U14z1-Pm';
    $clientSecret = 'ELDMwMK6z9-VNKHCyyzrKdDZ-bCSvvISvnffhwR95jULB9sTNuM8w-tfw70WtXYlYvXK964tYUE9Pfkb';

    /// Kiểm tra và xóa thông tin PayPal trong session
    if (isset($_SESSION['paypal_token'])) {
        unset($_SESSION['paypal_token']);  // Xóa token PayPal khỏi session
    }
    // Xóa cookie liên quan đến PayPal nếu có
    if (isset($_COOKIE['paypal_token'])) {
        setcookie('paypal_token', '', time() - 3600, '/');  // Đặt thời gian hết hạn trong quá khứ để xóa cookie
        unset($_COOKIE['paypal_token']);
    }

    /*
        check room id from url is present or not
        shutdown mode is active or not
        user is login or not
    */

    if (!isset($_GET['id']) || $settings_r['shutdown'] == true) {
        redirect('room.php');
    } else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('room.php');
    }




    //filter get room data

    $data = filtration($_GET);

    $room_res = select("SELECT * FROM `room` WHERE `id`=? AND `status`=?  AND `removed`=?", [$data['id'], 1, 0], 'iii');

    if (mysqli_num_rows($room_res) == 0) {
        redirect('room.php');
    }
    $room_data = mysqli_fetch_assoc($room_res);

    $_SESSION['room'] = [
        "id" => $room_data['id'],
        "name" => $room_data['name'],
        "price" => $room_data['price'],
        "payment" => null,
        "available" => false,
    ];


    $user_res = select("SELECT * FROM `user_cred` WHERE `id` = ? LIMIT 1", [$_SESSION['uID']], "i"); // chu y uID deo phai uId 
    $user_data = mysqli_fetch_assoc($user_res);


    ?>

    <div class="container">
        <div class="row">

            <div class="col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold">Confirm Booking</h2>
                <div style="font-size: 14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="room.php" class="text-secondary text-decoration-none">ROOMS</a>
                    <span class="text-secondary"> > </span>
                    <a href="#" class="text-secondary text-decoration-none">CONFIRM</a>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 px-4">
                <?php
                $room_thumb = ROOM_IMG_PATH . "thumbnail.jpg";
                $thumb_q = mysqli_query($con, "SELECT * FROM `room_image`
                        WHERE `room_id`='$room_data[id]'
                        AND `thumb`='1' ");

                if (mysqli_num_rows($thumb_q) > 0) {
                    $thumb_res = mysqli_fetch_assoc($thumb_q);
                    $room_thumb = ROOM_IMG_PATH . $thumb_res['image'];
                }

                echo <<<data
                    <div class="card p-3 shadow-sm rounded">
                        <img src="$room_thumb" class="img-fluid rounded mb-3">
                        <h5>$room_data[name]</h5>
                        <h6>$room_data[price]＄per night</h6>

                    </div>
                data;

                ?>

                <div id="roomCarousel" class="carousel slide" data-bs-ride="carousel">

                    <button class="carousel-control-prev" type="button" data-bs-target="#roomCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#roomCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>

            </div>

            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <form action="pay_now.php" method="POST" id="booking_form">
                            <h6 class="mb-3">BOOKING DETAILS</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label ">Name</label>
                                    <input name="name" type="text" value="<?php echo $user_data['name'] ?>" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label ">Phone Number</label>
                                    <input name="phonenum" type="number" value="<?php echo $user_data['phonenum'] ?>" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label ">Address</label>
                                    <textarea name="address" class="form-control shadow-none" rows="1" required><?php echo $user_data['address'] ?></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label ">Check-in</label>
                                    <input name="checkin" type="date" onchange="check_availability()" class="form-control shadow-none" required>
                                </div>
                                <div class="col-md-6 mb-">
                                    <label class="form-label ">Check-out</label>
                                    <input name="checkout" type="date" onchange="check_availability()" class="form-control shadow-none" required>
                                </div>
                                <div class="col-12">
                                    <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <h6 class="mb-3 text-danger" id="pay_info">Provide check-in & check-out date!</h6>
                                    <button name="pay_now" class="btn w-100 text-white custom-bg shadow-none mb-1" disabled>Pay Now</button>
                                </div>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <!-- Footer-->
        <?php require('inc/footer.php'); ?>
        <script>
            let booking_form = document.getElementById('booking_form');
            let info_loader = document.getElementById('info_loader');
            let pay_info = document.getElementById('pay_info');

            function check_availability() {
                let checkin_val = booking_form.elements['checkin'].value;
                let checkout_val = booking_form.elements['checkout'].value;

                booking_form.elements['pay_now'].setAttribute('disabled', true);


                if (checkin_val != '' && checkout_val != '') {

                    pay_info.classList.add('d-none');
                    pay_info.classList.replace('text-dark', 'text-danger');
                    info_loader.classList.remove('d-none');

                    let data = new FormData();
                    data.append('check_availability', '');
                    data.append('check_in', checkin_val);
                    data.append('check_out', checkout_val);

                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "ajax/confirm_booking.php", true);

                    xhr.onload = function() {
                        let data = JSON.parse(this.responseText);
                        if (data.status == 'check_in_out_equal') {
                            pay_info.innerText = "You cannot check-out on the same day!"
                        } else if (data.status == 'check_out_earlier') {
                            pay_info.innerText = "Check-out date is earlier than check-in date!"
                        } else if (data.status == 'check_in_earlier') {
                            pay_info.innerText = "Check-in date is earlier than today's date!"
                        } else if (data.status == 'unavailable') {
                            pay_info.innerText = "Room not available for this check-in date!"
                        } else {
                            pay_info.innerHTML = "No of Days: " + data.days + "<br>Total Amount to Pay: " + data.payment + "$";
                            pay_info.classList.replace('text-danger', 'text-dark');
                            booking_form.elements['pay_now'].removeAttribute('disabled');
                        }
                        pay_info.classList.remove('d-none');
                        info_loader.classList.add('d-none');
                    }

                    xhr.send(data);
                }

            }
        </script>

        <script src="https://www.paypal.com/sdk/js?client-id=<?php echo $clientId; ?>&currency=USD"></script>



</body>

</html>