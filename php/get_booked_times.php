<?php
$date = $_GET['date'] ?? '';

if (!$date) {
    echo json_encode(['bookedTimes' => [], 'fullyBookedTimes' => []]);
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

// Получаем времена, на которые все столики забронированы (всего 6 столиков)
$sqlFullyBooked = "SELECT ReservationTime 
                  FROM reservations 
                  WHERE ReservationDate = ? 
                  GROUP BY ReservationTime 
                  HAVING COUNT(DISTINCT TableNumber) >= 6";
$stmtFullyBooked = $conn->prepare($sqlFullyBooked);
$stmtFullyBooked->execute([$date]);
$fullyBookedTimes = $stmtFullyBooked->fetchAll(PDO::FETCH_COLUMN);

// Получаем информацию о забронированных столиках для каждого времени
$sqlBookedTablesCount = "SELECT ReservationTime, COUNT(DISTINCT TableNumber) as bookedCount 
                        FROM reservations 
                        WHERE ReservationDate = ? 
                        GROUP BY ReservationTime";
$stmtBookedTablesCount = $conn->prepare($sqlBookedTablesCount);
$stmtBookedTablesCount->execute([$date]);
$bookedTablesCount = [];
while ($row = $stmtBookedTablesCount->fetch(PDO::FETCH_ASSOC)) {
    $bookedTablesCount[$row['ReservationTime']] = $row['bookedCount'];
}

echo json_encode([
    'fullyBookedTimes' => $fullyBookedTimes,
    'bookedTablesCount' => $bookedTablesCount
]);
$conn = null;
?>