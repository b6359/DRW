<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fitterId = isset($input['fitterId']) ? $input['fitterId'] : null;
    $productMasterIds = isset($input['productMasterIds']) ? $input['productMasterIds'] : null;  // Array of productMasterIds
    $extraItemIds = isset($input['extraItemIds']) ? $input['extraItemIds'] : null;  // Array of extraItemIds
    $rejectionItemIds = isset($input['rejectionItemIds']) ? $input['rejectionItemIds'] : null;  // Array of rejectionItemIds
    $createdBy = isset($input['createdBy']) ? $input['createdBy'] : null;
    
    if ($fitterId && $productMasterIds && $createdBy) {
        $fitterSupplyData = array(
            'fitterId' => $fitterId,
            'createdBy' => $createdBy
        );

        $fitterSupplyInsert = $db->INSERT(TBL_FITTER_SUPPLY, $fitterSupplyData);

        if ($fitterSupplyInsert) {
            $fitterSupplyId = $db->connection->insert_id;

            foreach ($productMasterIds as $product) {
                $productMasterId = $product['productMasterId'];
                $productQty = (int)$product['qty'];
            
                $getItemMasterIdsQuery = "
                    SELECT pi.itemMasterId 
                    FROM tbl_product_item pi
                    JOIN tbl_item_master im ON pi.itemMasterId = im.id
                    WHERE pi.productMasterId = $productMasterId;
                ";
                
                $result = $db->connection->query($getItemMasterIdsQuery);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $itemMasterId = $row['itemMasterId'];
                        $updateItemQuery = "UPDATE tbl_item_master SET qty = qty - $productQty WHERE id = $itemMasterId";
                        $db->ROW_QUERY($updateItemQuery);
                    }
                }
            
                $fitterSupplyProductData = array(
                    'fitterSupplyId' => $fitterSupplyId,
                    'productMasterId' => $productMasterId,                    
                    'qty' => $productQty
                );
                $db->INSERT(TBL_FITTER_SUPPLY_PRODUCT, $fitterSupplyProductData);
            }

            if ($extraItemIds) {
                foreach ($extraItemIds as $extraItem) {
                    $extraItemId = $extraItem['extraItemId'];
                    $extraQty = (int)$extraItem['qty'];
                
                    $updateExtraItemQuery = "UPDATE tbl_item_master SET qty = qty - $extraQty WHERE id = $extraItemId";
                    $db->ROW_QUERY($updateExtraItemQuery);
                
                    $fitterSupplyExtraItemData = array(
                        'fitterSupplyId' => $fitterSupplyId,
                        'extraItemId' => $extraItemId,
                        'qty' => $extraQty
                    );
                    $db->INSERT(TBL_FITTER_SUPPLY_EXTRA_ITEM, $fitterSupplyExtraItemData);
                }
            }

            if ($rejectionItemIds) {
                foreach ($rejectionItemIds as $rejectionItem) {
                    $rejectionItemId = $rejectionItem['rejectionItemId'];
                    $rejectionQty = (int)$rejectionItem['qty'];
                
                    $updateRejectionItemQuery = "UPDATE tbl_item_master SET qty = qty - $rejectionQty WHERE id = $rejectionItemId";
                    $db->ROW_QUERY($updateRejectionItemQuery);
                
                    $fitterSupplyRejectionItemData = array(
                        'fitterSupplyId' => $fitterSupplyId,
                        'rejectionItemId' => $rejectionItemId,
                        'qty' => $rejectionQty
                    );
                    $db->INSERT(TBL_FITTER_SUPPLY_REJECTION_ITEM, $fitterSupplyRejectionItemData);
                }
            }

            $response['message'] = MSG_FITTER_SUPPLY_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FITTER_SUPPLY_ADD_FAIL;
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
