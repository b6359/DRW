<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $userId    = isset($input['id']) ? (int)$input['id'] : null;
    $name      = isset($input['name']) ? trim($input['name']) : null;
    $mobile    = isset($input['mobile']) ? trim($input['mobile']) : null;
    $userName  = isset($input['userName']) ? trim($input['userName']) : null;
    $password  = isset($input['password']) ? trim($input['password']) : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($userId && ($name || $mobile || $userName || $password || $createdBy)) {
        $set = array();
        if ($name)      $set['name'] = $name;
        if ($mobile)    $set['mobile'] = $mobile;
        if ($userName)  $set['userName'] = $userName;
        if ($password)  $set['password'] = $password;
        if ($createdBy) $set['createdBy'] = $createdBy;

        $where = array("id" => $userId);

        $update_user = $db->UPDATE(TBL_USERS, $set, $where);

        if ($update_user === true) {
            $response['message'] = MSG_USER_UPDATE_SUCCESS;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_USER_UPDATE_FAIL;
            $response['status'] = 500;
            $response['error'] = $update_user;
        }
    } else {
        $response['message'] = MSG_USER_UPDATE_INVALID_FIELDS;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
