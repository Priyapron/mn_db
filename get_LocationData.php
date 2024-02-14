<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

// Query to get locations_id and locations_name from travel_locations
$sql = "SELECT locations_id, locations_name FROM travel_locations";

$result = mysqli_query($conn, $sql);

$response = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = array(
            'locations_id' => $row['locations_id'],
            'locations_name' => $row['locations_name']
        );
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>
