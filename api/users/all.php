<?php

include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'GET') {
    $user = $db->SELECT(TBL_USERS, null, null);
    if ($user == true) {
        $total_rows = mysqli_num_rows($user);
        $user_data = [];
        if (intval($total_rows) > 0) {
            while ($row = $user->fetch_assoc()) {
                $user_data[] = array(
                    "id" => intval($row['id']),
                    "name" => $row['name'],
                    "mobile" => $row['mobile'],
                    "userName" => $row['userName'],
                    "password" => $row['password'],
                    "createdBy" => $row['createdBy'],
                    "createdAt" => $row['createdAt']
                );
            }
            $response["message"] = MSG_GET_ALL_USER_FOUND;
            $response["status"] = 200;
            $response["data"] = $user_data;

        } else {
            $response["message"] = MSG_USER_NOT_FOUND;
            $response["status"] = 404;
            $response["data"] = null;
        }
    } else {
        $response["error"] = $user;
    }
} else {
    $response["message"] = MSG_METHOD_NOT_ALLOWED;
    $response["status"] = 400;
}

echo json_encode($response);
