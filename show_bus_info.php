<?php
// Include database connection
header('Access-Control-Allow-Origin: *');
include("conn.php");

// Check if the ID card is provided
if (isset($_POST['bus_id'])) {
    // Sanitize and validate the input
    $bus_id = filter_input(INPUT_POST, 'bus_id', FILTER_SANITIZE_STRING);

    // Prepare and execute the SQL query
    $sql = "SELECT * FROM bus_info WHERE bus_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $bus_id);

    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);

        // Check if any row is returned
        if ($row = mysqli_fetch_assoc($result)) {
            // Return the health information as JSON
            echo json_encode($row);
        } else {
            // Return an empty JSON object if no data is found
            echo json_encode([]);
        }
    } else {
        // Return an error message if the query fails
        echo json_encode(['error' => 'Failed to execute query']);
    }

    mysqli_stmt_close($stmt);
} else {
    // Return an error message if the ID card is not provided
    echo json_encode(['error' => 'ID card is not provided']);
}

mysqli_close($conn);
?>
