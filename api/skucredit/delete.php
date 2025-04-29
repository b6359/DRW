<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : null;

    if ($id) {
        // Delete from child tables first
        $db->DELETE(TBL_SKU_CREDIT_PRODUCT, ['skuCreditId' => $id]);
        $db->DELETE(TBL_SKU_CREDIT_EXTRA_ITEM, ['skuCreditId' => $id]);
        $db->DELETE(TBL_SKU_CREDIT_ITEM, ['skuCreditId' => $id]);

        // Then delete the main record
        $result = $db->DELETE(TBL_SKU_CREDIT, ['id' => $id]);

        if ($result) {
            $response['status'] = 200;
            $response['message'] = MSG_SKU_CREDIT_DELETED;
        } else {
            $response['status'] = 500;
            $response['message'] = MSG_SKU_CREDIT_DELETE_FAIL;
        }
    } else {
        $response['status'] = 422;
        $response['message'] = MSG_REQUIRED_ALL;
    }
} else {
    $response['status'] = 400;
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
