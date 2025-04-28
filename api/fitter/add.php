<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = [];

if ($_REQUEST_METHOD === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $fitterData = [
        "fitterName" => $input['fitterName'] ?? null,
        "marko" => $input['marko'] ?? null,
        "mobile" => $input['mobile'] ?? null,
        "address" => $input['address'] ?? null,
        "empCount" => isset($input['empCount']) ? (int)$input['empCount'] : 0,
        "createdBy" => $input['createdBy'] ?? null,
        "pendingUpad" => isset($input['pendingUpad']) ? (int)$input['pendingUpad'] : 0
    ];

    if ($fitterData['fitterName'] && $fitterData['createdBy'] !== null) {
        $insert = $db->INSERT(TBL_FITTER_MASTER, $fitterData);

        if ($insert) {
            $response['message'] = MSG_RECORD_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FAILED_TO_ADD;
            $response['status'] = 500;
            $response['error'] = $db->connection->error;
        }
    } else {
        $response['message'] = MSG_MISSING_REQUIRED_FIELDS;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
