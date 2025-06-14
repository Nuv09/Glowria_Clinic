<?php
session_start();
include 'DB_Connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "patient") {
    header("Location: Log_in_Page.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $patient_id = $_SESSION['user_id'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];

    $stmt = $conn->prepare("INSERT INTO appointment (DoctorID, PatientID, date, time, reason, status) 
                                VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iisss", $doctor_id, $patient_id, $date, $time, $reason);
    $stmt->execute();

    header("Location: Patient_Page.php?msg=appointment_booked");
    exit();
}
?>