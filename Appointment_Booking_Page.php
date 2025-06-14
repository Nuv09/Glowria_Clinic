<?php
session_start();
include 'DB_Connection.php';

error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "patient") {
    header("Location: Log_in_Page.php");
    exit();
}

$specialities = $conn->query("SELECT * FROM speciality");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book an Appointment</title>
    <link rel="stylesheet" href="Form_Style.css">
</head>
<body>
<header class="main-header">
    <div class="logo">
        <a href="https://glowria-clinic.infinityfreeapp.com/Glowria-Clinic/Home.html">
    <img src="images/logo.png" alt="Glowria Logo">
</a>
    </div>
</header>

<div id="appointment-container">
    <div class="form-container">
        <h1>Book an Appointment</h1>

        <!-- First: Select Speciality -->
        <label for="speciality">Select Speciality:</label>
        <select id="speciality" name="speciality" required>
            <option value="">-- Select --</option>
            <?php while ($row = $specialities->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= $row['speciality'] ?></option>
            <?php endwhile; ?>
        </select>

        <!-- Second: Book Appointment -->
        <form id="bookingForm" method="POST" action="Add_Appointment.php" style="display:none;">
            <label for="doctor">Select Doctor:</label>
            <select id="doctor" name="doctor_id" required>
                <option value="">-- Select Doctor --</option>
            </select>

            <label>Date:</label>
            <input type="date" name="date" required>

            <label>Time:</label>
            <input type="time" name="time" required>

            <label>Reason for Visit:</label>
            <textarea name="reason" required></textarea>

            <button type="submit">Submit Booking</button>
        </form>
    </div>
</div>

<!-- AJAX Script -->
<script>
document.getElementById("speciality").addEventListener("change", function () {
    var specialityId = this.value;

    if (!specialityId) return;

    fetch("get_doctors.php?speciality_id=" + specialityId)
        .then(response => response.json())
        .then(data => {
            var doctorSelect = document.getElementById("doctor");
            doctorSelect.innerHTML = '<option value="">-- Select Doctor --</option>';

            if (data.length > 0) {
                data.forEach(doctor => {
                    var option = document.createElement("option");
                    option.value = doctor.id;
                    option.text = "Dr. " + doctor.firstName + " " + doctor.lastName;
                    doctorSelect.appendChild(option);
                });
                document.getElementById("bookingForm").style.display = "block";
            } else {
                document.getElementById("bookingForm").style.display = "none";
            }
        })
        .catch(error => {
            console.error("Error fetching doctors:", error);
        });
});
</script>

</body>
</html>