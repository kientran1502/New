<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php require('inc/link.php') ?>
    <title><?php echo $settings_r['site_title'] ?> - Home</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .search-form {
            margin-top: -100px;
            z-index: 2;
            position: relative;
        }

        @media screen and (max-width: 575px) {
            .search-date {
                margin-top: 25px;
                padding: 0 35px;
            }
        }

        .resize-image {
            height: 540px;
            object-fit: cover;
        }

        .resize-thumb {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>

<body class="bg-light">
    <!-- header -->
    <?php require('inc/header.php'); ?>

    <!--slide ảnh -->
    <div class="container-fluid">
        <div class="swiper swiper-container">
            <div class="swiper-wrapper">
                <?php
                $res = selectAll('carousel');
                while ($row = mysqli_fetch_assoc($res)) {
                    $path = CAROUSEL_IMG_PATH;
                    echo <<<data
                        <div class="swiper-slide">
                            <img src="$path$row[image]" class="w-100 resize-image d-block" />
                        </div>
                    data;
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Tìm phong -->
    <div class="container search-form">
        <div class="row">
            <div class="col-lg-12 bg-white shadow p-4 rounded">
                <h5 class="mb-4">Check Booking Availability</h5>
                <form class action="room.php">
                    <div class="row align-items-end ">
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight:500;">Check-in</label>
                            <input type="date" class="form-control shadow_none" name="checkin" required>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight:500;">Check-out</label>
                            <input type="date" class="form-control shadow_none" name="checkout" required>
                        </div>
                        <div class="col-lg-3 mb-3">
                            <label class="form-label" style="font-weight:500;">Adult</label>
                            <select class="form-select shadow-none" name="adult">

                                <?php
                                $guest_q = mysqli_query($con, "SELECT MAX(adult) as `max_adult`, MAX(children) AS `max_children` 
                                    FROM `room` WHERE `status`='1' AND `removed`='0'");
                                $guest_res = mysqli_fetch_assoc($guest_q);

                                for ($i = 1; $i <= $guest_res['max_adult']; $i++) {
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-lg-2 mb-3">
                            <label class="form-label" style="font-weight:500;">Children</label>
                            <select class="form-select shadow-none" name="children">
                                <?php
                                for ($i = 1; $i <= $guest_res['max_children']; $i++) {
                                    echo "<option value='$i'>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="check_avail">
                        <div class="col-lg-1 mb-3 ">
                            <button class="btn btn-outline-success" style="font-weight:500;" type="submit">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Hiển thị room -->
    <h2 class="mt-5 pt-4 mb-4 text-center h-font">Rooms</h2>
    <div class="container">
        <div class="row">
            <?php
            $room_res = select("SELECT * FROM `room` WHERE `status`=?  AND `removed`=? ORDER BY `id` DESC  LIMIT 3", [1, 0], 'ii');

            while ($room_data = mysqli_fetch_assoc($room_res)) {

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

                //get facilities of room

                $fac_q = mysqli_query($con, "SELECT f.name FROM `facilities` f
            INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id
            WHERE rfac.room_id = '$room_data[id]' ");

                $facilities_data = "";
                while ($fac_row = mysqli_fetch_assoc($fac_q)) {
                    $facilities_data .= "<span class='badge rounded-pill bg-light text-dark text-warp'>
                $fac_row[name]
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
                    $book_btn = " <button onclick='checkLoginToBook($login,$room_data[id])'  class='btn btn-sm text-white custom-bg shadow-none'>Book Now</button>";
                }

                $rating_q = "SELECT AVG(rating) AS `avg_rating` FROM `rating_review`
                    WHERE `room_id`='$room_data[id]' ORDER BY `sr_no` DESC LIMIT 20";

                $rating_res = mysqli_query($con, $rating_q);

                $rating_fetch = mysqli_fetch_assoc($rating_res);
                $rating_data = "";

                if ($rating_fetch['avg_rating'] != NULL) {
                    $rating_data = "<div class='rating mb-3'>
                                    <h6>Rating</h6>
                                    <span class='badge rounded-fill bg-light text-dark'>";
                    for ($i = 0; $i < $rating_fetch['avg_rating']; $i++) {
                        $rating_data .= "<i class='bi bi-star-fill text-warning'></i> ";
                    }
                    $rating_data .= "</span>
                                </div>";
                } else {
                    $rating_data = "<div class='rating mb-3'>
                                        <h6>Rating</h6>
                                        <span class='badge rounded-fill bg-light text-dark'>No review yet</span>
                                    </div>";
                }

                //show room

                echo <<<data
                    <div class="col-lg-4 col-md-6 my-3">
                        <div class="card border-0 shadow" style="width: 350px; margin: auto">
                            <img src="$room_thumb" class="card-img-top resize-thumb">
                            <div class="card-body">
                                <h5>$room_data[name]</h5>
                                <h6 class="mb-2">$room_data[price]$ per night</h6>
                                <div class="feature mb-4" ">
                                    <h6 class="mb-1"> Feature </h6>
                                    $feature_data
                                </div>
                                <div class="facilities mb-4" ">
                                    <h6 class="mb-1">Facilities</h6>
                                    $facilities_data
                                </div>
                                <div class="guest mb-3" >
                                    <h6 class="mb-1">Guest</h6>
                                    <span class="badge rounded-pill bg-light text-dark">
                                        $room_data[adult] Adults
                                    </span>
                                    <span class="badge rounded-pill bg-light text-dark">
                                        $room_data[children] Children
                                    </span>
                                </div>
                                $rating_data
                                <div class="d-flex justify-content-between ">
                                    $book_btn
                                    <a href="room_detail.php?id=$room_data[id]" class="btn btn-sm btn-outline-dark shadow-none">More Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                data;
            }
            ?>

            <div class="col-lg-12 text-center mt-5">
                <a href="room.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Rooms >>></a>
            </div>
        </div>
    </div>

    <h2 class="mt-5 pt-4 mb-4 text-center h-font">Our Facilities</h2>
    <div class="container">
        <div class="row justify-content-evenly px-lg-0 px-md-0 px-5">
            <?php
            $res = mysqli_query($con, "SELECT * FROM `facilities` ORDER BY `id` DESC  LIMIT 5 ");
            $path = FACILITIES_IMG_PATH;

            while ($row = mysqli_fetch_assoc($res)) {
                echo <<<data
                    <div class="col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3">
                        <img src="$path$row[icon]" width="50px">
                        <h5 class="mt-3">$row[name]</h5>
                    </div>
                data;
            }
            ?>
            <div class="col-lg-12 text-center mt-5">
                <a href="facilities.php" class="btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none">More Facilities >>></a>
            </div>
        </div>
    </div>

    <h2 class="mt-5 pt-4 mb-4 text-center h-font">Testimonials</h2>
    <div class="container mt-5">
        <div class="swiper swiper-testimonials">
            <div class="swiper-wrapper">
                <?php
                $review_q = "SELECT rr. * ,uc.name AS uname, uc.profile, r.name AS rname FROM `rating_review` rr
                            INNER JOIN `user_cred` uc ON rr.user_id = uc.id
                            INNER JOIN `room` r ON rr.room_id = r.id
                            ORDER BY `sr_no` DESC LIMIT 6";
                $review_res = mysqli_query($con, $review_q);
                $img_path = USER_IMG_PATH;
                if (mysqli_num_rows($review_res) == 0) {
                    echo '<div class="d-flex align-items-center">No review yet!</div>';
                } else {
                    while ($row = mysqli_fetch_assoc($review_res)) {
                        $stars = "<i class='bi bi-star-fill text-warning'></i>";
                        for ($i = 1; $i <= $row['rating']; $i++) {
                            $stars .= "<i class='bi bi-star-fill text-warning'></i>";
                        }
                        echo <<<slides
                            <div class="swiper-slide bg-white p-4">
                                <div class="profile d-flex align-items-center mb-3">
                                    <img src="$img_path$row[profile]" class="rounded-circle" loading="lazy" width="30px">
                                    <h6 class="m-0 ms-2">$row[uname]</h6>
                                </div>
                                <div class="profile d-flex align-items-center mb-2">Room Name: $row[rname]</div>
                                <p>
                                    $row[review]
                                </p>
                                <div class="rating">
                                    $stars
                                </div>
                            </div>
                        slides;
                    }
                }
                ?>
            </div>
            <!-- <div class="swiper-pagination"></div> -->
        </div>

    </div>



    <!--pass reset model  -->
    <div class="modal fade" id="recoveryModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="recovery-form">
                    <div class="modal-header">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="bi bi-shield-lock fs-3 me-2"></i> Set up New Password
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label">New Password</label>
                            <input type="password" name="pass" required class="form-control shadow-none">
                            <input type="hidden" name="email">
                            <input type="hidden" name="token">
                        </div>
                        <div class="mb-2 text-end">
                            <button type="button" class="btn shadow-none me-2" data-bs-dismiss="modal"> Cancel</button>
                            <button type="submit" class="btn btn-dark shadow_none">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Footer-->
    <?php require('inc/footer.php'); ?>

    <?php
    if (isset($_GET['account_recovery'])) {
        $data = filtration($_GET);

        $t_date = date("Y-m-d");

        $query = select(
            "SELECT * FROM `user_cred` WHERE `email`=? AND `token`=? AND `t_expire`=? LIMIT 1",
            [$data['email'], $data['token'], $t_date],
            'sss'
        );

        if (mysqli_num_rows($query) == 1) {
            echo <<<showModal
            <script>
                var myModal = document.getElementById('recoveryModel');
                myModal.querySelector("input[name='email']").value = '$data[email]';
                myModal.querySelector("input[name='token']").value = '$data[token]';

                var modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.show()
            </script>
            showModal;
            alert('success', 'Link is Good :)');
        } else {
            alert('error', 'Invalid or Expired Link!');
        }
    }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".swiper-testimonials", {
            effect: "coverflow",
            grabCursor: true,
            loop: true,
            centeredSlides: true,
            slidesPerView: "2",
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: false,
            },
            pagination: {
                el: ".swiper-pagination",
            },
            breakpoints: {
                320: {
                    slidesPerView: "1",
                },
                640: {
                    slidesPerView: "1",
                },
                768: {
                    slidesPerView: "1",
                },
                1024: {
                    slidesPerView: "2",
                },
            }
        });



        var swiper = new Swiper(".swiper-container", {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            effect: "fade",
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },

        });

        let recovery_form = document.getElementById('recovery-form');

        recovery_form.addEventListener('submit', (e) => {
            e.preventDefault();

            let data = new FormData();

            data.append('email', recovery_form.elements['email'].value);
            data.append('token', recovery_form.elements['token'].value);
            data.append('pass', recovery_form.elements['pass'].value);
            data.append('recover_user', '');

            var myModal = document.getElementById('recoveryModel');
            var modal = bootstrap.Modal.getInstance(myModal);
            modal.hide()

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "ajax/login_register.php", true);

            xhr.onload = function() {
                if (this.responseText.trim() == 'failed') {
                    setAlert('error', "Account reset failed!");
                } else {
                    setAlert('success', "Account reset successful!!");
                    recovery_form.reset();
                }

            }

            xhr.send(data);

        });
    </script>
</body>

</html>