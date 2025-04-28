<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = [];

if ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);

    $id = isset($input['id']) ? (int)$input['id'] : null;

    if ($id) {
        $delete = $db->DELETE(TBL_FITTER_MASTER, "id = $id");

        if ($delete) {
            $response['message'] = MSG_RECORD_DELETED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FAILED_TO_DELETE;
            $response['status'] = 500;
            $response['error'] = $db->connection->error;
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
