<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'GET') {
    $items = $db->SELECT(TBL_ITEM_MASTER, null, null);

    if ($items && mysqli_num_rows($items) > 0) {
        $item_data = [];
        while ($row = $items->fetch_assoc()) {
            $item_data[] = array(
                "id"        => intval($row['id']),
                "itemName"  => $row['itemName'],
                "modelNo"   => $row['modelNo'],
                "qty"       => intval($row['qty']),
                "createdBy" => intval($row['createdBy']),
                "createdAt" => $row['createdAt']
            );
        }
        $response["message"] = MSG_GET_ALL_ITEM_FOUND;
        $response["status"] = 200;
        $response["data"] = $item_data;
    } else {
        $response["message"] = MSG_ITEM_NOT_FOUND;
        $response["status"] = 404;
        $response["data"] = null;
    }
} else {
    $response["message"] = MSG_METHOD_NOT_ALLOWED;
    $response["status"] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
