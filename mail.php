<?php
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';
require 'phpmailer/Exception.php';

$host = 'sql308.infinityfree.com';
$db = '';
$user = '';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('Ошибка подключения: ' . $conn->connect_error);
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (!empty($email)) {
        // Подготовка запроса на добавление email в MySQL
        $query = "INSERT INTO subscriptions (Email) VALUES (?)";
        $stmt = $conn->prepare($query);

        if ($stmt === false) {
            die("Ошибка подготовки запроса: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        if (!$stmt->execute()) {
            die("Ошибка выполнения запроса: " . $stmt->error);
        }
        $stmt->close();

        // Отправка письма
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
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Благодарим за подписку!";
            $mail->Body = "<h2>Спасибо за подписку на рассылку Lost Heaven!</h2>";

            $mail->send();
            echo "Подписка успешно оформлена!";
        } catch (Exception $e) {
            echo "Ошибка при отправке письма: {$mail->ErrorInfo}";
        }
    } else {
        echo "Ошибка: Email не указан.";
    }
}
?>