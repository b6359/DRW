<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'GET') {
    // Get the productMasterId from the query string
    $productMasterId = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if ($productMasterId) {
        // Prepare the query to get the product by ID
        $query = "
            SELECT p.id AS productMasterId, 
                   p.productName, 
                   p.modelNo, 
                   p.rate, 
                   p.pendingForBelt, 
                   p.createdBy, 
                   p.createdAt,
                   GROUP_CONCAT(im.id) AS itemMasterIds,
                   GROUP_CONCAT(im.itemName) AS itemNames,
                   GROUP_CONCAT(im.modelNo) AS itemModelNos,
                   GROUP_CONCAT(pi.qty) AS itemQtys
            FROM tbl_product_master p
            JOIN tbl_product_item pi ON p.id = pi.productMasterId
            JOIN tbl_item_master im ON pi.itemMasterId = im.id
            WHERE p.id = $productMasterId
            GROUP BY p.id;
        ";

        $result = $db->connection->query($query);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $items = [];
            $itemMasterIds = explode(',', $row['itemMasterIds']);
            $itemNames = explode(',', $row['itemNames']);
            $itemModelNos = explode(',', $row['itemModelNos']);
            $itemQtys = explode(',', $row['itemQtys']);

            for ($i = 0; $i < count($itemMasterIds); $i++) {
                $items[] = [
                    'id' => $itemMasterIds[$i],
                    'itemName' => $itemNames[$i],
                    'modelNo' => $itemModelNos[$i],
                    'qty' => $itemQtys[$i],
                ];
            }

            $product_data = [
                'id' => $row['productMasterId'],
                'productName' => $row['productName'],
                'modelNo' => $row['modelNo'],
                'rate' => $row['rate'],
                'pendingForBelt' => $row['pendingForBelt'],
                'createdBy' => $row['createdBy'],
                'createdAt' => $row['createdAt'],
                'itemMaster' => $items,
            ];

            $response["message"] = MSG_GET_ALL_PRODUCTS_FOUND;
            $response["status"] = 200;
            $response["data"] = $product_data;
        } else {
            $response["message"] = MSG_PRODUCT_NOT_FOUND;
            $response["status"] = 404;
            $response["data"] = null;
        }
    } else {
        $response["message"] = "Product ID is required.";
        $response["status"] = 400;
    }
} else {
    $response["message"] = MSG_METHOD_NOT_ALLOWED;
    $response["status"] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
