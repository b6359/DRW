<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $itemId = isset($input['id']) ? (int)$input['id'] : null;

    if ($itemId) {
        $where = array("id" => $itemId);
        $whereEx = array("extraItemId" => $itemId);
        $whereRe = array("rejectionItemId" => $itemId);
        $whereCr = array("itemId" => $itemId);
        $deleteEx = $db->DELETE(TBL_FITTER_SUPPLY_EXTRA_ITEM, $whereEx);
        $deleteRej = $db->DELETE(TBL_FITTER_SUPPLY_REJECTION_ITEM, $whereRe);
        $deleteCr = $db->DELETE(TBL_ITEM_CREDIT_DEBIT_ITEM, $whereCr);
        $delete_item = $db->DELETE(TBL_ITEM_MASTER, $where);

        if ($delete_item) {
            $response['message'] = MSG_ITEM_DELETE_SUCCESS;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_ITEM_DELETE_FAIL;
            $response['status'] = 500;
            $response['error'] = $delete_item;
        }
    } else {
        $response['message'] = MSG_ITEM_ID_REQUIRED;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
