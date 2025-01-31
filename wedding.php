<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding Venue Finder</title>
	
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<link rel="stylesheet" href="wedding.css">
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>

<div class="title-card">
	<h1>Venue Wow</h1>
	<h4>Find Your Perfect Wedding Venue</h4>
</div>
<div class="main-container">

	<div class="filters-container">
	
		<form id="venueFilterForm" novalidation>
			<span id="eventDateErr" style="color:red"></span>
			<label class="mb-1"><b>Date Range</b></label>
			<div class='date-picker'>
				<label for="from" class="mb-1">From</label><br>
				<input type="date" class="mb-3" id="fromDate" name="from"><br>
				<label for="till" class="mb-1">Till</label><br>
				<input type="date" class="mb-1" id="tillDate" name="till">
			</div><br>
			
			<span id="partySizeErr" style="color:red"></span>
			
			
			<div class="form-group mb-3">
				<label for="partySize" class="mb-1"><b>Party Size</b></label>
				<input type="number" class="form-control mb-1" id="partySize" aria-describedby="party-size" name="partySize" placeholder="Enter party size">
				<small id="partySizeHelp" class="form-text text-muted">We'll only display venues with a capacity greater than your party size</small>
			  </div>
			
			<span id="cateringGradeErr" style="color:red"></span>
			<label for="cateringGrade"><b>Catering Grade:</b></label>
			<div class="cateringGrade">
				<span><i class="far fa-star fa-lg"></i></span>
				<span><i class="far fa-star fa-lg"></i></span>
				<span><i class="far fa-star fa-lg"></i></span>
				<span><i class="far fa-star fa-lg"></i></span>
				<span><i class="far fa-star fa-lg"></i></span>
			</div>

			<p id="cateringGrade">Grade selected:</p>
			<button type="submit" class="btn btn-warning">Search Venues!</button>
			<p id="venueCollection" class="mt-4"></p>
		</form>
	</div>
	
	<div class="venue-container" id="venueContainer">
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
            const stars = document.querySelectorAll('.cateringGrade > span');

            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    // Update the rating value display
                    document.getElementById('cateringGrade').textContent = `Grade selected: ${index + 1}`;
					document.getElementById('cateringGrade').value = `${index + 1}`;

                    // Reset all stars to empty
                    stars.forEach((s, i) => {
                        if (i <= index) {
                            s.innerHTML = '<i class="fas fa-star fa-lg"></i>'; // Fill selected stars
                        } else {
                            s.innerHTML = '<i class="far fa-star fa-lg"></i>'; // Empty unselected stars
                        }
                    });

                });
            });
        });

document.getElementById('venueFilterForm').addEventListener('submit', function(event) {
    event.preventDefault();
    
    // Fetch form data
    const formData = new FormData(this);
	for (const [key, value] of formData) {
		console.log(`${key}: ${value}\n`);
	}
	
	if (dataValidation(formData)){
		formData.append("cateringGrade", document.getElementById("cateringGrade").value);
		// Send AJAX request to retrieve venues based on filters
		fetch('get_venues.php', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.length == 0){
				venueContainer.innerHTML = '';
				document.getElementById("venueCollection").innerHTML = "<b>No venues with matching criteria</b>";
			}else {
				document.getElementById("venueCollection").innerHTML = "";
				displayVenues(data);
			}
		})
		.catch(error => {
			console.error('Error fetching venues:', error);
		});
	}
	
});

function dataValidation(formData){
	let isValid = true;
	
	const currentDate = new Date();
	const fromDate = new Date(formData.get("from"));
	const tillDate = new Date(formData.get("till"));

	// Validation check for event date, can't be before current date
	if (fromDate < currentDate || tillDate < currentDate){
		document.getElementById("eventDateErr").innerHTML = "* Given date is in the past<br>";
		isValid = false;
	} else if (fromDate > tillDate){
		document.getElementById("eventDateErr").innerHTML = "* Start date is later than end date<br>";
		isValid = false;
	} else if (fromDate == "Invalid Date" || tillDate == "Invalid Date"){
		document.getElementById("eventDateErr").innerHTML = "* One or both of given dates are null<br>";
		isValid = false;
	}else {
		document.getElementById("eventDateErr").innerHTML = "";
	}
	
	// Validation check for party size, can't be less than 1
	if (formData.get("partySize") < 1){
		document.getElementById("partySizeErr").innerHTML = "* Party size must be greater than 0<br>";
		isValid = false;
	} else {
		document.getElementById("partySizeErr").innerHTML = "";
	}
	
	if (document.getElementById("cateringGrade").value < 1 || typeof document.getElementById("cateringGrade").value == "undefined"){
		document.getElementById("cateringGradeErr").innerHTML = "* Catering grade must be greater than 0<br>";
		isValid = false;
	} else {
		document.getElementById("cateringGradeErr").innerHTML = "";
	}
	return isValid;
}

function displayVenues(venues) {
    const venueContainer = document.getElementById('venueContainer');
    venueContainer.innerHTML = ''; // Clear previous results
    
    // Show loading message or icon
    const loadingMessage = document.createElement('p');
    loadingMessage.textContent = 'Loading venues...';
    venueContainer.appendChild(loadingMessage);
    
	const venueCards = [];
	
    // Array to store all AJAX promises
    const ajaxPromises = [];
	
    venues.forEach(venue => {
        const venueCard = document.createElement('div');
        venueCard.className = 'venue-card';
		
		const venueImg = document.createElement('img');
		venueImg.src = venue.venue_id + ".jpg";
		venueImg.classList.add("venueImg"); 
        
        const venueName = document.createElement('h2');
        venueName.textContent = venue.name;
		
        const venueCapacity = document.createElement('p');
        venueCapacity.textContent = `Capacity: ${venue.capacity}`;
        
        const venuePrice = document.createElement('p');
        venuePrice.textContent = `Price (Weekday): £${venue.weekday_price}, Price (Weekend): £${venue.weekend_price}`;
        
        const cateringCost = document.createElement('p');
        cateringCost.textContent = `Catering Cost: £${venue.catering_cost} per person`;
        
        const totalCost = document.createElement('p');
        const totalWeekdayPrice = parseInt(venue.weekday_price) + (parseInt(venue.catering_cost) * parseInt(document.getElementById('partySize').value));
        const totalWeekendPrice = parseInt(venue.weekend_price) + (parseInt(venue.catering_cost) * parseInt(document.getElementById('partySize').value));
        totalCost.textContent = `Total Weekday Cost: £${totalWeekdayPrice}, Total Weekend Cost: £${totalWeekendPrice}`;
        
		function getDate(date_id){
			const date = new Date($(date_id).val());
			
			const year = date.getFullYear();
			const month = (date.getMonth() + 1).toString().padStart(2, "0");
			const day = date.getDate().toString().padStart(2, "0");

			const outputDate = `${year}-${month}-${day}`;
			return outputDate;
		}
		
		const fromDate = getDate('#fromDate');
		const tillDate = getDate('#tillDate');
		
        const viewDetails = document.createElement('button');
		viewDetails.classList.add("btn");
		viewDetails.classList.add("btn-link");
        viewDetails.innerHTML = "View details ->";
        viewDetails.addEventListener('click', () => {
            // Redirect to venue details page with venue ID or other identifier
			const venueId = venue.venue_id;
			const encodedVenueName = encodeURIComponent(venue.name);
			const venueLatitude = encodeURIComponent(venue.latitude);
			const venueLongitude = encodeURIComponent(venue.longitude);
			const encodedVenueCapacity = encodeURIComponent(venue.capacity);
			const encodedWeekdayPrice = encodeURIComponent(venue.weekday_price);
			const encodedWeekendPrice = encodeURIComponent(venue.weekend_price);
			const encodedCateringCost = encodeURIComponent(venue.catering_cost);
			const encodedPartySize = encodeURIComponent(document.getElementById('partySize').value);

			const venueUrl = `venue_details.php?venueId=${venueId}&venueName=${encodedVenueName}&venueLatitude=${venueLatitude}&venueLongitude=${venueLongitude}&venueCapacity=${encodedVenueCapacity}&weekdayPrice=${encodedWeekdayPrice}&weekendPrice=${encodedWeekendPrice}&cateringCost=${encodedCateringCost}&fromDate=${fromDate}&tillDate=${tillDate}&partySize=${encodedPartySize}`;
            window.location.href = venueUrl;
        });
        
		const venueDetails = document.createElement('p');
        // Make AJAX request to retrieve location data
        const ajaxPromise = $.getJSON(`https://findthatpostcode.uk/points/${venue.latitude},${venue.longitude}.json`, function(location) {
            const locationName = location.included[0].attributes.bua22_name;
            const nearestPostcode = location.data.relationships.nearest_postcode.data.id;

            // Update venueDetails.textContent with location data
            venueDetails.textContent = `Location: ${locationName}, ${nearestPostcode}`;
        })
        .fail(function() {
            // Handle AJAX request failure
            venueDetails.textContent = 'Location details unavailable';
        });
        
        
        // Append elements to venueCard
		venueCard.appendChild(venueImg);
        venueCard.appendChild(venueName);
        venueCard.appendChild(venueDetails);
        venueCard.appendChild(venueCapacity);
        venueCard.appendChild(venuePrice);
        venueCard.appendChild(cateringCost);
        venueCard.appendChild(totalCost);
        venueCard.appendChild(viewDetails);
        
        venueCards.push(venueCard);
        
        // Push promise to array
        ajaxPromises.push(ajaxPromise);
    });
    
    // When all AJAX requests are complete, update the UI
    $.when.apply($, ajaxPromises).done(function() {
        // All AJAX requests have completed
        // Now display the venue cards with location details
		venueContainer.removeChild(loadingMessage);
		venueCards.forEach(card => {
                    venueContainer.appendChild(card);
                });
    });
}

</script>

</body>
</html>
