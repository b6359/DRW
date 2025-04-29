<?php
include_once '../config/constant.php';
include_once '../config/config.php';
$db = new DataBase();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$response = array();

if ($requestMethod === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    $fitterId = isset($input['fitterId']) ? (int)$input['fitterId'] : null;
    $amount = isset($input['amount']) ? (float)$input['amount'] : null;
    $remark = isset($input['remark']) ? $input['remark'] : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($fitterId && $amount && $createdBy) {
        $insert_data = array(
            "fitterId" => $fitterId,
            "amount" => $amount,
            "remark" => $remark,
            "createdBy" => $createdBy
        );

        $insert_upad = $db->INSERT(TBL_UPAD, $insert_data);

        if ($insert_upad) {
            // Increment pendingUpad
            $db->ROW_QUERY("UPDATE " . TBL_FITTER_MASTER . " SET pendingUpad = pendingUpad + $amount WHERE id = $fitterId");

            $response['message'] = "Upad added successfully.";
            $response['status'] = 200;
        } else {
            $response['message'] = "Failed to add upad.";
            $response['status'] = 500;
        }
    } else {
        $response['message'] = "All fields are required.";
        $response['status'] = 422;
    }
}

// === UPDATE ===
elseif ($requestMethod === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);

    $id = isset($input['id']) ? (int)$input['id'] : null;
    $fitterId = isset($input['fitterId']) ? (int)$input['fitterId'] : null;
    $amount = isset($input['amount']) ? (float)$input['amount'] : null;
    $remark = isset($input['remark']) ? $input['remark'] : null;
    $createdBy = isset($input['createdBy']) ? (int)$input['createdBy'] : null;

    if ($id && $amount) {
        $existing = $db->SELECT(TBL_UPAD, ['fitterId', 'amount'], ['id' => $id]);

        if ($existing && $existing->num_rows > 0) {
            $old = $existing->fetch_assoc();
            $fitterId = $old['fitterId'];
            $oldAmount = $old['amount'];

            // Update pendingUpad = pendingUpad - old + new
            $diff = $amount - $oldAmount;
            $db->ROW_QUERY("UPDATE " . TBL_FITTER_MASTER . " SET pendingUpad = pendingUpad + $diff WHERE id = $fitterId");

            $update_data = array("amount" => $amount);
            $update_data = [
                'amount'   => $amount,
                'fitterId' => $fitterId,
                'remark'   => $remark,
                'createdBy'   => $createdBy
            ];
            $update = $db->UPDATE(TBL_UPAD, $update_data, ['id' => $id]);

            if ($update) {
                $response['message'] = "Upad updated successfully.";
                $response['status'] = 200;
            } else {
                $response['message'] = "Failed to update upad.";
                $response['status'] = 500;
            }
        } else {
            $response['message'] = "Upad not found.";
            $response['status'] = 404;
        }
    } else {
        $response['message'] = "ID and new amount required.";
        $response['status'] = 422;
    }
}

// === DELETE ===
elseif ($requestMethod === 'DELETE') {
    $input = json_decode(file_get_contents("php://input"), true);

    $id = isset($input['id']) ? (int)$input['id'] : null;

    if ($id) {
        $existing = $db->SELECT(TBL_UPAD, ['fitterId', 'amount'], ['id' => $id]);

        if ($existing && $existing->num_rows > 0) {
            $row = $existing->fetch_assoc();
            $fitterId = $row['fitterId'];
            $amount = $row['amount'];

            $delete = $db->DELETE(TBL_UPAD, ['id' => $id]);

            if ($delete) {
                $db->ROW_QUERY("UPDATE " . TBL_FITTER_MASTER . " SET pendingUpad = pendingUpad - $amount WHERE id = $fitterId");

                $response['message'] = "Upad deleted successfully.";
                $response['status'] = 200;
            } else {
                $response['message'] = "Failed to delete upad.";
                $response['status'] = 500;
            }
        } else {
            $response['message'] = "Upad not found.";
            $response['status'] = 404;
        }
    } else {
        $response['message'] = "ID required.";
        $response['status'] = 422;
    }
}

// === GET ===
elseif ($requestMethod === 'GET') {
    $query = "
        SELECT u.*, f.fitterName, f.pendingUpad
        FROM " . TBL_UPAD . " u
        JOIN " . TBL_FITTER_MASTER . " f ON u.fitterId = f.id
        ORDER BY u.id DESC
    ";

    $result = $db->ROW_QUERY($query);
    $data = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        $response['message'] = "Upad list fetched successfully.";
        $response['status'] = 200;
        $response['data'] = $data;
    } else {
        $response['message'] = "No data found.";
        $response['status'] = 404;
    }
} else {
    $response['message'] = "Method not allowed.";
    $response['status'] = 405;
}

header('Content-Type: application/json');
echo json_encode($response);
