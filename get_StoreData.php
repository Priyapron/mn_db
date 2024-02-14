<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

// Query to get store_id and store_name from product_info
$sql = "SELECT store_id, store_name FROM store_info";

$result = mysqli_query($conn, $sql);

$response = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $response[] = array(
            'store_id' => (string)$row['store_id'],
            'store_name' => $row['store_name']
        );
    }
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>
