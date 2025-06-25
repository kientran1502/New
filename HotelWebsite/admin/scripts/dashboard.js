


function booking_analytics(period=1) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/dashboard.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);


        // Sử dụng OR (||) để kiểm tra null và thay bằng 0
        document.getElementById('total_booking').textContent = data.total_booking || 0;
        document.getElementById('total_amt').textContent = (data.total_amt || 0) + '$';

        document.getElementById('active_booking').textContent = data.active_booking || 0;
        document.getElementById('active_amt').textContent = (data.active_amt || 0) + '$';

        document.getElementById('cancelled_booking').textContent = data.cancelled_booking || 0;
        document.getElementById('cancelled_amt').textContent = (data.cancelled_amt || 0) + '$';
        
    }
    xhr.send('booking_analytics&period='+period);

}


function user_analytics(period=1) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/dashboard.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function() {
        let data = JSON.parse(this.responseText);
        console.log("Server Response:", this.responseText); 

        document.getElementById('total_new_reg').textContent = data.total_new_reg;
        document.getElementById('total_queries').textContent = data.total_queries;
        document.getElementById('total_review').textContent = data.total_review;

    }
    xhr.send('user_analytics&period='+period);

}



window.onload = function() {
    booking_analytics();
    user_analytics();
}