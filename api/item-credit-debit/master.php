<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $vendorId     = isset($input['vendorId']) ? (int)$input['vendorId'] : null;
    $qty          = isset($input['qty']) ? (int)$input['qty'] : null;
    $creditOrDebit = isset($input['creditOrDebit']) ? (int)$input['creditOrDebit'] : null;
    $itemIds      = isset($input['itemIds']) ? $input['itemIds'] : null;
    $createdBy    = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($vendorId && $qty && is_array($itemIds) && count($itemIds) > 0) {
        $data = array(
            "vendorId" => $vendorId,
            "qty" => $qty,
            "creditOrDebit" => $creditOrDebit,
            "createdBy" => $createdBy
        );

        $insert_id = $db->INSERT(TBL_ITEM_CREDIT_DEBIT_MASTER, $data);
        if ($insert_id) {
            $itemCreditDebitId = $db->connection->insert_id;
            foreach ($itemIds as $itemId) {
                $db->INSERT(TBL_ITEM_CREDIT_DEBIT_ITEM, [
                    "itemCreditDebitId" => $itemCreditDebitId,
                    "itemId" => $itemId
                ]);
            }
            $response['message'] = MSG_ITEM_CREDIT_DEBIT_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_ITEM_CREDIT_DEBIT_FAILED;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }

} elseif ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = isset($input['id']) ? (int)$input['id'] : null;
    $vendorId = isset($input['vendorId']) ? (int)$input['vendorId'] : null;
    $qty = isset($input['qty']) ? (int)$input['qty'] : null;
    $creditOrDebit = isset($input['creditOrDebit']) ? (int)$input['creditOrDebit'] : null;

    if ($id && $vendorId && $qty && isset($creditOrDebit)) {
        $update_data = [
            "vendorId" => $vendorId,
            "qty" => $qty,
            "creditOrDebit" => $creditOrDebit
        ];

        $where = ["id" => $id];

        $update = $db->UPDATE(TBL_ITEM_CREDIT_DEBIT_MASTER, $update_data, $where);

        if ($update) {
            $response['message'] = MSG_ITEM_CREDIT_DEBIT_UPDATED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_ITEM_CREDIT_DEBIT_FAILED;
            $response['status'] = 500;
        }

    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }

} elseif ($_REQUEST_METHOD === 'DELETE') {
    parse_str(file_get_contents("php://input"), $input);
    $id = isset($input['id']) ? (int)$input['id'] : null;

    if ($id) {
        $db->DELETE(TBL_ITEM_CREDIT_DEBIT_ITEM, ["itemCreditDebitId" => $id]);
        $delete = $db->DELETE(TBL_ITEM_CREDIT_DEBIT_MASTER, ["id" => $id]);

        if ($delete) {
            $response['message'] = MSG_ITEM_CREDIT_DEBIT_DELETED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_ITEM_CREDIT_DEBIT_FAILED;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }

} elseif ($_REQUEST_METHOD === 'GET') {
    $result = $db->SELECT(TBL_ITEM_CREDIT_DEBIT_MASTER);

    $list = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $itemIds = [];

            $itemResult = $db->SELECT(TBL_ITEM_CREDIT_DEBIT_ITEM, null, ["itemCreditDebitId" => $row['id']]);
            if ($itemResult && $itemResult->num_rows > 0) {
                while ($item = $itemResult->fetch_assoc()) {
                    $itemIds[] = $item['itemId'];
                }
            }

            $row['itemIds'] = $itemIds;
            $list[] = $row;
        }

        $response['data'] = $list;
        $response['message'] = MSG_ITEM_CREDIT_DEBIT_LIST;
        $response['status'] = 200;
    } else {
        $response['data'] = [];
        $response['message'] = "No records found.";
        $response['status'] = 204;
    }
} else {
    $response["message"] = MSG_METHOD_NOT_ALLOWED;
    $response["status"] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
