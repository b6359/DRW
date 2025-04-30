<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $fitterId = isset($input['fitterId']) ? (int)$input['fitterId'] : null;
    $productMasterIds = isset($input['productMasterIds']) ? $input['productMasterIds'] : [];
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($fitterId && !empty($productMasterIds) && $createdBy) {
        $repairingData = array(
            'fitterId' => $fitterId,
            'createdBy' => $createdBy
        );

        $repairingAdd = $db->INSERT(TBL_REPAIRING, $repairingData);
        if ($repairingAdd) {
            $repairingId = $db->connection->insert_id;
            foreach ($productMasterIds as $item) {
                $productMasterId = $item['productMasterId'];
                $qty = $item['qty'];

                $productData = array(
                    'repairingId' => $repairingId,
                    'productMasterId' => $productMasterId,
                    'qty' => $qty
                );
                $db->INSERT(TBL_REPAIRING_PRODUCT, $productData);
            }

            $response['message'] = "Repairing entry added";
            $response['status'] = 200;
        } else {
            $response['message'] = "Failed to add repairing";
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
}

elseif ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    $repairingId = isset($input['id']) ? (int)$input['id'] : null;
    $fitterId = isset($input['fitterId']) ? (int)$input['fitterId'] : null;

    if ($repairingId && $fitterId) {
        $updateData = array('fitterId' => $fitterId);
        $where = array('id' => $repairingId);
        $update = $db->UPDATE(TBL_REPAIRING, $updateData, $where);

        $response['message'] = $update ? "Repairing updated" : "Failed to update";
        $response['status'] = $update ? 200 : 500;
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
}

elseif ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : null;

    if ($id) {
        $where = array('id' => $id);
        $delete = $db->DELETE(TBL_REPAIRING, $where);
        $db->DELETE(TBL_REPAIRING_PRODUCT, array('repairingId' => $id));

        $response['message'] = $delete ? "Repairing deleted" : "Failed to delete";
        $response['status'] = $delete ? 200 : 500;
    } else {
        $response['message'] = "Repairing ID is required";
        $response['status'] = 422;
    }
}

elseif ($_REQUEST_METHOD === 'GET') {
    $query = "
        SELECT r.*, f.fitterName, f.mobile, f.address 
        FROM tbl_repairing r 
        JOIN tbl_fitter_master f ON r.fitterId = f.id
    ";
    $result = $db->connection->query($query);
    $data = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $repairingId = $row['id'];

            $productResult = $db->connection->query("
                SELECT rp.*, p.productName 
                FROM tbl_repairing_product rp
                JOIN tbl_product_master p ON rp.productMasterId = p.id
                WHERE rp.repairingId = $repairingId
            ");

            $row['products'] = [];
            if ($productResult && $productResult->num_rows > 0) {
                while ($p = $productResult->fetch_assoc()) {
                    $row['products'][] = $p;
                }
            }

            $data[] = $row;
        }
    }

    $response['status'] = 200;
    $response['data'] = $data;
}

else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
