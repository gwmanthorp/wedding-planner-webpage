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
$eventDate = $_POST['eventDate'];

$sql = "SELECT booking_date
		FROM venue_booking
		WHERE venue_id = (SELECT venue_id FROM venue_booking WHERE venue_id = $venueID";
		

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $venues = array();
    
    while($row = $result->fetch_assoc()) {
        $venues[] = $row;
    }
    
    // Return venues data as JSON response
    header('Content-Type: application/json');
    echo json_encode($venues);
} else {
    // No venues found
    echo json_encode(array());
}

$conn->close();
?>
