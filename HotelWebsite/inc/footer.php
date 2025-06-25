<div class="container-fluid bg-white mt-5 ">
    <div class="row justify-content-end py-4 my-3 shadow">
        <div class="col-lg-4 p-4 ">
            <h3 class="mb-3"><?php echo $settings_r['site_title'] ?></h3>
            <p>
                <?php echo $settings_r['site_about'] ?>
            </p>
        </div>
        <div class="col-lg-4 p-4">
            <h5 class="mb-3">Links</h5>
            <a href="index.php" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a><br>
            <a href="about.php" class="d-inline-block mb-2 text-dark text-decoration-none">About Us</a><br>
            <a href="function.php" class="d-inline-block mb-2 text-dark text-decoration-none">Facilities</a><br>
            <a href="room.php" class="d-inline-block mb-2 text-dark text-decoration-none">Rooms</a><br>
            <a href="contact.php" class="d-inline-block mb-2 text-dark text-decoration-none">Contact Us</a><br>
        </div>
        <div class="col-lg-4 p-4">
            <h5 class="mb-3">Follow us</h5>
            <a href='<?php echo $contact_r['tw'] ?>' class="d-inline-block text-dark text-decoration-none mb-2">
                <i class="bi bi-twitter me-1"></i> Twitter
            </a><br>
            <a href='<?php echo $contact_r['fb'] ?>' class="d-inline-block text-dark text-decoration-none mb-2">
                <i class="bi bi-facebook me-1"></i> facebook
            </a><br>
            <a href='<?php echo $contact_r['insta'] ?>' class="d-inline-block text-dark text-decoration-none mb-2">
                <i class="bi bi-instagram me-1"></i> Instagram
            </a>
        </div>
    </div>
</div>


<style>
    .active {
        font-weight: bold;
    }
</style>

<script>
    function setAlert(type, msg) {
        let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';

        // Xóa tất cả các thông báo cũ
        let oldAlerts = document.querySelectorAll('.setting-alert');
        oldAlerts.forEach(alert => alert.remove());

        // Tạo thông báo mới
        let element = document.createElement('div');
        element.innerHTML = `
            <div class="alert ${bs_class} alert-dismissible fade show setting-alert"  role="alert">
                <strong class="me-3">${msg}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        document.body.append(element);
    }

    function setActive() {
        let navbar = document.getElementById('nav-bar');
        let a_tags = navbar.getElementsByTagName('a');

        for (i = 0; i < a_tags.length; i++) {
            let file = a_tags[i].href.split('/').pop();
            let file_name = file.split('.')[0];

            if (document.location.href.indexOf(file_name) >= 0) {
                a_tags[i].classList.add('active');
            }
        }
    }

    let register_form = document.getElementById('register-form');

    register_form.addEventListener('submit', (e) => {
        e.preventDefault();

        let data = new FormData();
        data.append('name', register_form.elements['name'].value);
        data.append('email', register_form.elements['email'].value);
        data.append('phonenum', register_form.elements['phonenum'].value);
        data.append('address', register_form.elements['address'].value);
        data.append('pincode', register_form.elements['pincode'].value);
        data.append('dob', register_form.elements['dob'].value);
        data.append('pass', register_form.elements['pass'].value);
        data.append('cpass', register_form.elements['cpass'].value);
        data.append('profile', register_form.elements['profile'].files[0]);
        data.append('register', '');

        var myModal = document.getElementById('registerModel');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide()

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/login_register.php", true);

        xhr.onload = function() {

            console.log(this.responseText);
            if (this.responseText.trim() == 'pass_mismatch') { //chú ý lỗi ở this.responseText.trim()
                setAlert('error', "Password mismatch ! ");
            } else if (this.responseText.trim() == 'email_already') {
                setAlert('error', "Email is already registered ! ");
            } else if (this.responseText.trim() == 'phone_already') {
                setAlert('error', "Phone number is already registered ! ");
            } else if (this.responseText.trim() == 'inv_img') {
                setAlert('error', "Only JPG, PNG & WEBP are allowed ! ");
            } else if (this.responseText.trim() == 'upd_failed') {
                setAlert('error', "Image upload failed ! ");
            } else if (this.responseText.trim() == 'invalid_email') {
                setAlert('error', "Cannot send confirmation email! Check email again! ");
            } else if (this.responseText.trim() == 'ins_failed') {
                setAlert('error', "Registration failed!");
            } else {
                setAlert('success', "Registration successful. Confirmation link sent to email ! ");
                register_form.reset();
            }
        }

        xhr.send(data);
    });


    let login_form = document.getElementById('login-form');

    login_form.addEventListener('submit', (e) => {
        e.preventDefault();

        let data = new FormData();

        data.append('email_mob', login_form.elements['email_mob'].value);
        data.append('pass', login_form.elements['pass'].value);
        data.append('login', '');

        var myModal = document.getElementById('loginModel');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide()

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/login_register.php", true);

        xhr.onload = function() {

            if (this.responseText.trim() == 'inv_email_mob') { //chú ý lỗi ở this.responseText.trim()
                setAlert('error', "Invalid Email or Mobile Number!");
            } else if (this.responseText.trim() == 'not_verified') {
                setAlert('error', "Email is not verified! ");
            } else if (this.responseText.trim() == 'inactive') {
                setAlert('error', "Account Suspended! Please contact Admin!");
            } else if (this.responseText.trim() == 'invalid_pass') {
                setAlert('error', "Incorrect Password!");
            } else {
                let fileurl = window.location.href.split('/').pop().split('?').shift();
                if (fileurl == 'room_detail.php') {
                    window.location = window.location.href;
                } else {
                    window.location = window.location.pathname;
                }

            }
        }
        xhr.send(data);
    });


    let forgot_form = document.getElementById('forgot-form');

    forgot_form.addEventListener('submit', (e) => {
        e.preventDefault();

        let data = new FormData();

        data.append('email', forgot_form.elements['email'].value);
        data.append('forgot_pass', '');

        var myModal = document.getElementById('forgotModel');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide()

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/login_register.php", true);

        xhr.onload = function() {

            if (this.responseText.trim() == 'inv_email') { //chú ý lỗi ở this.responseText.trim()
                setAlert('error', "Invalid Email");
            } else if (this.responseText.trim() == 'not_verified') {
                setAlert('error', "Email is not verified! Contact Admin");
            } else if (this.responseText.trim() == 'inactive') {
                setAlert('error', "Account Suspended! Please contact Admin!");
            } else if (this.responseText.trim() == 'mail_failed') {
                setAlert('error', "Cannot send email. Server Down!");
            } else if (this.responseText.trim() == 'up_failed') {
                setAlert('error', "Password reset failed!");
            } else {
                setAlert('success', "Reset link sent to email!");
                forgot_form.reset();
            }

        }
        xhr.send(data);

    });

    function checkLoginToBook(status, room_id) {
        if (status) {
            window.location.href = 'confirm_booking.php?id=' + room_id;
        } else {
            setAlert('error', 'Please login to book room!');

        }
    }


    setActive();
</script>