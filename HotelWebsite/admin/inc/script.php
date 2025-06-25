<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

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

    function setAlert2(type, msg, position = 'body') {
        let bs_class = (type == 'success') ? 'alert-success' : 'alert-danger';

        // Xóa tất cả các thông báo cũ
        let oldAlerts = document.querySelectorAll('.setting-alert');
        oldAlerts.forEach(alert => alert.remove());

        // Tạo thông báo mới
        let element = document.createElement('div');
        element.innerHTML = `
            <div class="alert ${bs_class} alert-dismissible fade show" role="alert">
                <strong class="me-3">${msg}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        if (position == 'body') {
            document.body.append(element);
            element.classList.add('custom-alert');
        } else {
            document.getElementById(position).appendChild(element);
        }

        setTimeout(remAlert, 1000);
    }

    function remAlert() {
        document.getElementsByClassName('alert')[0].remove();
    }


    function setActive() {
        let navbar = document.getElementById('dashboard-menu');
        let a_tags = navbar.getElementsByTagName('a');

        for (i = 0; i < a_tags.length; i++) {
            let file = a_tags[i].href.split('/').pop();
            let file_name = file.split('.')[0];

            if (document.location.href.indexOf(file_name) >= 0) {
                a_tags[i].classList.add('active');
            }
        }
    }
    setActive();

    if (window.location.search) {
        const url = new URL(window.location.href);
        url.search = ''; // Xóa toàn bộ tham số
        window.history.replaceState({}, document.title, url);
    }
</script>