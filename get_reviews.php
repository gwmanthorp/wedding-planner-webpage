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
		
$sql = "SELECT score
        FROM venue_review_score
        WHERE venue_id = $venueId";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $reviews = array();
    
    while($row = $result->fetch_assoc()) {
        $reviews[] = $row["score"];
    }
    
    // Return venues data as JSON response
    header('Content-Type: application/json');
    echo json_encode(array_values($reviews));
} else {
    // No venues found
    echo json_encode(array());
}

$conn->close();
?>
