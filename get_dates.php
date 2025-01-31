<?php
// Database connection parameters
$servername = "sci-mysql";
$username = "coa123wuser";
$password = "grt64dkh!@2FD";
$dbname = "coa123wdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve filter parameters from POST request
$venueId = $_POST['venueId'];

$sql = "SELECT booking_date
		FROM venue_booking
		WHERE venue_id = $venueId";
		

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $dates = array();
    
    while($row = $result->fetch_assoc()) {
        $dates[] = $row["booking_date"];
    }
    
    // Return venues data as JSON response
    echo json_encode(array_values($dates));
} else {
    // No venues found
    echo json_encode(array());
}

$conn->close();
?>
