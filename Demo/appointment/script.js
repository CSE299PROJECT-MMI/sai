function populateDoctors(department) {
    const doctorSelect = document.getElementById('doctor_name');
    doctorSelect.innerHTML = ''; // Clear existing options

    // Create the default "Select Doctor" option
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.text = 'Select Doctor';
    doctorSelect.add(defaultOption);

    if (department !== "") {
        // Create an AJAX request to fetch doctors for the selected department
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'appointment.php?department=' + encodeURIComponent(department), true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                const doctors = JSON.parse(xhr.responseText); // Parse the response as a JSON array

                // Check if any doctors were found
                if (doctors.length > 0) {
                    doctors.forEach(function(doctor) {
                        const option = document.createElement('option');
                        option.value = doctor;
                        option.text = doctor;
                        doctorSelect.add(option);
                    });
                } else {
                    // Option when no doctors are available for the department
                    const option = document.createElement('option');
                    option.value = '';
                    option.text = 'No doctors available';
                    doctorSelect.add(option);
                }
            }
        };
        xhr.send();
    }
}
