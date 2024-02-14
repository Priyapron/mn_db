<?php
include 'conn.php';
header("Access-Control-Allow-Origin: *");

$xcase = $_POST['case'];

// Common variables
$store_id = mysqli_real_escape_string($conn, $_POST['store_id']);
$product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
$product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
$counting_unit = mysqli_real_escape_string($conn, $_POST['counting_unit']);
$price = mysqli_real_escape_string($conn, $_POST['price']);

$response = array();

switch ($xcase) {
    case '1': // insert
        // Retrieve the maximum product_id
        $sqlMaxId = "SELECT MAX(product_id) AS MAX_ID FROM product_info";
        $resultMaxId = mysqli_query($conn, $sqlMaxId) or die(mysqli_error($conn));
        $maxId = 1; // Default value
        while ($rowMaxId = mysqli_fetch_array($resultMaxId)) {
            if ($rowMaxId["MAX_ID"] != "") {
                $maxId = $rowMaxId["MAX_ID"] + 1;
            }
        }
    
        // Insert with the manually counted product_id
        $sqlInsert = "INSERT INTO product_info (store_id, product_id, product_name, counting_unit, price)
            VALUES (?, ?, ?, ?, ?)";
    
        $stmt = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmt, 'ssssd', $store_id, $maxId, $product_name, $counting_unit, $price);
    
        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Product data inserted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to insert product data: " . mysqli_error($conn);
        }
    
        mysqli_stmt_close($stmt);
        break;
    
    case '2': // update
        $sqlUpdate = "UPDATE product_info
            SET store_id=?, product_name=?, counting_unit=?, price=?
            WHERE product_id=?";
    
        $stmt = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'dssds', $store_id, $product_name, $counting_unit, $price, $product_id);
    
        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Product data updated successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to update product data: " . mysqli_error($conn);
        }
    
        mysqli_stmt_close($stmt);
        break;

    case '3': // delete
        $sqlDelete = "DELETE FROM product_info WHERE product_id=?";
        
        $stmt = mysqli_prepare($conn, $sqlDelete);
        mysqli_stmt_bind_param($stmt, 'd', $product_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $response['status'] = 200;
            $response['message'] = "Product data deleted successfully";
        } else {
            $response['status'] = 500;
            $response['message'] = "Failed to delete product data: " . mysqli_error($conn);
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
