<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require('inc/paypal/config_paypal.php');
date_default_timezone_set("Asia/Ho_chi_Minh");

session_start();


if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('index.php');
}

if (isset($_POST['pay_now'])) {
    header("Pragma: no-cache");
    header("Cache-Control: no-cache");
    header("Expires:0");

    $ORDER_ID = 'ORD_' . $_SESSION['uID'] . random_int(11111, 9999999);
    $CUST_ID = $_SESSION['uID'];
    $TXN_AMOUNT = $_SESSION['room']['payment'];
    $CURRENCY = 'USD';

    $paramList = array();
    $paramList["ORDER_ID"] = $ORDER_ID;
    $paramList["CUST_ID"] = $CUST_ID;
    $paramList["TXN_AMOUNT"] = $TXN_AMOUNT;
    $paramList["CURRENCY"] = $CURRENCY;
    $paramList["CALLBACK_URL"] = CALLBACK_URL;



    //insert payment data into database
    $frm_data = filtration($_POST);
    $query = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `order_id`) 
        VALUES (?,?,?,?,?)";
    insert($query, [$CUST_ID, $_SESSION['room']['id'], $frm_data['checkin'], $frm_data['checkout'], $ORDER_ID], 'issss');
    $booking_id = $con->insert_id;
    $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) 
        VALUES (?,?,?,?,?,?,?)";
    insert($query2, [
        $booking_id,
        $_SESSION['room']['name'],
        $_SESSION['room']['price'],
        $TXN_AMOUNT,
        $frm_data['name'],
        $frm_data['phonenum'],
        $frm_data['address']
    ], 'issssss');

    // Lấy OAuth token từ PayPal
    $accessToken = getPayPalAccessToken(PAYPAL_CLIENT_ID, PAYPAL_CLIENT_SECRET);

    // Tạo yêu cầu thanh toán PayPal
    $paymentResponse = createPayPalPayment($accessToken, $ORDER_ID, $TXN_AMOUNT, $CURRENCY, CALLBACK_URL, PAYPAL_CANCEL_URL);

    // Kiểm tra phản hồi từ PayPal
    if ($paymentResponse['state'] == 'created') {
        // Tìm URL chuyển hướng từ phản hồi PayPal
        foreach ($paymentResponse['links'] as $link) {
            if ($link['rel'] == 'approval_url') {
                $approvalUrl = $link['href'];
                header("Location: " . $approvalUrl);  // Chuyển hướng người dùng tới PayPal
                exit();
            }
        }
    } else {
        // Xử lý nếu có lỗi
        echo "Error creating payment: " . $paymentResponse['message'];
    }
}


// Hàm lấy OAuth token từ PayPal
function getPayPalAccessToken($clientId, $clientSecret)
{
    $url = 'https://api.sandbox.paypal.com/v1/oauth2/token'; // Đối với môi trường sandbox

    // Xác thực bằng Client ID và Client Secret
    $headers = [
        'Authorization: Basic ' . base64_encode($clientId . ':' . $clientSecret),
        'Content-Type: application/x-www-form-urlencoded'
    ];

    // Dữ liệu yêu cầu lấy token
    $data = ['grant_type' => 'client_credentials'];

    // Gửi yêu cầu lấy token
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    curl_close($ch);

    $responseData = json_decode($response, true);
    return $responseData['access_token']; // Trả về token để sử dụng trong các API tiếp theo
}

// Hàm tạo yêu cầu thanh toán PayPal
function createPayPalPayment($accessToken, $orderId, $txnAmount, $currency, $returnUrl, $cancelUrl)
{
    $url = 'https://api.sandbox.paypal.com/v1/payments/payment'; // Đối với môi trường sandbox

    // Đặt tiêu đề của yêu cầu HTTP
    $headers = [
        'Authorization: Bearer ' . $accessToken,
        'Content-Type: application/json'
    ];

    // Dữ liệu yêu cầu thanh toán
    $data = [
        'intent' => 'sale',
        'payer' => [
            'payment_method' => 'paypal'
        ],
        'transactions' => [
            [
                'amount' => [
                    'total' => $txnAmount,
                    'currency' => $currency
                ],
                'invoice_number' => $orderId, // Chèn order_id vào invoice_number
                'description' => 'Payment for Order ID: ' . $orderId
            ]
        ],
        'redirect_urls' => [
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
        ]
    ];

    // Gửi yêu cầu thanh toán tới PayPal
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true); // Trả về kết quả từ PayPal
}
?>

<html>

<head>
    <title>Processing</title>
</head>

<body>
    <h1>Please do not refresh this page...</h1>
    <!-- Form HTML gửi yêu cầu tới PayPal -->
    <form method="post" action="https://api.sandbox.paypal.com/v1/payments/payment" name="f1">
        <?php
        // Lặp qua các tham số và tạo các trường ẩn
        foreach ($paramList as $name => $value) {
            echo '<input type="hidden" name="' . $name . '" value="' . $value . '">';
        }
        ?>
    </form>

    <script type="text/javascript">
        // Tự động submit form để gửi yêu cầu thanh toán tới PayPal
        document.f1.submit();
    </script>
</body>

</html>