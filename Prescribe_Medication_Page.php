<?php
session_start();
include 'DB_Connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "doctor") {
    header("Location: Home.html");
    exit();
}


if (!isset($_GET['appointment_id'])) {
    echo "No appointment selected.";
    exit();
}

$appointment_id = $_GET['appointment_id'];

$sql = "SELECT 
            patient.firstName, 
            patient.lastName, 
            patient.DoB, 
            patient.gender
        FROM appointment
        JOIN patient ON appointment.PatientID = patient.id
        WHERE appointment.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if (!$patient) {
    echo "Patient not found.";
    exit();
}

$birthYear = date("Y", strtotime($patient['DoB']));
$currentYear = date("Y");
$age = $currentYear - $birthYear;

$sql_meds = "SELECT id, medicationName FROM medication";
$result_meds = $conn->query($sql_meds);
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Glowria Clinic</title>
        <link rel="stylesheet" href="Form_Style.css">
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
    </head>
    <body>
        <!-- Header -->
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

        <!-- Page Content for 'Patient's Medications' -->
        <div id="prescribe-container">
            <div class="form-container">
                <h1>Patient's Medications</h1>
                <form method="POST" action="Submit_Prescription.php">
                    <label for="patient-name">Patient's Name:</label>
                    <input type="text" id="patient-name" name="patient-name" value="<?php echo $patient['firstName'] . ' ' . $patient['lastName']; ?>" readonly>
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" value="<?php echo $age; ?>" readonly>

                    <label>Gender:</label>
                    <div class="gender-group">
                        <label><input type="radio" id="male" name="gender" value="male" <?php if ($patient['gender'] == 'Male') echo 'checked'; ?> disabled> Male</label>
                        <label><input type="radio" id="female" name="gender" value="female" <?php if ($patient['gender'] == 'Female') echo 'checked'; ?> disabled> Female</label>

                        <label>Medications:</label>
                        <div class="checkbox-group">
                            <?php while ($med = $result_meds->fetch_assoc()): ?>
                                <label>
                                    <input type="checkbox" name="medications[]" value="<?php echo $med['medicationName']; ?>">
                                    <?php echo $med['medicationName']; ?>
                                </label>
                            <?php endwhile; ?>
                        </div>



                        <input type="hidden" name="appointment_id" value="<?php echo $appointment_id; ?>">
                        <button type="submit">Submit</button>
                </form>
            </div>
        </div>

        <!-- Footer -->
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
                    <a href="https://www.facebook.com" target="_blank">
                        <img src="images/facebook-icon.png" alt="Facebook">
                    </a>
                    <a href="https://www.twitter.com" target="_blank">
                        <img src="images/twitter-icon.png" alt="Twitter">
                    </a>
                    <a href="https://www.instagram.com" target="_blank">
                        <img src="images/instagram-icon.png" alt="Instagram">
                    </a>
                    <a href="mailto:info@glowria.com">
                        <img src="images/email-icon.png" alt="Email">
                    </a>
                </div>
                <p>© 2025 Glowria Clinic. All Rights Reserved.</p>
            </div>
        </footer>
    </body>
</html>