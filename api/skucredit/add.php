<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fitterId = isset($input['fitterId']) ? (int)$input['fitterId'] : null;
    $productMasterIds = isset($input['productMasterIds']) ? $input['productMasterIds'] : [];
    $extraItemIds = isset($input['extraItemIds']) ? $input['extraItemIds'] : [];
    $itemIds = isset($input['itemIds']) ? $input['itemIds'] : [];
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($fitterId && $createdBy) {
        $skuCreditData = [
            'fitterId' => $fitterId,
            'createdBy' => $createdBy,
        ];

        $skuCreditId = $db->INSERT(TBL_SKU_CREDIT, $skuCreditData);

        if ($skuCreditId) {
            foreach ($productMasterIds as $product) {
                $productMasterId = $product['productMasterId'];
                $productQty = (int)$product['qty'];
                $db->INSERT(TBL_SKU_CREDIT_PRODUCT, [
                    'skuCreditId' => $skuCreditId,
                    'productMasterId' => $productMasterId,
                    'qty' => $productQty,
                ]);
            }

            foreach ($extraItemIds as $extraItem) {
                $extraItemId = $extraItem['extraItemId'];
                $extraQty = (int)$extraItem['qty'];
                $db->INSERT(TBL_SKU_CREDIT_EXTRA_ITEM, [
                    'skuCreditId' => $skuCreditId,
                    'extraItemId' => $extraItemId,
                    'qty' => $extraQty
                ]);
            }

            foreach ($itemIds as $items) {
                $itemId = $items['itemId'];
                $itemQty = (int)$items['qty'];
                $db->INSERT(TBL_SKU_CREDIT_ITEM, [
                    'skuCreditId' => $skuCreditId,
                    'itemId' => $itemId,
                    'qty' => $itemQty
                ]);
            }

            $response['status'] = 200;
            $response['message'] = MSG_SKU_CREDIT_ADDED;
        } else {
            $response['status'] = 500;
            $response['message'] = MSG_SKU_CREDIT_ADD_FAIL;
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
