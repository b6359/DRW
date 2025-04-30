<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : null;
    $itemName = isset($input['itemName']) ? trim($input['itemName']) : null;
    $modelNo  = isset($input['modelNo']) ? trim($input['modelNo']) : null;
    $rate  = isset($input['rate']) ? trim($input['rate']) : 0;
    $qty      = isset($input['qty']) ? (int)$input['qty'] : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($id && $itemName && $modelNo && $qty && $createdBy) {
        $update_data = array(
            "itemName"  => $itemName,
            "modelNo"   => $modelNo,
            "qty"       => $qty,
            "rate"       => $rate,
            "createdBy" => $createdBy
        );

        $where = array("id" => $id);
        $update_item = $db->UPDATE(TBL_ITEM_MASTER, $update_data, $where);

        if ($update_item) {
            $response['message'] = MSG_ITEM_UPDATED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_ITEM_UPDATE_FAIL;
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
