<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fitterSupplyId = isset($input['fitterSupplyId']) ? $input['fitterSupplyId'] : null;
    $fitterId = isset($input['fitterId']) ? $input['fitterId'] : null;
    $productMasterIds = isset($input['productMasterIds']) ? $input['productMasterIds'] : null;
    $extraItemIds = isset($input['extraItemIds']) ? $input['extraItemIds'] : null;
    $rejectionItemIds = isset($input['rejectionItemIds']) ? $input['rejectionItemIds'] : null;
    $updatedBy = isset($input['updatedBy']) ? $input['updatedBy'] : null;

    if ($fitterSupplyId && $fitterId && $productMasterIds) {
        // Update Fitter Supply
        $fitterSupplyData = array(
            'fitterId' => $fitterId
        );
        $updateFitterSupply = $db->UPDATE(TBL_FITTER_SUPPLY, $fitterSupplyData, "id = $fitterSupplyId");

        if ($updateFitterSupply) {
            // Update Product Master IDs in tbl_fitter_supply_product
            $db->DELETE(TBL_FITTER_SUPPLY_PRODUCT, "fitterSupplyId = $fitterSupplyId");
            foreach ($productMasterIds as $product) {
                $productMasterId = $product['productMasterId'];
                $productQty = (int)$product['qty'];
                $productRate = (int)$product['rate'];

                $rateResult = $db->connection->query("SELECT * FROM tbl_fitter_product_rate WHERE fitterId=$fitterId AND productMasterId=$productMasterId");

                if ($rateResult->num_rows > 0) {
                    $rate_data = array(
                        'rate' => $productRate
                    );
                    $rate_where = array(
                        'fitterId' => $fitterId,
                        'productMasterId' => $productMasterId
                    );
                    $update = $db->UPDATE(TBL_FITTER_PRODUCT_RATE, $rate_data, $rate_where);
                } else {
                    $R_data = array(
                        'fitterId' => $fitterId,
                        'productMasterId' => $productMasterId,
                        'rate' => $productRate
                    );
                    $insert = $db->INSERT(TBL_FITTER_PRODUCT_RATE, $R_data);
                }
                $fitterSupplyProductData = array(
                    'fitterSupplyId' => $fitterSupplyId,
                    'productMasterId' => $productMasterId,                    
                    'qty' => $productQty
                );
                $db->INSERT(TBL_FITTER_SUPPLY_PRODUCT, $fitterSupplyProductData);
            }

            // Update Extra Item IDs in tbl_fitter_supply_extra_item
            if ($extraItemIds) {
                $db->DELETE(TBL_FITTER_SUPPLY_EXTRA_ITEM, "fitterSupplyId = $fitterSupplyId");
                foreach ($extraItemIds as $extraItem) {
                    $extraItemId = $extraItem['extraItemId'];
                    $extraQty = (int)$extraItem['qty'];
                    $fitterSupplyExtraItemData = array(
                        'fitterSupplyId' => $fitterSupplyId,
                        'extraItemId' => $extraItemId,
                        'qty' => $extraQty
                    );
                    $db->INSERT(TBL_FITTER_SUPPLY_EXTRA_ITEM, $fitterSupplyExtraItemData);
                }
            }

            // Update Rejection Item IDs in tbl_fitter_supply_rejection_item
            if ($rejectionItemIds) {
                $db->DELETE(TBL_FITTER_SUPPLY_REJECTION_ITEM, "fitterSupplyId = $fitterSupplyId");
                foreach ($rejectionItemIds as $rejectionItem) {
                    $rejectionItemId = $rejectionItem['rejectionItemId'];
                    $rejectionQty = (int)$rejectionItem['qty'];
                    $fitterSupplyRejectionItemData = array(
                        'fitterSupplyId' => $fitterSupplyId,
                        'rejectionItemId' => $rejectionItemId,
                        'qty' => $rejectionQty
                    );
                    $db->INSERT(TBL_FITTER_SUPPLY_REJECTION_ITEM, $fitterSupplyRejectionItemData);
                }
            }

            $response['message'] = MSG_FITTER_SUPPLY_UPDATED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FITTER_SUPPLY_UPDATE_FAIL;
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
