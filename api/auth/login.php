<?php
include_once '../config/config.php';
include_once '../config/constant.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $device_id = $_POST['device_id'];
    $password = $_POST['password'];
    $user = $db->SELECT($TBL_USER, null, array("device_id" => $device_id, "password" => $password));
    if ($user == true) {
        $user_data = $user->fetch_assoc();
        $total_rows = mysqli_num_rows($user);
        if ($total_rows > 0) {
            $response['message'] = $MSG_LOGIN_SUCCESS;
            $response['status'] = 200;
            $response['data'] = $user_data;
        } else {
            $response['message'] = $MSG_LOGIN_FAILED;
            $response['status'] = 401;
            $response['data'] = null;
        }
    } else {
        $response["ERROR"] = $user;
    }
} else {
    $response["message"] = $MSG_INVALID_REQUEST;
    $response["status"] = 400;
}
echo json_encode($response);
