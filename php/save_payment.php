<?php
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

require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require '../phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $address = $_POST['адрес'] ?? null;
    $apartment = $_POST['№_квартиры_/_офиса'] ?? null;
    $entrance = $_POST['Подъезд'] ?? null;
    $floor = $_POST['Этаж'] ?? null;
    $phone = $_POST['телефон'] ?? null;
    $name = $_POST['имя'] ?? null;
    $message = $_POST['message'] ?? null;
    $email = $_POST['email'] ?? null;
    $paymentMethod = $_POST['payment_method'] ?? null;

    // Проверка обязательных полей
    if (!$address || !$apartment || !$entrance || !$floor || !$phone || !$name || !$email || !$paymentMethod) {
        die("Ошибка: не все обязательные поля заполнены.");
    }

    // SQL-запрос на вставку
    $sql = "INSERT INTO paymentdata (Address, Apartment, Entrance, Floor, Phone, Name, Message, Email, PaymentMethod)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $address, $apartment, $entrance, $floor, $phone,
            $name, $message, $email, $paymentMethod
        ]);

        // Отправка письма пользователю
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPAuth = true;
            $mail->Host = 'smtp.yandex.ru';
            $mail->Username = 'dailyweeknd@yandex.ru';
            $mail->Password = '';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom('dailyweeknd@yandex.ru', 'Lost Heaven');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);
            $mail->Subject = "Ваш заказ принят";
            $mail->Body = "
                <h2>Спасибо за заказ, {$name}!</h2>
                <p><strong>Адрес:</strong> {$address}, кв/офис: {$apartment}</p>
                <p><strong>Подъезд:</strong> {$entrance}, <strong>Этаж:</strong> {$floor}</p>
                <p><strong>Телефон:</strong> {$phone}</p>
                <p><strong>Оплата:</strong> {$paymentMethod}</p>
                <p><strong>Комментарий:</strong> {$message}</p>
                <br>
                <p>Мы свяжемся с вами в ближайшее время.</p>
            ";

            $mail->send();
            echo "Данные успешно сохранены и письмо отправлено.";
        } catch (Exception $e) {
            echo "Данные сохранены, но произошла ошибка при отправке письма: {$mail->ErrorInfo}";
        }
    } catch (PDOException $e) {
        die("Ошибка при сохранении: " . $e->getMessage());
    }
}
?>