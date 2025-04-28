<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = isset($input['id']) ? (int)$input['id'] : null;

    if ($userId) {
        $where = array("id" => $userId);
        
        $delete_user = $db->DELETE(TBL_USERS, $where);
        
        if ($delete_user === true) {
            $response['message'] = MSG_USER_DELETE_SUCCESS;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_USER_DELETE_FAIL;
            $response['status'] = 500;
            $response['error'] = $delete_user;
        }
    } else {
        $response['message'] = MSG_USER_ID_REQUIRED;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
