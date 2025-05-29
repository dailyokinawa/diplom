<?php
session_start();

// Данные для входа
$admin_user = "";
$admin_pass = "";

// Обработка формы входа
if (isset($_POST['login']) && isset($_POST['password'])) {
    if ($_POST['login'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin_auth'] = true;
    }
}

// Обработка выхода
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_auth']);
    header("Location: index.php");
    exit;
}

// Подключение к базе данных
function db_connect() {
    $host = 'sql308.infinityfree.com';
    $db = '';
    $user = '';
    $pass = '';
    
    try {
        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES utf8mb4");
        $conn->exec("SET CHARACTER SET utf8mb4");
        return $conn;
    } catch (PDOException $e) {
        return false;
    }
}

// Получение данных бронирований
function get_reservations() {
    $conn = db_connect();
    if (!$conn) return [];
    
    try {
        $stmt = $conn->query("SELECT * FROM reservations ORDER BY ReservationDate DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Получение данных платежей
function get_payments() {
    $conn = db_connect();
    if (!$conn) return [];
    
    try {
        $stmt = $conn->query("SELECT * FROM paymentdata ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Получение данных подписок
function get_subscriptions() {
    $conn = db_connect();
    if (!$conn) return [];
    
    try {
        $stmt = $conn->query("SELECT * FROM subscriptions ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

// Определение текущего раздела
$section = isset($_GET['section']) ? $_GET['section'] : 'reservations';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        /* Стили для фона админ-панели */
        body.admin-logged-in {
            background-image: url('../img/preview/bgnorway.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            padding: 30px;
            margin: 0;
        }
        
        body.admin-logged-in::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }
        
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .login-form button {
            width: 100%;
            padding: 10px;
            background: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            color: white;
        }
        .admin-nav {
            margin-bottom: 20px;
        }
        .admin-nav a {
            display: inline-block;
            padding: 10px 15px;
            margin-right: 5px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        .admin-nav a.active {
            background: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            border-radius: 5px;
            overflow: hidden;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }
        th {
            background-color: rgba(242, 242, 242, 0.9);
            font-weight: bold;
        }
        tr:hover {
            background-color: #f9f9f9;
        }
        .logout {
            padding: 10px 15px;
            background: #f44336;
            color: white;
            text-decoration: none;
            border-radius: 3px;
        }
        h2 {
            color: white;
            margin-bottom: 20px;
        }
        .no-data {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body<?php echo isset($_SESSION['admin_auth']) ? ' class="admin-logged-in"' : ''; ?>>
    <?php if (!isset($_SESSION['admin_auth'])): ?>
        <!-- Форма входа -->
        <div class="login-form">
            <h2>Вход в админ-панель</h2>
            <form method="post" action="">
                <input type="text" name="login" placeholder="Логин" required>
                <input type="password" name="password" placeholder="Пароль" required>
                <button type="submit">Войти</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Админ-панель -->
        <div class="admin-header">
            <h1>Админ-панель</h1>
            <a href="?logout=1" class="logout">Выйти</a>
        </div>
        
        <div class="admin-nav">
            <a href="?section=reservations" class="<?php echo $section === 'reservations' ? 'active' : ''; ?>">Бронирования</a>
            <a href="?section=payments" class="<?php echo $section === 'payments' ? 'active' : ''; ?>">Платежи</a>
            <a href="?section=subscriptions" class="<?php echo $section === 'subscriptions' ? 'active' : ''; ?>">Подписки</a>
        </div>
        
        <?php if ($section === 'reservations'): ?>
            <h2>Бронирования столиков</h2>
            <?php
            $reservations = get_reservations();
            if (empty($reservations)):
            ?>
                <div class="no-data">Нет данных о бронированиях</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($reservations[0]) as $key): ?>
                                <th><?php echo htmlspecialchars($key); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php elseif ($section === 'payments'): ?>
            <h2>Платежи</h2>
            <?php
            $payments = get_payments();
            if (empty($payments)):
            ?>
                <div class="no-data">Нет данных о платежах</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($payments[0]) as $key): ?>
                                <th><?php echo htmlspecialchars($key); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php elseif ($section === 'subscriptions'): ?>
            <h2>Подписки</h2>
            <?php
            $subscriptions = get_subscriptions();
            if (empty($subscriptions)):
            ?>
                <div class="no-data">Нет данных о подписках</div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($subscriptions[0]) as $key): ?>
                                <th><?php echo htmlspecialchars($key); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($subscriptions as $row): ?>
                            <tr>
                                <?php foreach ($row as $value): ?>
                                    <td><?php echo htmlspecialchars($value); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>