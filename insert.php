<?php
// Allow connection from ESP32 on local network
header("Access-Control-Allow-Origin: *");

// Database credentials
$host     = "localhost";
$user     = "root";
$password = "";          // XAMPP default has no password
$dbname   = "greenhouse_db";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read values sent by ESP32 via GET parameters
$temperature = isset($_GET['temperature']) ? floatval($_GET['temperature']) : null;
$humidity    = isset($_GET['humidity'])    ? floatval($_GET['humidity'])    : null;
$fan_status  = isset($_GET['fan_status'])  ? $_GET['fan_status']            : null;

// Validate — all three must be present
if ($temperature === null || $humidity === null || $fan_status === null) {
    http_response_code(400);
    echo "Error: Missing parameters.";
    exit();
}

// Sanitize fan_status — only allow "ON" or "OFF"
$fan_status = ($fan_status === "ON") ? "ON" : "OFF";

// Insert into database (time_stamp fills itself automatically)
$sql = "INSERT INTO sensor_data (temperature, humidity, fan_status)
        VALUES (?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("dds", $temperature, $humidity, $fan_status);

if ($stmt->execute()) {
    echo "OK";   // ESP32 checks for this 200 OK response
} else {
    http_response_code(500);
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>