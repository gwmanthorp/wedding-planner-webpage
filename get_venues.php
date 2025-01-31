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
$partySize = $_POST['partySize'];
$cateringGrade = $_POST['cateringGrade'];

// Prepare and execute SQL query to retrieve venues based on filters
		
$sql = "SELECT v.*, c.cost AS catering_cost
        FROM venue v
        JOIN catering c ON v.venue_id = c.venue_id
        WHERE v.capacity >= $partySize
        AND c.grade = $cateringGrade";

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
