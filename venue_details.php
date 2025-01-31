<!DOCTYPE html>
<html lang="en">
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
		
	</head>
	<body>
		<?php
		// Retrieve and sanitize each parameter from the URL
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
		$calendarUrl = "calendar.php" . $urlData;
		$reviewsUrl = "reviews.php" . $urlData;
		
		$totalWeekdayPrice = intval($weekdayPrice) + (intval($cateringCost) * intval($partySize));
		$totalWeekendPrice = intval($weekendPrice) + (intval($cateringCost) * intval($partySize));

		$location_api_url = "https://findthatpostcode.uk/points/". $venueLatitude . "," . $venueLongitude . ".json";
		$json_data = file_get_contents($location_api_url);
		$decoded_data = json_decode($json_data);

		$locationName = $decoded_data->included[0]->attributes->bua22_name;
		$nearestPostcode = $decoded_data->data->relationships->nearest_postcode->data->id;
		?>
		
		<div id="all-container">		
			<nav class="navbar navbar-expand-lg navbar-dark bg-light">
			  <a class="navbar-brand" href="wedding.php">Venue Search</a>
			  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			  </button>
			  <div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav">
				  <li class="nav-item active">
					<a class="nav-link" href="#">Info page<span class="sr-only">(current)</span></a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" href="<?php echo $calendarUrl; ?>">Calendar</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" href="<?php echo $reviewsUrl; ?>">Reviews</a>
				  </li>
				</ul>
			  </div>
			</nav><br>
			
			<div id="venue-info">
				<div>
					<h1>Venue Details</h1>
					<img id="venue-image" src="<?php echo $venueId; ?>.jpg"><br><br>
					<p><b>Name:</b> <?php echo $venueName; ?></p>
					<p><b>Location:</b> <?php echo "$locationName, $nearestPostcode"; ?></p>
					<p><b>Capacity:</b> <?php echo $venueCapacity; ?></p>
				</div>
				<div>
					<h2>Price Overview</h2>
					<table>
						<tr>
							<th></th>
							<th><b>Weekday</b></th>
							<th><b>Weekend</b></th>
						</tr>
						<tr>
							<td><b>Catering Cost per person:</b></td>
							<td colspan="2"><?php echo "£$cateringCost"; ?></td>
						</tr>
						<tr>
							<td><b>Party Size:</b></td>
							<td colspan="2"><?php echo $partySize; ?> guests</td>
						</tr>
						<tr>
							<td><b>Hire Price:</b></td>
							<td><?php echo "£$weekdayPrice"; ?></td>
							<td><?php echo "£$weekendPrice"; ?></td>
						</tr>
						<tr>
							<td><b>Total Cost for <?php echo $partySize; ?> guests:</b></td>
							<td><?php echo "£$totalWeekdayPrice"; ?></td>
							<td><?php echo "£$totalWeekendPrice"; ?></td>
						</tr>
					</table>
				</div>
			</div>
			
			<?php
				function getTileNumber($lat, $lon, $zoom) {
					$xtile = intval(($lon + 180) / 360 * pow(2, $zoom));
					$ytile = intval((1 - log(tan(deg2rad($lat)) + 1 / cos(deg2rad($lat))) / M_PI) / 2 * pow(2, $zoom));
					return array($xtile, $ytile);
				}

				function getLonLat($xtile, $ytile, $zoom) {
					$n = pow(2, $zoom);
					$lon_deg = $xtile / $n * 360.0 - 180.0;
					$lat_rad = atan(sinh(M_PI * (1 - 2 * $ytile / $n)));
					$lat_deg = rad2deg($lat_rad);
					return array($lon_deg, $lat_deg);
				}

				function LonLat_to_bbox($lat, $lon, $zoom) {
					$width = 425;
					$height = 350;
					$tile_size = 256;

					list($xtile, $ytile) = getTileNumber($lat, $lon, $zoom);

					$xtile_s = ($xtile * $tile_size - $width/2) / $tile_size;
					$ytile_s = ($ytile * $tile_size - $height/2) / $tile_size;
					$xtile_e = ($xtile * $tile_size + $width/2) / $tile_size;
					$ytile_e = ($ytile * $tile_size + $height/2) / $tile_size;

					list($lon_s, $lat_s) = getLonLat($xtile_s, $ytile_s, $zoom);
					list($lon_e, $lat_e) = getLonLat($xtile_e, $ytile_e, $zoom);

					$bbox = $lon_s. "%2C" .$lat_s. "%2C" .$lon_e. "%2C" .$lat_e;
					return $bbox;
				}

				// Example usage:
				$lat = $venueLatitude;
				$lon = $venueLongitude;
				$zoom = 13;

				$bbox = LonLat_to_bbox($lat, $lon, $zoom);
				
			?>
			<div id="map-container">
				<h1>Venue Area Map</h1> 
				<iframe id="map" src="https://www.openstreetmap.org/export/embed.html?bbox=<?php echo $bbox; ?>&amp;layer=mapnik"></iframe>
				<br>
				<small><a href="https://www.openstreetmap.org/#map=13/<?php echo $venueLatitude; ?>/<?php echo $venueLongitude; ?>">View Larger Map</a></small><br><br>
				<p><b>Location:</b> <?php echo "$locationName, $nearestPostcode"; ?></p>
			</div>	
			
		</div>
	</body>
</html>