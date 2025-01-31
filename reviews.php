<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Wedding Venue Finder</title>
		
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

		<!-- Bootstrap JS (popper.js and bootstrap.js are required for dropdowns and responsive navbar) -->
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
		
		<link rel="stylesheet" href="main.css">
		
		<?php
			$venueId = isset($_GET['venueId']) ? htmlspecialchars($_GET['venueId']) : '';
			$venueName = isset($_GET['venueName']) ? htmlspecialchars($_GET['venueName']) : '';
			$venueLatitude = isset($_GET['venueLatitude']) ? htmlspecialchars($_GET['venueLatitude']) : '';
			$venueLongitude = isset($_GET['venueLongitude']) ? htmlspecialchars($_GET['venueLongitude']) : '';
			$venueCapacity = isset($_GET['venueCapacity']) ? htmlspecialchars($_GET['venueCapacity']) : '';
			$weekendPrice = isset($_GET['weekendPrice']) ? htmlspecialchars($_GET['weekendPrice']) : '';
			$weekdayPrice = isset($_GET['weekdayPrice']) ? htmlspecialchars($_GET['weekdayPrice']) : '';
			$cateringCost = isset($_GET['cateringCost']) ? htmlspecialchars($_GET['cateringCost']) : '';
			$fromDate = isset($_GET['fromDate']) ? htmlspecialchars($_GET['fromDate']) : '';
			$tillDate = isset($_GET['tillDate']) ? htmlspecialchars($_GET['tillDate']) : '';
			$partySize = isset($_GET['partySize']) ? htmlspecialchars($_GET['partySize']) : '';
			
			$urlData = "?venueId=$venueId&venueName=$venueName&venueLatitude=$venueLatitude&venueLongitude=$venueLongitude&venueCapacity=$venueCapacity&weekdayPrice=$weekdayPrice&weekendPrice=$weekendPrice&cateringCost=$cateringCost&fromDate=$fromDate&tillDate=$tillDate&partySize=$partySize";
			$venueInfoUrl = "venue_details.php" . $urlData;
			$calendarUrl = "calendar.php" . $urlData;
			
			$location_api_url = "https://findthatpostcode.uk/points/". $venueLatitude . "," . $venueLongitude . ".json";
			$json_data = file_get_contents($location_api_url);
			$decoded_data = json_decode($json_data);
			
			$locationName = $decoded_data->included[0]->attributes->bua22_name;
			$nearestPostcode = $decoded_data->data->relationships->nearest_postcode->data->id;
		?>		
		
	</head>
	<body>
		<div id="all-container">
			<nav class="navbar navbar-expand-lg navbar-dark bg-light">
			  <a class="navbar-brand" href="wedding.php">Venue Search</a>
			  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			  </button>
			  <div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav">
				  <li class="nav-item">
					<a class="nav-link" href="<?php echo $venueInfoUrl; ?>">Info page</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" href="<?php echo $calendarUrl; ?>">Calendar</a>
				  </li>
				  <li class="nav-item active">
					<a class="nav-link" href="#">Reviews<span class="sr-only">(current)</span></a>
				  </li>
				</ul>
			  </div>
			</nav><br>
			
			<div id="reviews">
				<h1>Venue Reviews</h1>
				<canvas id="myChart" style="display: block; height: 389px;"></canvas>
				<script>
					let data = new FormData();
					data.append("venueId", <?php echo $venueId; ?>);
					
					document.addEventListener('DOMContentLoaded', function() {	
						// Send AJAX request to retrieve venues based on filters
						fetch('get_reviews.php', {
							method: 'POST',
							body: data
						})
						.then(response => {
							if (!response.ok) {
								throw new Error('Network response was not ok');
							}
							return response.json(); // Parse JSON response
						})
						.then(data => {
							// Check if data is an array and not empty
							if (Array.isArray(data) && data.length > 0) {
								reviewData = data;
								generateBarChart(reviewData);
							} else {
								console.log('No booking dates found');
							}
						})
						.catch(error => {
							console.error('Error fetching venues:', error);
						});
					});
					
					
					function generateBarChart(reviewData) {
						const xValues = ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"];
						const yValues = [];
						
						for (let i = 1; i <= 10; i++) {
							let count = reviewData.filter(num => num === String(i)).length;
							yValues.push(count);
						}
						
						console.log(yValues);

						const myChart = new Chart("myChart", {
							type: "bar",
							data: {
								labels: xValues,
								datasets: [{
									data: yValues,
									backgroundColor: 'rgba(88, 153, 147, 0.5)',
								}]
							},
							options: {
								responsive: true,
								maintainAspectRatio: false,
								plugins: {
									title: {
										display: true,
										text: 'Review Chart'
									},
									legend: {
										display: false
									}
								},
								scales: {
									y: {
										title: {
											display: true,
											text: 'Frequency of score'
										}
									},
									x: {
										title: {
											display: true,
											text: 'Review score'
										}
									}
								},
								layout: {
									padding: {
										bottom: 50
									}
								}
							}
						});
					}

				</script>
			</div>
		</div>
	</body>
</html>