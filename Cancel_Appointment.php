<?php
session_start();
error_reporting(E_ALL);
ini_set('log_errors', '1');
ini_set('display_errors', '1');
include 'DB_Connection.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != "patient") {
        echo json_encode(false);
        exit();
    }

    $appointment_id = $_POST['appointment_id'];
    $patient_id = $_SESSION['user_id'];

    $sql = "DELETE FROM appointment WHERE id = ? AND PatientID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $appointment_id, $patient_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);

    } else {
       echo json_encode(["success" => false]);

    }

    $stmt->close();
    $conn->close();
}
?>