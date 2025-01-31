<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Wedding Venue Finder</title>
		
		<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

		<!-- Bootstrap JS (popper.js and bootstrap.js are required for dropdowns and responsive navbar) -->
		<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
		
		<link rel="stylesheet" href="main.css">
		<script src="calendar.js"></script>
		
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
			$reviewsUrl = "reviews.php" . $urlData;
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
				  <li class="nav-item active">
					<a class="nav-link" href="#">Calendar<span class="sr-only">(current)</span></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" href="<?php echo $reviewsUrl; ?>">Reviews</a>
				  </li>
				</ul>
			  </div>
			</nav><br>
			
			<div class="calendar-info">
				<h1>Venue Availability</h1>
				<h5>All dates in red are unavailable</h5>
				<h5>All dates in green are all of the available days within your date range</h5>
				
				<div class="calendar">
					<div class="month">
						<button id="monthBackBtn" onclick="previousMonth()"><</button>
						<h2 id="month-year">May 2024</h2>
						<button id="monthForwardBtn" onclick="nextMonth()">></button>
					</div>
					<div class="weekdays">
						<div>Mon</div>
						<div>Tue</div>
						<div>Wed</div>
						<div>Thu</div>
						<div>Fri</div>
						<div>Sat</div>
						<div>Sun</div>
					</div>
					<div class="days"></div>
				</div>

				<h5><b>Date range entered: </b>From <span id="fromDate"><?php echo $fromDate; ?></span> till <span id="tillDate"><?php echo $tillDate; ?></span></h5>
			</div>
		</div>
	</body>
</html>