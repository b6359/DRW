<?php
include_once '../config/config.php';
include_once '../config/constant.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $userName = isset($input['userName']) ? $input['userName'] : null;
    $password = isset($input['password']) ? $input['password'] : null;

    if ($userName && $password) {
        $user = $db->SELECT(TBL_USERS, null, array("userName" => $userName, "password" => $password));
        
        if ($user && mysqli_num_rows($user) > 0) {
            $user_data = $user->fetch_assoc();
            
            $response['message'] = MSG_LOGIN_SUCCESS;
            $response['status'] = 200;
            $response['data'] = $user_data;
        } else {
            $response['message'] = MSG_LOGIN_FAILED;
            $response['status'] = 401;
            $response['data'] = null;
        }
    } else {
        $response['message'] = MSG_LOGIN_REQUIRED;
        $response['status'] = 422;
    }
} else {
    $response["message"] = MSG_METHOD_NOT_ALLOWED;
    $response["status"] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
