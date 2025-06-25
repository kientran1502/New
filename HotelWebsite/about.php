<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/link.php') ?>
    <title><?php echo $settings_r['site_title'] ?> - About</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .box {
            border-top-color: var(--teal) !important;
        }

        .resize-image {
            width: 300px;
            /* Đặt chiều rộng cố định */
            height: 300px;
            /* Đặt chiều cao cố định */
            object-fit: contain;
            /* Đảm bảo ảnh không bị méo */
        }
    </style>
</head>

<body class="bg-light">
    <!-- header -->
    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold h-font text-center">Introduction</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-lg-6 col-md-5 mb-4 order-lg-1 order-md-1 order-2">
                <h3 class="mb-3">Lorem ipsum dolor sit amet consectetur.</h3>
                <p>Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                    Ullam veniam dolorum voluptatum delectus quas natus pariatur modi aliquam!
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                    Ullam veniam dolorum voluptatum delectus quas natus pariatur modi aliquam!
                </p>
            </div>
            <div class="con-lg-5 col-md-5 mb-4 order-lg-2 order-md-2 order-1">
                <img src="img/about/img.jpg" class="w-100" />
            </div>
        </div>
    </div>

    <div class="container mt-5">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="img/about/pic2.jpg" width="70px" />
                    <h4 class="mt-3">100+ Sân</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="img/about/pic2.jpg" width="70px" />
                    <h4 class="mt-3">100+ Sân</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="img/about/pic2.jpg" width="70px" />
                    <h4 class="mt-3">100+ Sân</h4>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4 px-4">
                <div class="bg-white rounded shadow p-4 border-top border-4 text-center box">
                    <img src="img/about/pic2.jpg" width="70px" />
                    <h4 class="mt-3">100+ Sân</h4>
                </div>
            </div>
        </div>
    </div>

    <h3 class="my-5 fw-bold h-font text-center">Admin Team</h3>

    <div class="container px-4">
        <div class="swiper mySwiper">
            <div class="swiper-wrapper mb-5">

                <?php
                $about_r = selectAll('team_details');
                $path = ABOUT_IMG_PATH;

                while ($row = mysqli_fetch_assoc($about_r)) {
                    echo <<<data
                        <div class="swiper-slide bg-white text-center overflow-hidden rounded">
                            <img src="$path$row[picture]" class="resize-image w-100">
                            <h2 class="mt-2">$row[name]</h2>
                        </div>
                    data;
                }
                ?>


            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>


    <!-- Footer-->
    <?php require('inc/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".mySwiper", {
            pagination: {
                el: ".swiper-pagination",
                dynamicBullets: false, // Hiển thị các chấm phân trang nhỏ
            },
            slidesPerView: 2, // Mặc định là 2 slide hiển thị
            spaceBetween: 90, // Khoảng cách giữa các slide
            breakpoints: {
                320: {
                    slidesPerView: 1, // Khi màn hình nhỏ hơn 320px, hiển thị 1 slide
                },
                640: {
                    slidesPerView: 1, // Khi màn hình rộng hơn 640px, hiển thị 1 slide
                },
                768: {
                    slidesPerView: 2, // Khi màn hình rộng hơn 768px, hiển thị 2 slide
                },
                1024: {
                    slidesPerView: 2, // Khi màn hình rộng hơn 1024px, hiển thị 2 slide
                },
            },
        });
    </script>


</body>

</html>