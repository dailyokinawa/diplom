<?php
// Получение данных из формы
$fullName = $_POST['фио'] ?? '';
$phoneNumber = $_POST['номер'] ?? '';
$peopleCount = $_POST['человек'] ?? '';
$reservationDate = $_POST['дата'] ?? '';
$reservationTime = $_POST['время'] ?? '';
$tableNumber = $_POST['столик'] ?? '';

// Проверка формата даты
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $reservationDate)) {
    die('Ошибка: Неверный формат даты.');
}

// Подключение к базе данных
$serverName = "localhost"; // Или имя вашего сервера
$database = "serve"; // Имя базы данных

try {
    // Подключение к SQL Server с использованием аутентификации Windows
    $conn = new PDO("sqlsrv:Server=$serverName;Database=$database", null, null);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

// Проверка на существующее бронирование
$sqlCheck = "SELECT COUNT(*) FROM Reservations WHERE ReservationDate = ? AND ReservationTime = ? AND TableNumber = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->execute([$reservationDate, $reservationTime, $tableNumber]);
$existingReservations = $stmtCheck->fetchColumn();

if ($existingReservations > 0) {
    die('Ошибка: Этот столик уже забронирован на указанное время.');
}

// SQL-запрос для сохранения бронирования
$sql = "INSERT INTO Reservations (FullName, PhoneNumber, PeopleCount, ReservationDate, ReservationTime, TableNumber)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$fullName, $phoneNumber, $peopleCount, $reservationDate, $reservationTime, $tableNumber]);

echo 'Бронирование успешно сохранено!';
$conn = null;
?>