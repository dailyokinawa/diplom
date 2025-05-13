<?php
$date = $_GET['date'] ?? '';
$time = $_GET['time'] ?? '';

if (!$date || !$time) {
    echo json_encode(['bookedTables' => []]);
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

// SQL-запрос для получения забронированных столиков
$sql = "SELECT TableNumber FROM Reservations WHERE ReservationDate = ? AND ReservationTime = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$date, $time]);
$bookedTables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode(['bookedTables' => $bookedTables]);
$conn = null;
?>