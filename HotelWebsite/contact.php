<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require('inc/link.php') ?>
    <title><?php echo $settings_r['site_title'] ?> - Contact</title>

</head>

<body class="bg-light">
    <!-- header -->
    <?php require('inc/header.php'); ?>

    <div class="my-5 px-4">
        <h2 class="fw-bold h-font text-center">Liên hệ với chúng tôi</h2>
        <div class="h-line bg-dark"></div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-5 px-4">
                <div class="bg-white rounded shadow p-4">
                    <iframe class="w-100 rounded mb-4" height="320px" src="<?php echo $contact_r['iframe'] ?>" loading="lazy"></iframe>
                    <h5>Địa chỉ</h5>
                    <a href="<?php echo $contact_r['gmap'] ?>" target="_blank" class="d-inline-block text-decoration-none text-dark mb-2">
                        <i class="bi bi-geo"></i> <?php echo $contact_r['address'] ?>
                    </a>
                    <h5 class="mt-4">Liên lạc</h5>
                    <a href="SĐT: <?php echo $contact_r['pn1'] ?>" class="d-inline-block mb-2 text-decoration-none text-dark">
                        <i class="bi bi-telephone-inbound"></i> <?php echo $contact_r['pn1'] ?>
                    </a>
                    <br>
                    <a href="SĐT: <?php echo $contact_r['pn2'] ?>" class="d-inline-block text-decoration-none text-dark">
                        <i class="bi bi-telephone-inbound"></i> <?php echo $contact_r['pn2'] ?>
                    </a>
                    <h5 class="mt-4">Email</h5>
                    <a href="mailto: <?php echo $contact_r['email'] ?>" class="d-inline-block text-decoration-none text-dark">
                        <i class="bi bi-envelope-at"></i> <?php echo $contact_r['email'] ?>
                    </a>

                    <h5 class="mt-4">Follow us</h5>

                    <a href="<?php echo $contact_r['tw'] ?>" target="_blank" class="d-inline-block text-dark fs-5 me-2">
                        <i class="bi bi-twitter me-1"></i>
                    </a>
                    <a href="<?php echo $contact_r['fb'] ?>" target="_blank" class="d-inline-block text-dark fs-5 me-2">
                        <i class="bi bi-facebook me-1"></i>
                    </a>
                    <a href="<?php echo $contact_r['insta'] ?>" target="_blank" class="d-inline-block text-dark fs-5">
                        <i class="bi bi-instagram me-1"></i>
                    </a>

                </div>
            </div>
            <div class="col-lg-6 col-md-6 mb-5 px-4">
                <div class="bg-white rounded shadow p-4 ">
                    <form method="POST">
                        <h5>Gửi lời nhắn</h5>
                        <div class="mt-3">
                            <label class="form-label" style="font-weight: 500;">Họ tên</label>
                            <input name="name" required type="text" class="form-control shadow-none">
                        </div>
                        <div class="mt-3">
                            <label class="form-label" style="font-weight: 500;">Email</label>
                            <input name="email" required type="email" class="form-control shadow-none">
                        </div>
                        <div class="mt-3">
                            <label class="form-label" style="font-weight: 500;">Chủ đề</label>
                            <input name="subject" required type="text" class="form-control shadow-none">
                        </div>
                        <div class="mt-3">
                            <label class="form-label" style="font-weight: 500;">Lời nhắn</label>
                            <textarea name="message" required class="form-control shadow-none" rows="5" style="resize: none;"></textarea>
                        </div>
                        <button type="submit" name="send" class="btn text-white custom-bg mt-3">Gửi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <?php
    if (isset($_POST['send'])) {
        // Làm sạch dữ liệu
        $frm_data = filtration($_POST);

        // Chuẩn bị câu truy vấn
        $q = "INSERT INTO `user_queries`(`name`, `email`, `subject`, `message`) VALUES (?,?,?,?)";
        $values = [$frm_data['name'], $frm_data['email'], $frm_data['subject'], $frm_data['message']];

        // Chèn dữ liệu vào cơ sở dữ liệu
        $res = insert($q, $values, 'ssss');

        if ($res == 1) {
            // Gửi thành công
            alert('success', 'Mail sent successfully!');
        } else {
            // Xảy ra lỗi
            alert('error', 'Failed to send the message. Please try again.');
        }
    }
    ?>


    <!-- Footer-->
    <?php require('inc/footer.php'); ?>

</body>

</html>