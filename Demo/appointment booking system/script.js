// JavaScript to populate doctor names based on department
function populateDoctors(department) {
    const doctorSelect = document.getElementById('doctor_name');
    doctorSelect.innerHTML = ''; // Clear existing options

    const doctors = {
        "Child and Adolescent Development Clinic": [
            "Dr. Farzana Ahmed", "Dr. Rafiq Rahman", "Dr. Ayesha Khan", "Dr. Nasim Hossain", "Dr. Saiful Islam"
        ],
        "Psychosexual Disorder Clinic": [
            "Dr. Tanvir Alam", "Dr. Nabila Hasan", "Dr. Shahinur Rahman", "Dr. Rezaul Karim", "Dr. Sumaiya Sultana"
        ],
        "Addiction Clinic": [
            "Dr. Moinul Haque", "Dr. Asma Parveen", "Dr. Firoz Mahmud", "Dr. Sabrina Khan", "Dr. Arif Hossain"
        ],
        "Psychotherapy Clinic": [
            "Dr. Sadia Afrin", "Dr. Imran Chowdhury", "Dr. Tahmina Rahman", "Dr. Mahbub Alam", "Dr. Nusrat Jahan"
        ],
        
    };

    if (department in doctors) {
        doctors[department].forEach(doctor => {
            const option = document.createElement('option');
            option.value = doctor;
            option.text = doctor;
            doctorSelect.add(option);
        });
    }
}

