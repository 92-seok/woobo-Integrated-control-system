<?php
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

// Check if required data is provided
if (!isset($_POST['cd_dist_obsv']) || !isset($_POST['gate_status'])) {
    die("Required data not provided.");
}

// Database connection information
$servername = "192.168.80.80";
$username = "WBEarly";
$password = "#woobosys@early!";
$dbname = "warning";
$port = 3306;

// Get data from the POST request
$cdDistObsv = $_POST['cd_dist_obsv'];
$gateStatus = $_POST['gate_status'];

// MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to insert data
$sql = "INSERT INTO wb_gatecontrol (GCtrCode, CD_DIST_OBSV, RegDate, Gate, GStatus) VALUES (NULL, ?, now(), ?, 'start' )";

// Prepare the statement
$stmt = $conn->prepare($sql);

// Bind parameters and execute
$stmt->bind_param("ss", $cdDistObsv, $gateStatus);

if ($stmt->execute()) {
    echo "데이터 삽입 성공!";
} else {
    echo "실행 결과 : 오류: " . $stmt->error;
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>