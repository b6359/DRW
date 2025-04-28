<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    $productId         = isset($input['id']) ? (int)$input['id'] : null;
    $productName       = isset($input['productName']) ? trim($input['productName']) : null;
    $modelNo           = isset($input['modelNo']) ? trim($input['modelNo']) : null;
    $itemMasterIds     = isset($input['itemMasterIds']) ? $input['itemMasterIds'] : null;
    $qty               = isset($input['qty']) ? (int)$input['qty'] : null;
    $rate              = isset($input['rate']) ? (float)$input['rate'] : null;
    $pendingForBelt   = isset($input['pendingForBelt']) ? (int)$input['pendingForBelt'] : 0;
    $createdBy         = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($productId && ($productName || $modelNo || $itemMasterIds || $qty || $rate || $pendingForBelt || $createdBy)) {
        $update_data = array();
        if ($productName) $update_data['productName'] = $productName;
        if ($modelNo) $update_data['modelNo'] = $modelNo;
        if ($qty) $update_data['qty'] = $qty;
        if ($rate) $update_data['rate'] = $rate;
        if (isset($pendingForBelt)) $update_data['pendingForBelt'] = $pendingForBelt;
        if ($createdBy) $update_data['createdBy'] = $createdBy;

        $where = array("id" => $productId);
        $update_product = $db->UPDATE(TBL_PRODUCT_MASTER, $update_data, $where);

        if ($update_product === true) {
            if ($itemMasterIds) {
                $db->DELETE(TBL_PRODUCT_ITEM, array("productMasterId" => $productId));

                foreach ($itemMasterIds as $itemMasterId) {
                    $insert_item_relation = array(
                        "productMasterId" => $productId,
                        "itemMasterId"    => $itemMasterId
                    );
                    $db->INSERT(TBL_PRODUCT_ITEM, $insert_item_relation);
                }
            }

            $response['message'] = MSG_PRODUCT_UPDATED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_PRODUCT_UPDATE_FAIL;
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
