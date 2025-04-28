<?php
include_once '../config/config.php';
include_once '../config/constant.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name      = isset($input['name']) ? trim($input['name']) : null;
    $mobile    = isset($input['mobile']) ? trim($input['mobile']) : null;
    $userName  = isset($input['userName']) ? trim($input['userName']) : null;
    $password  = isset($input['password']) ? trim($input['password']) : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($name && $mobile && $userName && $password && $createdBy) {
        $check_user = $db->SELECT(TBL_USERS, null, array("userName" => $userName));
        
        if ($check_user && mysqli_num_rows($check_user) > 0) {
            $response['message'] = MSG_USER_ALREADY_EXIST;
            $response['status'] = 409;
        } else {
            $insert_data = array(
                "name"       => $name,
                "mobile"     => $mobile,
                "userName"   => $userName,
                "password"   => $password,
                "createdBy"  => $createdBy
            );
            $insert_user = $db->INSERT(TBL_USERS, $insert_data);

            if ($insert_user) {
                $response['message'] = MSG_USER_ADDED;
                $response['status'] = 200;
            } else {
                $response['message'] = MSG_USER_ADD_FAIL;
                $response['status'] = 500;
            }
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
} else {
    $response["message"] = MSG_METHOD_NOT_ALLOWED;
    $response["status"] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
