function get_booking(search='') {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_booking.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        document.getElementById('table-data').innerHTML = this.responseText;
    }
    xhr.send('get_booking&search='+search);

}

let assign_room_form = document.getElementById('assign_room_form');
function assign_room(id){
    assign_room_form.elements['booking_id'].value = id;
}
assign_room_form.addEventListener('submit', function(e){
    e.preventDefault();
    let data = new FormData();
    data.append('room_no', assign_room_form.elements['room_no'].value);
    data.append('booking_id', assign_room_form.elements['booking_id'].value);
    data.append('assign_room','');

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/new_booking.php", true);

    xhr.onload = function() {
        var myModal = document.getElementById('assign-room');
        var modal = bootstrap.Modal.getInstance(myModal);
        modal.hide();

        if(this.responseText==1){
            setAlert('success','Room Num alloted! Booking finalized!');
            assign_room_form.reset();
            get_booking();
        
        }else{
            setAlert('error','Sever Down!');
        }



    }
    xhr.send(data);

});


function cancel_booking(id){
    if (confirm("Are you sure, you want to cancel this booking?")) {
        let data = new FormData();
        data.append('booking_id', id);
        data.append('cancel_booking', '');

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/new_booking.php", true);

        xhr.onload = function() {

            if (this.responseText == 1) {
                setAlert('success', 'Booking Cancelled!');
                get_booking();
            } else {
                setAlert('error', 'Serverdown!');

            }
        }
        xhr.send(data);
    }

}


window.onload = function() {
    get_booking();
}