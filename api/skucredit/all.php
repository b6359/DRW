<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$response = [];
$data = [];

$skuCredits = $db->SELECT(TBL_SKU_CREDIT);

if ($skuCredits && $skuCredits->num_rows > 0) {
    while ($row = $skuCredits->fetch_assoc()) {
        $id = $row['id'];

        $products = [];
        $productResult = $db->SELECT(TBL_SKU_CREDIT_PRODUCT, null, ['skuCreditId' => $id]);
        if ($productResult && $productResult->num_rows > 0) {
            while ($prod = $productResult->fetch_assoc()) {
                $products[] = $prod;
            }
        }

        $extraItems = [];
        $extraResult = $db->SELECT(TBL_SKU_CREDIT_EXTRA_ITEM, null, ['skuCreditId' => $id]);
        if ($extraResult && $extraResult->num_rows > 0) {
            while ($ei = $extraResult->fetch_assoc()) {
                $extraItems[] = $ei;
            }
        }

        $returnedItems = [];
        $itemResult = $db->SELECT(TBL_SKU_CREDIT_ITEM, null, ['skuCreditId' => $id]);
        if ($itemResult && $itemResult->num_rows > 0) {
            while ($ri = $itemResult->fetch_assoc()) {
                $returnedItems[] = $ri;
            }
        }

        $row['products'] = $products;
        $row['extraItems'] = $extraItems;
        $row['returnedItems'] = $returnedItems;

        $data[] = $row;
    }

    $response['status'] = 200;
    $response['data'] = $data;
    $response['message'] = MSG_SKU_CREDIT_FOUND;
} else {
    $response['status'] = 404;
    $response['message'] = MSG_SKU_CREDIT_NOT_FOUND;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
