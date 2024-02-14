<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$locations_id = mysqli_real_escape_string($conn, $_POST['locations_id']);
$time = mysqli_real_escape_string($conn, $_POST['time']); // Assuming $_POST['time'] is a string in 'HH:MM:SS' format

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum route_No
        $sqlMaxNo = "SELECT MAX(route_No) AS MAX_NO FROM bus_route";
        $resultMaxNo = mysqli_query($conn, $sqlMaxNo) or die(mysqli_error($conn));
        $maxNo = 1; // Default value
        while ($rowMaxNo = mysqli_fetch_array($resultMaxNo)) {
            if ($rowMaxNo["MAX_NO"] != "") {
                $maxNo = $rowMaxNo["MAX_NO"] + 1;
            }
        }

        $sqlInsert = "INSERT INTO bus_route (route_No, locations_id, time) VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'dss', $maxNo, $locations_id, $time);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 201; // Created
            $response['message'] = "Bus route data inserted successfully";
        } else {
            $response['status'] = 500; // Internal Server Error
            $response['message'] = "Failed to insert bus route data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '2': // update
        $route_No = mysqli_real_escape_string($conn, $_POST['route_No']);

        $sqlUpdate = "UPDATE bus_route SET locations_id=?, time=? WHERE route_No=?";

        $stmt = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'dss', $locations_id, $time, $route_No);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200; // OK
            $response['message'] = "Bus route data updated successfully";
        } else {
            $response['status'] = 500; // Internal Server Error
            $response['message'] = "Failed to update bus route data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $route_No = mysqli_real_escape_string($conn, $_POST['route_No']);

        $sqlDelete = "DELETE FROM bus_route WHERE route_No=?";

        $stmt = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmt, 'd', $route_No);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200; // OK
            $response['message'] = "Bus route data deleted successfully";
        } else {
            $response['status'] = 500; // Internal Server Error
            $response['message'] = "Failed to delete bus route data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    default:
        $response['status'] = 400; // Bad Request
        $response['message'] = "Invalid case provided";
        break;
}

echo json_encode($response);

mysqli_close($conn);
?>
