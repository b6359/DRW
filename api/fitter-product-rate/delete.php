<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = isset($input['id']) ? $input['id'] : null;

    if ($id) {
        $where = array(
            'id' => $id
        );

        $delete = $db->DELETE(TBL_FITTER_PRODUCT_RATE, $where);

        if ($delete) {
            $response['message'] = MSG_FITTER_PRODUCT_RATE_DELETED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FITTER_PRODUCT_RATE_DELETE_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_FIELDS_MISSING;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
