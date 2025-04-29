<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$_REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
$response = [];

if ($_REQUEST_METHOD === 'POST') {
    // Add Salary
    $input = json_decode(file_get_contents('php://input'), true);

    $fitterId  = isset($input['fitterId']) ? (int)$input['fitterId'] : null;
    $amount    = isset($input['amount']) ? (float)$input['amount'] : null;
    $fromDate  = isset($input['fromDate']) ? trim($input['fromDate']) : null;
    $toDate    = isset($input['toDate']) ? trim($input['toDate']) : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($fitterId && $amount && $fromDate && $toDate) {
        $data = [
            'fitterId'  => $fitterId,
            'amount'    => $amount,
            'fromDate'  => $fromDate,
            'toDate'    => $toDate,
            'createdBy' => $createdBy
        ];

        $insert = $db->INSERT(TBL_SALARY, $data);

        if ($insert) {
            $response['message'] = MSG_SALARY_ADDED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_SALARY_ADD_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
}

elseif ($_REQUEST_METHOD === 'PUT') {
    // Update Salary
    $input = json_decode(file_get_contents('php://input'), true);

    $id       = isset($input['id']) ? (int)$input['id'] : null;
    $amount   = isset($input['amount']) ? (float)$input['amount'] : null;
    $fromDate = isset($input['fromDate']) ? trim($input['fromDate']) : null;
    $toDate   = isset($input['toDate']) ? trim($input['toDate']) : null;

    if ($id && $amount && $fromDate && $toDate) {
        $update_data = [
            'amount'   => $amount,
            'fromDate' => $fromDate,
            'toDate'   => $toDate
        ];

        $where = ['id' => $id];
        $update = $db->UPDATE(TBL_SALARY, $update_data, $where);

        if ($update) {
            $response['message'] = MSG_SALARY_UPDATED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_SALARY_UPDATE_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
}

elseif ($_REQUEST_METHOD === 'DELETE') {
    // Delete Salary
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : null;

    if ($id) {
        $delete = $db->DELETE(TBL_SALARY, ['id' => $id]);

        if ($delete) {
            $response['message'] = MSG_SALARY_DELETED;
            $response['status'] = 200;
        } else {
            $response['message'] = MSG_SALARY_DELETE_FAIL;
            $response['status'] = 500;
        }
    } else {
        $response['message'] = MSG_REQUIRED_ALL;
        $response['status'] = 422;
    }
}

elseif ($_REQUEST_METHOD === 'GET') {
    // Get All Salaries
    $result = $db->SELECT(TBL_SALARY);

    if (is_object($result) && $result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $response['data'] = $data;
        $response['status'] = 200;
    } else {
        $response['message'] = MSG_DATA_NOT_FOUND;
        $response['status'] = 404;
    }
}

else {
    $response['message'] = MSG_METHOD_NOT_ALLOWED;
    $response['status'] = 400;
}

header('Content-Type: application/json');
echo json_encode($response);
?>
