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
            foreach ($productMasterIds as $productMasterId) {
                $fitterSupplyProductData = array(
                    'fitterSupplyId' => $fitterSupplyId,
                    'productMasterId' => $productMasterId,
                );
                $db->INSERT(TBL_FITTER_SUPPLY_PRODUCT, $fitterSupplyProductData);
            }

            // Update Extra Item IDs in tbl_fitter_supply_extra_item
            if ($extraItemIds) {
                $db->DELETE(TBL_FITTER_SUPPLY_EXTRA_ITEM, "fitterSupplyId = $fitterSupplyId");
                foreach ($extraItemIds as $extraItemId) {
                    $fitterSupplyExtraItemData = array(
                        'fitterSupplyId' => $fitterSupplyId,
                        'extraItemId' => $extraItemId
                    );
                    $db->INSERT(TBL_FITTER_SUPPLY_EXTRA_ITEM, $fitterSupplyExtraItemData);
                }
            }

            // Update Rejection Item IDs in tbl_fitter_supply_rejection_item
            if ($rejectionItemIds) {
                $db->DELETE(TBL_FITTER_SUPPLY_REJECTION_ITEM, "fitterSupplyId = $fitterSupplyId");
                foreach ($rejectionItemIds as $rejectionItemId) {
                    $fitterSupplyRejectionItemData = array(
                        'fitterSupplyId' => $fitterSupplyId,
                        'rejectionItemId' => $rejectionItemId
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
