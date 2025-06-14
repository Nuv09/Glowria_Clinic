<?php
$servername = "sql304.infinityfree.com"; 
$username = "if0_38802563";         
$password = "GlowriaClinic1";        
$dbname = "if0_38802563_glowria_clinic_database"; 


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>