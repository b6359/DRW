<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$response = array();

try {
    // 1. Get all fitter supplies
    $supplyResult = $db->SELECT(TBL_FITTER_SUPPLY);
    $data = [];

    if ($supplyResult && $supplyResult->num_rows > 0) {
        while ($supply = $supplyResult->fetch_assoc()) {
            $supplyId = $supply['id'];
            $fitterId = $supply['fitterId'];

            // 2. Get fitter details
            $fitterResult = $db->SELECT(TBL_FITTER_MASTER, null, ['id' => $fitterId]);
            $fitterDetail = ($fitterResult && $fitterResult->num_rows > 0) ? $fitterResult->fetch_assoc() : null;

            // 3. Get products linked with this supply
            $productList = [];
            $supplyProductResult = $db->SELECT(TBL_FITTER_SUPPLY_PRODUCT, null, ['fitterSupplyId' => $supplyId]);
            if ($supplyProductResult && $supplyProductResult->num_rows > 0) {
                while ($supplyProduct = $supplyProductResult->fetch_assoc()) {
                    $productMasterId = $supplyProduct['productMasterId'];

                    $productResult = $db->SELECT(TBL_PRODUCT_MASTER, null, ['id' => $productMasterId]);
                    $productDetail = ($productResult && $productResult->num_rows > 0) ? $productResult->fetch_assoc() : null;

                    // Also get linked items inside product
                    $productItems = [];
                    $productItemResult = $db->SELECT(TBL_PRODUCT_ITEM, null, ['productMasterId' => $productMasterId]);
                    if ($productItemResult && $productItemResult->num_rows > 0) {
                        while ($productItem = $productItemResult->fetch_assoc()) {
                            $itemId = $productItem['itemMasterId'];
                            $itemResult = $db->SELECT(TBL_ITEM_MASTER, null, ['id' => $itemId]);
                            $itemDetail = ($itemResult && $itemResult->num_rows > 0) ? $itemResult->fetch_assoc() : null;

                            if ($itemDetail) {
                                $productItems[] = $itemDetail;
                            }
                        }
                    }

                    $productList[] = [
                        'product' => $productDetail,
                        'items' => $productItems
                    ];
                }
            }

            // 4. Get extra items
            $extraItems = [];
            $extraItemResult = $db->SELECT(TBL_FITTER_SUPPLY_EXTRA_ITEM, null, ['fitterSupplyId' => $supplyId]);
            if ($extraItemResult && $extraItemResult->num_rows > 0) {
                while ($extraItem = $extraItemResult->fetch_assoc()) {
                    $itemId = $extraItem['extraItemId'];
                    $itemResult = $db->SELECT(TBL_ITEM_MASTER, null, ['id' => $itemId]);
                    $itemDetail = ($itemResult && $itemResult->num_rows > 0) ? $itemResult->fetch_assoc() : null;

                    if ($itemDetail) {
                        $extraItems[] = $itemDetail;
                    }
                }
            }

            // 5. Get rejection items
            $rejectionItems = [];
            $rejectionItemResult = $db->SELECT(TBL_FITTER_SUPPLY_REJECTION_ITEM, null, ['fitterSupplyId' => $supplyId]);
            if ($rejectionItemResult && $rejectionItemResult->num_rows > 0) {
                while ($rejectionItem = $rejectionItemResult->fetch_assoc()) {
                    $itemId = $rejectionItem['rejectionItemId'];
                    $itemResult = $db->SELECT(TBL_ITEM_MASTER, null, ['id' => $itemId]);
                    $itemDetail = ($itemResult && $itemResult->num_rows > 0) ? $itemResult->fetch_assoc() : null;

                    if ($itemDetail) {
                        $rejectionItems[] = $itemDetail;
                    }
                }
            }

            // 6. Prepare final data
            $supply['fitter'] = $fitterDetail;
            $supply['products'] = $productList;
            $supply['extraItems'] = $extraItems;
            $supply['rejectionItems'] = $rejectionItems;
            $data[] = $supply;
            
        }
    }

    $response['status'] = 200;
    $response['message'] = MSG_GET_ALL_FITTER_SUPPLY_FOUND;
    $response['data'] = $data;

} catch (Exception $e) {
    $response['status'] = 500;
    $response['message'] = "Something went wrong: " . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
?>
