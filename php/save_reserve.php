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
$host = 'sql308.infinityfree.com';
$db = '';
$user = '';
$pass = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

// Проверка на существующее бронирование
$sqlCheck = "SELECT COUNT(*) FROM reservations WHERE ReservationDate = ? AND ReservationTime = ? AND TableNumber = ?";
$stmtCheck = $conn->prepare($sqlCheck);
$stmtCheck->execute([$reservationDate, $reservationTime, $tableNumber]);
$existingReservations = $stmtCheck->fetchColumn();

if ($existingReservations > 0) {
    die('Ошибка: Этот столик уже забронирован на указанное время.');
}

// Вставка данных
$sql = "INSERT INTO reservations (FullName, PhoneNumber, PeopleCount, ReservationDate, ReservationTime, TableNumber)
        VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->execute([$fullName, $phoneNumber, $peopleCount, $reservationDate, $reservationTime, $tableNumber]);

// Отправка email через PHPMailer
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require '../phpmailer/Exception.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP();
$mail->CharSet = "UTF-8";
$mail->SMTPAuth = true;

$mail->Host = 'smtp.yandex.ru';
$mail->Username = 'dailyweeknd@yandex.ru';
$mail->Password = '';
$mail->SMTPSecure = 'ssl';
$mail->Port = 465;
$mail->setFrom('dailyweeknd@yandex.ru', 'Lost Heaven');
$mail->addAddress('dailyweeknd@yandex.ru');

$mail->isHTML(true);
$mail->Subject = "Новое бронирование столика";
$mail->Body = "
<h2>Информация о бронировании</h2>
<p><strong>ФИО:</strong> {$fullName}</p>
<p><strong>Номер телефона:</strong> {$phoneNumber}</p>
<p><strong>Количество человек:</strong> {$peopleCount}</p>
<p><strong>Дата:</strong> {$reservationDate}</p>
<p><strong>Время:</strong> {$reservationTime}</p>
<p><strong>Столик:</strong> {$tableNumber}</p>
";

$mail->send();

echo 'Бронирование успешно сохранено!';
$conn = null;
?>