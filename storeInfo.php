<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$store_id = mysqli_real_escape_string($conn, $_POST['store_id']);
$store_name = mysqli_real_escape_string($conn, $_POST['store_name']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum store_id
        $sqlMaxId = "SELECT MAX(store_id) AS MAX_ID FROM store_info";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value
        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }

        // Insert new data with the next available store_id
        $sqlInsert = "INSERT INTO store_info (store_id, store_name) VALUES (?, ?)";
        $stmtInsert = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmtInsert, 'ss', $maxId, $store_name);

        if (mysqli_stmt_execute($stmtInsert)) {
            $response['status'] = 200;
            $response['message'] = "Store data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert store data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtInsert);
        break;

    case '2': // update
        // Update data in store_info
        $sqlUpdate = "UPDATE store_info SET store_name=? WHERE store_id=?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, 'ss', $store_name, $store_id);

        if (mysqli_stmt_execute($stmtUpdate)) {
            $response['status'] = 200;
            $response['message'] = "Store data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update store data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtUpdate);
        break;

    case '3': // delete
        // Delete data from store_info
        $sqlDelete = "DELETE FROM store_info WHERE store_id=?";
        $stmtDelete = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmtDelete, 's', $store_id);

        if (mysqli_stmt_execute($stmtDelete)) {
            $response['status'] = 200;
            $response['message'] = "Store data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete store data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmtDelete);
        break;

    default:
        $response['status'] = 400;
        $response['message'] = "Invalid case provided";
        break;
}

echo json_encode($response);

mysqli_close($conn);
?>
