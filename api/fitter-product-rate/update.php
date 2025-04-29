<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = isset($input['id']) ? $input['id'] : null;
    $rate = isset($input['rate']) ? $input['rate'] : null;

    if ($id && $rate !== null) {
        $data = array(
            'rate' => $rate
        );

        $where = array(
            'id' => $id
        );

        $update = $db->UPDATE(TBL_FITTER_PRODUCT_RATE, $data, $where);

        if ($update) {
            $response['message'] = MSG_FITTER_PRODUCT_RATE_UPDATED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FITTER_PRODUCT_RATE_UPDATE_FAIL;
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
