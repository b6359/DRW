<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'POST') {
    // ADD VENDOR
    $input = json_decode(file_get_contents('php://input'), true);

    $name      = isset($input['name']) ? trim($input['name']) : null;
    $address   = isset($input['address']) ? trim($input['address']) : null;
    $mobile    = isset($input['mobile']) ? trim($input['mobile']) : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($name && $address && $mobile && $createdBy) {
        $data = array(
            'name'      => $name,
            'address'   => $address,
            'mobile'    => $mobile,
            'createdBy' => $createdBy
        );

        $insert = $db->INSERT(TBL_VENDOR_MASTER, $data);

        if ($insert) {
            $response['message'] = MSG_VENDOR_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_VENDOR_ADD_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
} elseif ($_REQUEST_METHOD === 'GET') {
    // FETCH ALL VENDORS
    $result = $db->SELECT(TBL_VENDOR_MASTER);

    if (is_object($result) && $result->num_rows > 0) {
        $vendors = array();
        while ($row = $result->fetch_assoc()) {
            $vendors[] = $row;
        }

        $response['data'] = $vendors;
        $response['status'] = 200;
    } else {
        $response['message'] = MSG_DATA_NOT_FOUND;
        $response['status'] = 404;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
