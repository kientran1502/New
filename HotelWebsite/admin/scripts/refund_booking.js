function get_booking(search='') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/refund_booking.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('table-data').innerHTML = this.responseText;
    }
    xhr.send('get_booking&search='+search);

}


function refund_booking(id){
    if (confirm("Refund money for this booking?")) {
        let data = new FormData();
        data.append('booking_id', id);
        data.append('refund_booking', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/refund_booking.php", true);

        xhr.onload = function() {

            if (this.responseText == 1) {
                setAlert('success', 'Money Refunded!');
                get_booking();
            } else {
                setAlert('error', 'Server down!');

            }
        }
        xhr.send(data);
    }

}


window.onload = function() {
    get_booking();
}