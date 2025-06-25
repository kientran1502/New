
let feature_s_form = document.getElementById('feature_s_form');
let facilities_s_form = document.getElementById('facilities_s_form');


feature_s_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_feature();
});


function add_feature() {

    let data = new FormData();
    data.append('name', feature_s_form.elements['feature_name'].value);
    data.append('add_feature', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/feature_facilities.php", true);

    xhr.onload = function() {

        var myModal = document.getElementById('feature-s');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 1) {
            setAlert('success', 'New feature added!');
            feature_s_form.elements['feature_name'].value = '';
            get_feature();
        } else {
            setAlert('error', 'Server Down!');
        }
    }
    xhr.send(data);
}

function get_feature() {

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/feature_facilities.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('feature-data').innerHTML = this.responseText;

    }
    xhr.send('get_feature');
}

function rem_feature(val) {

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/feature_facilities.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        console.log(this.responseText)
        if (this.responseText == 1) {
            setAlert('success', 'Feature removed');
            get_feature();
        } else if (this.responseText == 666) {
            setAlert('error', 'Feature is added in room!') //bug

        } else {
            setAlert('error', 'Server down ???');
        }
    }
    xhr.send('rem_feature=' + val);
}



facilities_s_form.addEventListener('submit', function(e) {
    e.preventDefault();
    add_facilities();
});

function add_facilities() {

    let data = new FormData();
    data.append('name', facilities_s_form.elements['facilities_name'].value);
    data.append('icon', facilities_s_form.elements['facilities_icon'].files[0]);
    data.append('desc', facilities_s_form.elements['facilities_desc'].value);


    data.append('add_facilities', '');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/feature_facilities.php", true);

    xhr.onload = function() {

        var myModal = document.getElementById('facilities-s');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if (this.responseText == 'inv_img') {
            setAlert('error', 'Only SVG imgs are allowed !');
        } else if (this.responseText == 'inv_size') {
            setAlert('error', 'Image should be less than 50MB !');

        } else if (this.responseText == 'upd_failed') {
            setAlert('error', 'Image upload failed!');
        } else {
            setAlert('success', 'New Facility added!');
            facilities_s_form.reset();

            get_facilities();
        }
    }
    xhr.send(data);
}

function get_facilities() {

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/feature_facilities.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('facilities-data').innerHTML = this.responseText;

    }
    xhr.send('get_facilities');
}

function rem_facilities(val) {

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/feature_facilities.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText == 1) {
            setAlert('success', 'Facility removed');
            get_facilities();
        } else if (this.responseText == 666) {
            setAlert('error', 'Facility is added in room!')//bug

        } else {
            setAlert('error', 'Server down');
        }
    }
    xhr.send('rem_facilities=' + val);
}

window.onload = function() {
    get_feature();
    get_facilities();
}
