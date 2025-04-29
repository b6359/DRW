<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($_REQUEST_METHOD === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $fitterSupplyId = isset($input['fitterSupplyId']) ? $input['fitterSupplyId'] : null;

    if ($fitterSupplyId) {
        // Delete related extra items
        $db->DELETE(TBL_FITTER_SUPPLY_EXTRA_ITEM, "fitterSupplyId = $fitterSupplyId");
        // Delete related rejection items
        $db->DELETE(TBL_FITTER_SUPPLY_REJECTION_ITEM, "fitterSupplyId = $fitterSupplyId");
        // Delete related product items
        $db->DELETE(TBL_FITTER_SUPPLY_PRODUCT, "fitterSupplyId = $fitterSupplyId");
        // Delete fitter supply
        $delete = $db->DELETE(TBL_FITTER_SUPPLY, "id = $fitterSupplyId");

        if ($delete) {
            $response['message'] = MSG_FITTER_SUPPLY_DELETED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_FITTER_SUPPLY_DELETE_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_METHOD_NOT_ALLOWED;
        $response['status'] = 400;
    }
} else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
