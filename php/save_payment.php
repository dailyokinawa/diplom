<?php
require 'basketdb.php'; // Подключение к базе данных

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
    $paymentMethod = $_POST['payment_method'] ?? null; // Получаем выбранный метод оплаты

    // Проверка на заполненность обязательных полей
    if (!$address || !$apartment || !$entrance || !$floor || !$phone || !$name || !$email || !$paymentMethod) {
        die("Ошибка: не все обязательные поля заполнены.");
    }

    // SQL-запрос для вставки данных
    $sql = "INSERT INTO PaymentData (Address, Apartment, Entrance, Floor, Phone, Name, Message, Email, PaymentMethod)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [$address, $apartment, $entrance, $floor, $phone, $name, $message, $email, $paymentMethod];

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    echo "Данные успешно сохранены!";
}
?>