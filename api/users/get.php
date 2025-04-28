<?php

include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $email = $_POST['email'];
    $user = $db->SELECT($TBL_PROJECTS, null, null);
    if ($user == true) {
        $total_rows = mysqli_num_rows($user);
        $user_data = [];
        if (intval($total_rows) > 0) {
            while ($row = $user->fetch_assoc()) {
                $user_data = array(
                    "id" => intval($row['id']),
                    "project_name" => $row['project_name'],
                    "project_type" => $row['project_type'],
                    "project_logo" => intval($row['project_logo']),
                    "master_file" => $row['master_file'],
                    "created_at" => $row['created_at'],
                    "updated_at" => $row['updated_at'],
                );
            }
            $response["message"] = $MSG_GET_PROJECT_FOUND;
            $response["status"] = 200;
            $response["data"] = $user_data;

        } else {
            $response["message"] = $MSG_GET_PROJECT_NOT_FOUND;
            $response["status"] = 404;
            $response["data"] = null;
        }
    } else {
        $response["error"] = $user;
    }
} else {
    $response["message"] = $MSG_INVALID_REQUEST;
    $response["status"] = 400;
}

echo json_encode($response);
