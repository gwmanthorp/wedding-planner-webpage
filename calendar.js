let currentYear, currentMonth;
let unavailableDateArr = [];
let betweenDatesArray = [];
const monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"];
  

document.addEventListener('DOMContentLoaded', function() {	
	//betweenDatesArray.document.getElementById("fromDate").innerHTML);
	const startDate = new Date(document.querySelector("#fromDate").innerHTML);
	const endDate = new Date(document.querySelector("#tillDate").innerHTML);
	
	
	function getDates(startDate, endDate){
		const dates = [];
		const currentDate = startDate;
		while (currentDate < endDate) {
			dates.push(new Date(currentDate).toISOString().slice(0, 10));
			currentDate.setDate(currentDate.getDate() + 1);
		}
		dates.push(endDate.toISOString().slice(0, 10));
		return dates;
	};
	
	betweenDatesArray = getDates(startDate, endDate);

	let currentDate = new Date(document.querySelector("#fromDate").innerHTML);
    currentYear = currentDate.getFullYear();
    currentMonth = currentDate.getMonth();
	
	const formData = new FormData();
    formData.append('venueId', 1);

    fetch('get_dates.php', {
        method: 'POST',
        body: formData
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
            unavailableDateArr = data;
			console.log(unavailableDateArr);
            generateCalendar();
        } else {
            console.log('No booking dates found');
        }
    })
    .catch(error => {
        console.error('Error fetching booking dates:', error);
    });
});

function generateCalendar(){	
	const calendarDays = document.querySelector('.days');
	calendarDays.innerHTML = '';
	
    // Get first day and last day of the current month
    const firstDayOfMonth = new Date(currentYear, currentMonth, 0);
    const lastDayOfMonth = new Date(currentYear, currentMonth + 1, 0);

    // Determine the number of days in the month
    const daysInMonth = lastDayOfMonth.getDate();

    // Determine the index of the first day of the month (0 = Sunday, 1 = Monday, etc.)
    const firstDayOfWeek = firstDayOfMonth.getDay(); // 0-based index
	
	document.getElementById("month-year").innerHTML = monthNames[currentMonth] + " " + parseInt(currentYear);
	
	for (let i = 0; i < firstDayOfWeek; i++) {
        // Add empty placeholders for days before the first day of the month
        const emptyDay = document.createElement('div');
        emptyDay.classList.add('empty');
        calendarDays.appendChild(emptyDay);
    }

    for (let day = 1; day <= daysInMonth; day++) {
        const dayElement = document.createElement('div');
        dayElement.textContent = day;

		const dateString = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
		if (unavailableDateArr.includes(dateString)){
			dayElement.style.backgroundColor = "red";
			dayElement.style.color = "white";
			
		} else if (betweenDatesArray.includes(dateString)){
			dayElement.style.backgroundColor = "green";
			dayElement.style.color = "white";
		}
		

		calendarDays.appendChild(dayElement);
    }
}

function nextMonth(){
	generateCalendar();
	if (currentMonth == 11){
		currentMonth = 0;
		currentYear += 1;
	} else{
		currentMonth += 1;
	}
	generateCalendar();
}

function previousMonth(){
	if (currentMonth == 0){
		currentMonth = 11;
		currentYear -= 1;
	} else{
		currentMonth -= 1;
	}
	generateCalendar();
}