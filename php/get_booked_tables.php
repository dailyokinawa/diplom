<?php
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

if (!$date || !$time) {
    echo json_encode(['bookedTables' => []]);
    exit;
}

// Подключение к базе данных
$host = 'sql308.infinityfree.com';
$db = '';
$user = '';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => $e->getMessage()]));
}

$sql = "SELECT TableNumber FROM reservations WHERE ReservationDate = ? AND ReservationTime = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$date, $time]);
$bookedTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['bookedTables' => $bookedTables]);
$conn = null;
?>