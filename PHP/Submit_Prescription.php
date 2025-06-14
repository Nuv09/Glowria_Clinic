<?php
session_start();
include 'DB_Connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "doctor") {
    header("Location: Home.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $medications = $_POST['medications'] ?? [];

    foreach ($medications as $medicationName) {
        $stmt = $conn->prepare("SELECT id FROM medication WHERE medicationName = ?");
        $stmt->bind_param("s", $medicationName);
        $stmt->execute();
        $result = $stmt->get_result();
        $med = $result->fetch_assoc();

        if ($med) {
            $med_id = $med['id'];

            $stmt = $conn->prepare("INSERT INTO prescription (AppointmentID, MedicationID) VALUES (?, ?)");
            $stmt->bind_param("ii", $appointment_id, $med_id);
            $stmt->execute();
        }
    }

    $stmt = $conn->prepare("UPDATE appointment SET status = 'Done' WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    header("Location: Doctor_Page.php");
    exit();
}
?>