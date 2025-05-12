<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $productName = isset($input['productName']) ? $input['productName'] : null;
    $modelNo = isset($input['modelNo']) ? $input['modelNo'] : null;
    $itemMasterIds = isset($input['itemMasterIds']) ? $input['itemMasterIds'] : null;  // Array of itemMasterIds
    // $qty = isset($input['qty']) ? $input['qty'] : null;
    $rate = isset($input['rate']) ? $input['rate'] : null;
    $pendingForBelt = isset($input['pendingForBelt']) ? $input['pendingForBelt'] : null;
    $createdBy = isset($input['createdBy']) ? $input['createdBy'] : null;

    if ($productName && $modelNo && $itemMasterIds && $rate !== null && $createdBy) {
        $productData = array(
            "productName" => $productName,
            "modelNo" => $modelNo,
            // "qty" => $qty,
            "rate" => $rate,
            "pendingForBelt" => $pendingForBelt,
            "createdBy" => $createdBy
        );

        $productInsert = $db->INSERT(TBL_PRODUCT_MASTER, $productData);

        if ($productInsert) {
            $productMasterId = $db->connection->insert_id;

            foreach ($itemMasterIds as $item) {
                $itemMasterId = $item['itemMasterId'];
                $itemQty = (int)$item['qty'];
                $productItemData = array(
                    "productMasterId" => $productMasterId,
                    "itemMasterId" => $itemMasterId,
                    "qty" => $itemQty,
                );
                $productItemInsert = $db->INSERT(TBL_PRODUCT_ITEM, $productItemData);
            }

            $response['message'] = MSG_PRODUCT_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_PRODUCT_ADD_FAIL;
            $response['status'] = 500;
            $response['error'] = $db->connection->error;
        }
    } else {
        $response['message'] = MSG_METHOD_NOT_ALLOWED;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
