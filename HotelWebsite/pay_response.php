<?php

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');
require 'vendor/autoload.php'; // Tự động tải các thư viện
// Đặt không gian tên của PayPal SDK
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

date_default_timezone_set("Asia/Ho_Chi_Minh");

session_start();
unset($_SESSION['room']);

header("Pragma: no-cache");
header("Cache-Control: no-cache");
header("Expires:0");

function regenerate_session($uid)
{
    $user_q = select("SELECT * FROM `user_cred` WHERE `id`=? LIMIT 1", [$uid], 'i');
    $user_fetch = mysqli_fetch_assoc($user_q);
    $_SESSION['login'] = true;
    $_SESSION['uID'] = $user_fetch['id'];
    $_SESSION['uName'] = $user_fetch['name'];
    $_SESSION['uPic'] = $user_fetch['profile'];
    $_SESSION['uPhone'] = $user_fetch['phonenum'];
}

file_put_contents('paypal_log.txt', "REQUEST: " . print_r($_REQUEST, true), FILE_APPEND);

if (!isset($_GET['token'])) {
    echo "Token not received!";
    file_put_contents('paypal_log.txt', "No token received!\n", FILE_APPEND);
    exit;
}

$token = $_GET['token'];

$client = new PayPalHttpClient(
    new SandboxEnvironment(
        'AWgPzkEO8ShdDdGoZxd2cv3sIDLLrfOLkSJv1I4LECYorl10bvokXWozHeKD5QwRlzWkua99U14z1-Pm',
        'ELDMwMK6z9-VNKHCyyzrKdDZ-bCSvvISvnffhwR95jULB9sTNuM8w-tfw70WtXYlYvXK964tYUE9Pfkb'
    )
);

$request = new OrdersGetRequest($token);

try {
    $response = $client->execute($request);

    if ($response->statusCode === 200) {
        // Lấy invoice_id từ phản hồi
        $invoiceNumber = $response->result->purchase_units[0]->invoice_id;

        // Ghi log phản hồi từ PayPal
        file_put_contents('paypal_log.txt', "PayPal Response: " . print_r($response, true), FILE_APPEND);

        // Truy vấn để lấy thông tin booking từ invoice_id
        $slct_query = "SELECT `booking_id`, `user_id` FROM `booking_order` WHERE `order_id`='$invoiceNumber'";
        $slct_res = mysqli_query($con, $slct_query);

        if (mysqli_num_rows($slct_res) == 0) {
            file_put_contents('paypal_log.txt', "Order ID not found in database: $invoiceNumber\n", FILE_APPEND);
            redirect('index.php');
        }

        $slct_fetch = mysqli_fetch_assoc($slct_res);

        if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
            regenerate_session($slct_fetch['user_id']);
        }

        $transactionID = $response->result->id;
        $transactionAmount = $response->result->purchase_units[0]->amount->value;
        $transactionStatus = $response->result->status;

        if ($transactionStatus === "APPROVED") {
            // Gửi yêu cầu CAPTURE giao dịch
            $captureRequest = new \PayPalCheckoutSdk\Orders\OrdersCaptureRequest($transactionID);
            $captureRequest->prefer('return=representation');

            try {
                $captureResponse = $client->execute($captureRequest);

                // Kiểm tra phản hồi từ CAPTURE
                if ($captureResponse->statusCode === 201 && $captureResponse->result->status === "COMPLETED") {
                    // CAPTURE thành công, cập nhật trạng thái booking
                    $captureTransactionID = $captureResponse->result->id;
                    $captureTransactionAmount = $captureResponse->result->purchase_units[0]->payments->captures[0]->amount->value;

                    $upd_query = "UPDATE `booking_order` SET 
                        `booking_status`='booked',
                        `trans_id`='$captureTransactionID',
                        `trans_amt`='$captureTransactionAmount',
                        `trans_status`='COMPLETED',
                        `trans_res_msg`='Transaction completed'
                        WHERE `booking_id` = '{$slct_fetch['booking_id']}'";

                    mysqli_query($con, $upd_query);

                    redirect('pay_status.php?order=' . $invoiceNumber);
                } else {
                    // CAPTURE thất bại
                    $captureTransactionID = $captureResponse->result->id;
                    $captureStatus = $captureResponse->result->status;

                    $upd_query = "UPDATE `booking_order` SET 
                        `booking_status`='payment failed',
                        `trans_status`='FAILED',
                        `trans_res_msg`='Transaction failed'
                        WHERE `booking_id` = '{$slct_fetch['booking_id']}'";

                    mysqli_query($con, $upd_query);

                    redirect('pay_status.php?order=' . $invoiceNumber);
                }
            } catch (Exception $ex) {
                // Xử lý lỗi khi gửi yêu cầu CAPTURE
                error_log("Capture Error: " . $ex->getMessage());
                file_put_contents('paypal_log.txt', "Capture Exception: " . $ex->getMessage(), FILE_APPEND);

                $upd_query = "UPDATE `booking_order` SET 
                    `booking_status`='payment failed',
                    `trans_status`='FAILED',
                    `trans_res_msg`='Transaction failed'
                    WHERE `booking_id` = '{$slct_fetch['booking_id']}'";

                mysqli_query($con, $upd_query);
                redirect('pay_status.php?order=' . $invoiceNumber);
            }
        } else {
            // Trường hợp trạng thái không phải APPROVED
            $upd_query = "UPDATE `booking_order` SET 
                `booking_status`='payment failed',
                `trans_status`='FAILED',
                `trans_res_msg`='Transaction failed'
                WHERE `order_id` = '$invoiceNumber'";

            mysqli_query($con, $upd_query);
            redirect('pay_status.php?order=' . $invoiceNumber);
        }

        redirect('pay_status.php?order=' . $invoiceNumber);
    } else {
        echo "Failed to retrieve order details.";
        file_put_contents('paypal_log.txt', "Error retrieving order details for token: $token\n", FILE_APPEND);
        redirect('index.php');
    }
} catch (Exception $ex) {
    error_log($ex->getMessage());
    file_put_contents('paypal_log.txt', "Exception: " . $ex->getMessage(), FILE_APPEND);
    redirect('index.php');
}
