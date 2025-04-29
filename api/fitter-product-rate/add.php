<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fitterId = isset($input['fitterId']) ? $input['fitterId'] : null;
    $productMasterId = isset($input['productMasterId']) ? $input['productMasterId'] : null;
    $rate = isset($input['rate']) ? $input['rate'] : null;

    if ($fitterId && $productMasterId && $rate !== null) {
        $data = array(
            'fitterId' => $fitterId,
            'productMasterId' => $productMasterId,
            'rate' => $rate
        );

        $insert = $db->INSERT(TBL_FITTER_PRODUCT_RATE, $data);

        if ($insert) {
            $response['message'] = MSG_FITTER_PRODUCT_RATE_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FITTER_PRODUCT_RATE_ADD_FAIL;
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
