<?php
header('Content-Type: application/json');

$host = getenv('MYSQLHOST')     ?: getenv('DB_HOST');
$user = getenv('MYSQLUSER')     ?: getenv('DB_USER');
$pass = getenv('MYSQLPASSWORD') ?: getenv('DB_PASS');
$name = getenv('MYSQLDATABASE') ?: getenv('DB_NAME');
$port = getenv('MYSQLPORT')     ?: 3306;

$conn = new mysqli($host, $user, $pass, $name, (int)$port);
if ($conn->connect_error) {
    echo json_encode(["error" => "DB failed"]);
    exit();
}

$rows   = [];
$result = $conn->query("SELECT * FROM sensor_data ORDER BY id DESC LIMIT 50");
while ($row = $result->fetch_assoc()) $rows[] = $row;

$latest = $conn->query("SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1")
               ->fetch_assoc();
$count  = $conn->query("SELECT COUNT(*) as c FROM sensor_data")
               ->fetch_assoc()['c'];

echo json_encode(["rows" => $rows, "latest" => $latest, "count" => $count]);
$conn->close();
?>