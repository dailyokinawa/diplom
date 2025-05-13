<?php
$date = $_GET['date'] ?? '';

if (!$date) {
    echo json_encode(['bookedTimes' => []]);
    exit;
}

// Подключение к базе данных
$serverName = "localhost"; // Или имя вашего сервера
$database = "serve"; // Имя базы данных

try {
    // Подключение к SQL Server с использованием аутентификации Windows
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", null, null);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => $e->getMessage()]));
}

// SQL-запрос для получения забронированных времён
$sql = "SELECT FORMAT(ReservationTime, 'HH:mm') AS ReservationTime FROM Reservations WHERE ReservationDate = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$date]);

$bookedTimes = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['bookedTimes' => $bookedTimes]);
$conn = null;
?>