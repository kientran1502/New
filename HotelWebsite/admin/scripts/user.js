function get_user() {

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/user.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('user-data').innerHTML = this.responseText;
    }
    xhr.send('get_user')

}


function toggle_status(id, val) {

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/user.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        if (this.responseText == 1) {
            setAlert('success', 'Status toggled');
            get_user();

        } else {
            setAlert('error', 'Server Down!');
        }
    }
    xhr.send('toggle_status=' + id + '&value=' + val);

}


function remove_user(user_id) {

    if (confirm("Are you sure, you want to remove this user?")) {
        let data = new FormData();
        data.append('user_id', user_id);
        data.append('remove_user', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/user.php", true);

        xhr.onload = function() {

            if (this.responseText == 1) {
                setAlert('success', 'User Remove!');
                get_user();
            } else {
                setAlert('error', 'User Removed Failed!');

            }
        }
        xhr.send(data);
    }

}

function search_user(username){
    
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/user.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('user-data').innerHTML = this.responseText;
    }
    xhr.send('search_user&name='+username)

}

window.onload = function() {
    get_user();
}