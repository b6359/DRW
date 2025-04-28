<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $itemName = isset($input['itemName']) ? trim($input['itemName']) : null;
    $modelNo  = isset($input['modelNo']) ? trim($input['modelNo']) : null;
    $qty      = isset($input['qty']) ? (int)$input['qty'] : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($itemName && $modelNo && $qty && $createdBy) {
        $insert_data = array(
            "itemName"  => $itemName,
            "modelNo"   => $modelNo,
            "qty"       => $qty,
            "createdBy" => $createdBy
        );

        $insert_item = $db->INSERT(TBL_ITEM_MASTER, $insert_data);

        if ($insert_item) {
            $response['message'] = MSG_ITEM_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_ITEM_ADD_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
} else {
    $response["message"] = MSG_METHOD_NOT_ALLOWED;
    $response["status"] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
