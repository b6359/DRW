<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = isset($input['id']) ? (int)$input['id'] : null;

    if ($productId) {
        $db->DELETE(TBL_PRODUCT_ITEM, array("productMasterId" => $productId));

        $where = array("id" => $productId);
        $delete_product = $db->DELETE(TBL_PRODUCT_MASTER, $where);

        if ($delete_product === true) {
            $response['message'] = MSG_PRODUCT_DELETE_SUCCESS;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_PRODUCT_DELETE_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_PRODUCT_ID_REQUIRED;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
