<?php
require('inc/essentials.php');
require('inc/db_config.php');
adminLogin();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Bookings Record </title>
    <?php require('inc/link.php'); ?>
</head>

<body class="bg-white">

    <?php require('inc/adheader.php') ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4">Bookings Record </h3>


                <div class="card border-0 shadow mb-4">
                    <div class="card-body">

                        <div class="text-end mb-4">
                            <input type="text" id="search_input" oninput="get_booking(this.value,1)" class="form-control shadow-noe w-25 ms-auto" placeholder="Type to search">
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover border" style="min-width: 1200px;">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th scope="col">#</th>
                                        <th scope="col">User Details</th>
                                        <th scope="col">Room Details</th>
                                        <th scope="col">Booking Details</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="table-data">
                                </tbody>
                            </table>
                        </div>
                        <nav>
                            <ul class="pagination mt-2" id="table-pagination">
                                <li class='page-item'><button class='page-link'>Prev</button></li>

                            </ul>
                        </nav>





                    </div>
                </div>

            </div>
        </div>
    </div>





    <?php require('inc/script.php'); ?>
    <script src="scripts/booking_record.js"></script>

</body>

</html>