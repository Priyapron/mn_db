<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$bus_id = mysqli_real_escape_string($conn, $_POST['bus_id']);
$bus_number = mysqli_real_escape_string($conn, $_POST['bus_number']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum bus_id from the database
        $sqlMaxId = "SELECT MAX(bus_id) AS MAX_ID FROM bus_info";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value
    
        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }
    
        // Increment the maxId for the new record
        $bus_id = $maxId;
    
        $sql = "INSERT INTO bus_info (bus_id, bus_number)
                VALUES (?, ?)";
    
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $bus_id, $bus_number);
    
        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Bus data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert bus data: " . mysqli_error($conn);
        }
    
        mysqli_stmt_close($stmt);
        break;

    case '2': // update
        $sql = "UPDATE bus_info
                SET bus_number=?
                WHERE bus_id=?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $bus_number, $bus_id);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Bus data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update bus data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $sql = "DELETE FROM bus_info WHERE bus_id=?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 's', $bus_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Bus data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete bus data: " . mysqli_error($conn);
        }
        
        mysqli_stmt_close($stmt);
        break;

    default:
        $response['status'] = 400;
        $response['message'] = "Invalid case provided";
        break;
}

echo json_encode($response);

mysqli_close($conn);
?>
