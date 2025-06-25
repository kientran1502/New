<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/link.php') ?>
    <title><?php echo $settings_r['site_title'] ?> - Rooms</title>
    <style>
        .resize-image {
            height: 240px;
            object-fit: contain;
        }
    </style>
</head>

<body class="bg-light">
    <!-- header -->
    <?php require('inc/header.php');

    $checkin_default = "";
    $checkout_default = "";
    $adult_default = "";
    $children_default = "";



    if (isset($_GET['check_avail'])) {
        $frm_data = filtration($_GET);
        $checkin_default = $frm_data['checkin'];
        $checkout_default = $frm_data['checkout'];
        $adult_default = $frm_data['adult'];
        $children_default = $frm_data['children'];
    }

    ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold h-font text-center">Our Rooms</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <div class="container-fluid">
        <div class="row">

            <div class="col-lg-3 col-md-12 mb-lg-0 mb-4 ps-4">
                <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow">
                    <div class="container-fluid flex-lg-column align-items-stretch">
                        <h4 class="mt-4">FILTERS</h4>
                        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersDrop" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse flex-column align-items-stretch mt-2" id="filtersDrop">
                            <!-- check availablity logic -->
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                    <span>Check availability</span>
                                    <button id="chk_avail_btn" onclick="chk_avail_clear()" class="btn btn-sm shadow-none text-secondary d-none">Reset</button>
                                </h5>

                                <label class="form-label">Check In</label>
                                <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkin_default ?>" id="checkin" onchange="chk_avail_filter()">
                                <label class="form-label">Check Out</label>
                                <input type="date" class="form-control shadow-none mb-3" value="<?php echo $checkout_default ?>" id="checkout" onchange="chk_avail_filter()">
                            </div>

                            <!-- Guest -->
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                    <span>Guests</span>
                                    <button id="guest_btn" onclick="guest_clear()" class="btn btn-sm shadow-none text-secondary d-none">Reset</button>
                                </h5>
                                <div class="d-flex">
                                    <div class="me-3">
                                        <label class="form-label">Adults</label>
                                        <input type="number" min="1" id="adult" value="<?php echo $adult_default ?>" oninput="guest_filter()" class="form-control shadow-none">
                                    </div>
                                    <div>
                                        <label class="form-label">Children</label>
                                        <input type="number" min="0" id="children" value="<?php echo $children_default ?>" oninput="guest_filter()" class="form-control shadow-none">
                                    </div>

                                </div>
                            </div>
                            <!-- facitily -->
                            <div class="border bg-light p-3 rounded mb-3">
                                <h5 class="d-flex align-items-center justify-content-between mb-3" style="font-size: 18px;">
                                    <span>Facility</span>
                                    <button id="facility_btn" onclick="facility_clear()" class="btn btn-sm shadow-none text-secondary d-none">Reset</button>
                                </h5>

                                <?php

                                $facilities_q = selectAll('facilities');
                                while ($row = mysqli_fetch_assoc($facilities_q)) {

                                    echo <<<facilities
                                            <div class="mb-2">
                                                <input type="checkbox" onclick="fetch_room()" name="facilities" value="$row[id]" class="form-check-input shadow-none me-1" id="$row[id]">
                                                <label class="form-check-label" for="$row[id]">$row[name]</label>
                                            </div>
                                    facilities;
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>

            <div class="col-lg-9 col-md-12 px-4" id="room-data">

            </div>

        </div>
    </div>

    <script>
        let room_data = document.getElementById('room-data');
        let checkin = document.getElementById('checkin');
        let checkout = document.getElementById('checkout');
        let chk_avail_btn = document.getElementById('chk_avail_btn');

        let adult = document.getElementById('adult');
        let children = document.getElementById('children');
        let guest_btn = document.getElementById('guest_btn');

        let facility_btn = document.getElementById('facility_btn');

        function fetch_room() {

            let chk_avail = JSON.stringify({
                checkin: checkin.value,
                checkout: checkout.value

            });

            let guest = JSON.stringify({
                adult: adult.value,
                children: children.value

            });

            let facilities_list = {
                "facilities": []
            };

            let get_facilities = document.querySelectorAll('[name="facilities"]:checked')
            if (get_facilities.length > 0) {
                get_facilities.forEach((facility) => {
                    facilities_list.facilities.push(facility.value);
                })
                facility_btn.classList.remove('d-none');
            } else {
                facility_btn.classList.add('d-none');
            }
            facilities_list = JSON.stringify(facilities_list);

            let xhr = new XMLHttpRequest();
            xhr.open("GET", "ajax/room.php?fetch_room&chk_avail=" + chk_avail + "&guest=" + guest + "&facilities_list=" + facilities_list, true);
            xhr.onprogress = function() {
                room_data.innerHTML = `<div class="spinner-border text-info mb-3 d-block mx-auto" id="loader" role="status">
                            <span class="visually-hidden">Loading ...</span>
                        </div>`;
            }

            xhr.onload = function() {
                room_data.innerHTML = this.responseText;

            }
            xhr.send();
        }


        function chk_avail_filter() {
            if (checkin.value != '' && checkout.value != '') {
                fetch_room();
                chk_avail_btn.classList.remove('d-none');
            }
        }


        function chk_avail_clear() {
            checkin.value = ''
            checkout.value = ''
            chk_avail_btn.classList.add('d-none');
            fetch_room();

        }

        function guest_filter() {
            if (adult.value > 0 || children.value >= 0) {
                fetch_room();
                guest_btn.classList.remove('d-none');
            }

        }

        function guest_clear() {
            adult.value = ''
            children.value = ''
            guest_btn.classList.add('d-none');
            fetch_room();

        }

        function facility_clear() {
            let get_facilities = document.querySelectorAll('[name="facilities"]:checked')
            get_facilities.forEach((facility) => {
                facility.checked = false;
            })
            facility_btn.classList.add('d-none');
            fetch_room();

        }

        window.onload = function() {
            fetch_room();

        }
    </script>



    <!-- Footer-->
    <?php require('inc/footer.php'); ?>

</body>

</html>