<?php


include 'DB_Connection.php';

if (!isset($_GET['speciality_id'])) {
    echo json_encode([]);
    exit();
}

$speciality_id = intval($_GET['speciality_id']);

$stmt = $conn->prepare("SELECT id, firstName, lastName FROM doctor WHERE SpecialityID = ?");
$stmt->bind_param("i", $speciality_id);
$stmt->execute();
$result = $stmt->get_result();

$doctors = [];
while ($row = $result->fetch_assoc()) {
    $doctors[] = $row;
}

header('Content-Type: application/json');
echo json_encode($doctors);


?>