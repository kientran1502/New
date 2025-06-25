<?php

// Cấu hình môi trường (TEST hoặc PROD)
define('PAYPAL_ENVIRONMENT', 'TEST');  // Sử dụng 'PROD' cho môi trường sản phẩm thực tế

// Cấu hình thông tin PayPal
define('PAYPAL_CLIENT_ID', 'AWgPzkEO8ShdDdGoZxd2cv3sIDLLrfOLkSJv1I4LECYorl10bvokXWozHeKD5QwRlzWkua99U14z1-Pm');
define('PAYPAL_CLIENT_SECRET', 'ELDMwMK6z9-VNKHCyyzrKdDZ-bCSvvISvnffhwR95jULB9sTNuM8w-tfw70WtXYlYvXK964tYUE9Pfkb');
define('CALLBACK_URL', 'http://localhost/MyProject/pay_response.php');
define('PAYPAL_API_URL', 'https://api.sandbox.paypal.com');  // URL của PayPal Sandbox (hoặc PROD khi triển khai thực tế)


// URL để lấy OAuth token
define('PAYPAL_OAUTH_URL', 'https://api.sandbox.paypal.com/v1/oauth2/token');

// Cấu hình các URL API của PayPal (môi trường TEST)
define('PAYPAL_PAYMENT_URL', 'https://api.sandbox.paypal.com/v1/payments/payment');  // URL cho yêu cầu thanh toán
define('PAYPAL_PAYMENT_EXECUTE_URL', 'https://api.sandbox.paypal.com/v1/payments/payment/execute');  // URL để thực hiện thanh toán

// Đường dẫn tới các URL callback sau khi thanh toán thành công hoặc bị hủy
define('PAYPAL_RETURN_URL', 'http://localhost/MyProject/pay_status.php');  // URL callback khi thanh toán thành công
define('PAYPAL_CANCEL_URL', 'http://localhost/MyProject/confirm_booking.php');  // URL callback khi thanh toán bị hủy

// Định nghĩa một số thông số khác nếu cần thiết cho giao dịch
define('PAYPAL_CURRENCY', 'USD');  // Đơn vị tiền tệ cho giao dịch
define('PAYPAL_PAYMENT_INTENT', 'sale');  // Mục đích thanh toán (có thể là 'sale', 'authorize', v.v.)
