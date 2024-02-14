<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$locations_name = mysqli_real_escape_string($conn, $_POST['locations_name']);
$latitude = mysqli_real_escape_string($conn, $_POST['latitude']);
$longitude = mysqli_real_escape_string($conn, $_POST['longitude']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum locations_id
        $sqlMaxId = "SELECT MAX(locations_id) AS MAX_ID FROM travel_locations";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value
        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }

        // Insert with the manually counted locations_id
        $sqlInsert = "INSERT INTO travel_locations (locations_id, locations_name, latitude, longitude)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'dssd', $maxId, $locations_name, $latitude, $longitude);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Location data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert location data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '2': // update
        $locations_id = mysqli_real_escape_string($conn, $_POST['locations_id']);

        $sqlUpdate = "UPDATE travel_locations
                SET locations_name=?, latitude=?, longitude=?
                WHERE locations_id=?";

        $stmt = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'sdds', $locations_name, $latitude, $longitude, $locations_id);

        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Location data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update location data: " . mysqli_error($conn);
        }

        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $locations_id = mysqli_real_escape_string($conn, $_POST['locations_id']);

        $sqlDelete = "DELETE FROM travel_locations WHERE locations_id=?";
        
        $stmt = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmt, 's', $locations_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Location data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete location data: " . mysqli_error($conn);
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
