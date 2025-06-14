<?php
session_start();
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');

include 'DB_Connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "doctor") {
    header("Location: Home.html");
    exit();
}

$doctor_id = $_SESSION['user_id'];

$sql_doctor = "SELECT firstName, lastName, emailAddress, SpecialityID, uniqueFileName FROM doctor WHERE id = ?";

$stmt = $conn->prepare($sql_doctor);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

$speciality_name = "";
if ($doctor && isset($doctor['SpecialityID'])) {
    $sql_speciality = "SELECT speciality FROM speciality WHERE id = ?";
    $stmt = $conn->prepare($sql_speciality);
    $stmt->bind_param("i", $doctor['SpecialityID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $speciality = $result->fetch_assoc();
    $speciality_name = $speciality ? $speciality['speciality'] : "";
}
?>

<!-- HTML content starts here -->
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Doctor Page</title>
        <link rel="stylesheet" href="Doctor_Style.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    </head>
    <body>
        <header class="main-header">
            <div class="logo">
                <a href="https://glowria-clinic.infinityfreeapp.com/Glowria-Clinic/Home.html">
    <img src="images/logo.png" alt="Glowria Logo">
</a>
            </div>
            <nav class="navigation">
                <a href="Home.html">Home</a>
                <a href="About.html">About</a>
            </nav>
        </header>

        <div class="wrapper">
            <div class="left" style="width: 350px; height: 420px; background-image: url('images/<?php echo htmlspecialchars($doctor['uniqueFileName']); ?>'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <div class="middle">
                <div class="Welcome">
                    <h3>Welcome Dr. <?php echo htmlspecialchars($doctor['firstName']); ?></h3>
                </div>
                <div class="info">
                    <h3>Information</h3>
                    <div class="info_data">
                        <div class="data"><h4>First Name:</h4><p><?php echo $doctor['firstName']; ?></p></div>
                        <div class="data"><h4>Last Name:</h4><p><?php echo $doctor['lastName']; ?></p></div>
                        <div class="data"><h4>Speciality:</h4><p><?php echo $speciality['speciality']; ?></p></div>
                        <div class="data"><h4>Email:</h4><p><?php echo $doctor['emailAddress']; ?></p></div>
                        <div class="data"><h4>ID:</h4><p><?php echo $doctor_id; ?></p></div>
                        <button class="LogOut" role="button"><a href="Signout.php" class="LogOut2">Sign out</a></button>
                    </div>
                </div>
            </div>
            <div class="right"></div>
        </div>

        <div class="waveWrapper waveAnimation">
            <div class="waveWrapperInner bgTop">
                <div class="wave waveTop" style="background-image: url('http://front-end-noobs.com/jecko/img/wave-top.png')"></div>
            </div>
            <div class="waveWrapperInner bgMiddle">
                <div class="wave waveMiddle" style="background-image: url('http://front-end-noobs.com/jecko/img/wave-mid.png')"></div>
            </div>
            <div class="waveWrapperInner bgBottom">
                <div class="wave waveBottom" style="background-image: url('http://front-end-noobs.com/jecko/img/wave-bot.png')"></div>
            </div>
        </div>

        <h2 id="Upcoming">Upcoming Appointments</h2>
        <div class="table-main">
            <table class="table-area">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient's Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Reason for visit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_appointments = "SELECT appointment.id, appointment.date, appointment.time, appointment.reason, appointment.status,
                patient.firstName, patient.lastName, patient.DoB, patient.gender
            FROM appointment
            JOIN patient ON appointment.PatientID = patient.id
            WHERE appointment.DoctorID = ? AND (appointment.status = 'Pending' OR appointment.status = 'Confirmed')
            ORDER BY appointment.date, appointment.time";
                    $stmt = $conn->prepare($sql_appointments);
                    $stmt->bind_param("i", $doctor_id);
                    $stmt->execute();
                    $appointments_result = $stmt->get_result();
                    while ($row = $appointments_result->fetch_assoc()):
                        ?>
                        <tr id="row-<?php echo $row['id']; ?>">
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo date("g A", strtotime($row['time'])); ?></td>
                            <td><?php echo $row['firstName'] . ' ' . $row['lastName']; ?></td>
                            <td><?php echo date("Y") - date("Y", strtotime($row['DoB'])); ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $row['reason']; ?></td>
                            <td class="status-cell">
                                <?php if ($row['status'] === 'Pending'): ?>
                                    <button class="Confirm confirm-btn" role="button" type="button" data-id="<?php echo $row['id']; ?>">
    <span class="text">Pending&#8987;</span>
    <span>Confirmed &#10004;</span>
</button>

                                    <div class="status-text"></div>
                                <?php else: ?>
                                    <div class="status-text">Confirmed</div>
                                    <div style="margin-top: 5px;">
                                        <a class="Prescribe" href="Prescribe_Medication_Page.php?appointment_id=<?php echo $row['id']; ?>">Prescribe</a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <h2 id="Patients">Your Patients</h2>
        <div class="table-main" id="table2">
            <table class="table-area">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Medications</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_patients = "SELECT DISTINCT patient.firstName, patient.lastName, patient.gender, patient.DoB,
            GROUP_CONCAT(medication.medicationName SEPARATOR ', ') AS medications
            FROM appointment
            JOIN patient ON appointment.PatientID = patient.id
            LEFT JOIN prescription ON appointment.id = prescription.AppointmentID
            LEFT JOIN medication ON prescription.MedicationID = medication.id
            WHERE appointment.DoctorID = ? AND appointment.status = 'Done'
            GROUP BY patient.id";
                    $stmt = $conn->prepare($sql_patients);
                    $stmt->bind_param("i", $doctor_id);
                    $stmt->execute();
                    $patients_result = $stmt->get_result();
                    while ($row = $patients_result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['firstName'] . ' ' . $row['lastName']; ?></td>
                            <td><?php echo date("Y") - date("Y", strtotime($row['DoB'])); ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $row['medications'] ?: 'N/A'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <footer class="footer">
            <div class="footer-content">
                <div class="footer-main">
                    <p>Glowria Clinic • Beauty & Wellness</p>
                </div>
                <div class="footer-links">
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                    <a href="#">Privacy Policy</a>
                </div>
                <div class="social-icons">
                    <a href="https://www.facebook.com" target="_blank"><img src="images/facebook-icon.png" alt="Facebook"></a>
                    <a href="https://www.twitter.com" target="_blank"><img src="images/twitter-icon.png" alt="Twitter"></a>
                    <a href="https://www.instagram.com" target="_blank"><img src="images/instagram-icon.png" alt="Instagram"></a>
                    <a href="mailto:info@glowria.com"><img src="images/email-icon.png" alt="Email"></a>
                </div>
                <p>© 2025 Glowria Clinic. All Rights Reserved.</p>
            </div>
        </footer>

        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="confirm_appointment.js"></script> 
    </body>
</html>