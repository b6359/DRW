<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = [];

if ($_REQUEST_METHOD === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = isset($input['id']) ? (int)$input['id'] : null;

    if ($id) {
        $updateData = [];

        if (isset($input['fitterName'])) $updateData['fitterName'] = $input['fitterName'];
        if (isset($input['marko'])) $updateData['marko'] = $input['marko'];
        if (isset($input['mobile'])) $updateData['mobile'] = $input['mobile'];
        if (isset($input['address'])) $updateData['address'] = $input['address'];
        if (isset($input['empCount'])) $updateData['empCount'] = (int)$input['empCount'];
        if (isset($input['pendingUpad'])) $updateData['pendingUpad'] = (int)$input['pendingUpad'];

        if (!empty($updateData)) {
            $where = array("id" => $id);
            $update = $db->UPDATE(TBL_FITTER_MASTER, $updateData, $where);

            if ($update) {
                $response['message'] = MSG_RECORD_UPDATED;
                $response['status'] = 200;
            } else {
                $response['message'] = MSG_FAILED_TO_UPDATE;
                $response['status'] = 500;
                $response['error'] = $db->connection->error;
            }
        } else {
            $response['message'] = MSG_NO_FIELDS_TO_UPDATE;
            $response['status'] = 400;
        }
    } else {
        $response['message'] = MSG_MISSING_ID;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
