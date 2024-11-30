<?php
// Отримуємо значення дати з фільтра
$date_from = isset($_POST['date_from']) ? $_POST['date_from'] : date('Y-m-d', strtotime('-30 days')) . ' 00:00:00';
$date_to = isset($_POST['date_to']) ? $_POST['date_to'] : date('Y-m-d') . ' 23:59:59';

// Отримуємо поточну сторінку для пагінації
$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$limit = 100; // Ліміт на одну сторінку

// Дані для API-запиту
$data = [
    'date_from' => $date_from,
    'date_to' => $date_to,
    'page' => $page,
    'limit' => $limit
];

// Виконання запиту до API для отримання статусів лідів
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://crm.belmar.pro/api/v1/getstatuses');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'token: ba67df6a-a17c-476f-8e95-bcdb75ed3958',
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

// Обробка відповіді
$response_data = json_decode($response, true);
$statuses = isset($response_data['data']) ? $response_data['data'] : [];
$total_count = isset($response_data['limit']) ? (int)$response_data['limit'] : 0;
$total_pages = ceil($total_count / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Statuses</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Lead Statuses</h1>

    <form method="POST">
        <label for="date_from">Date From:</label>
        <input type="date" name="date_from" value="<?= date('Y-m-d', strtotime($date_from)) ?>"><br><br>

        <label for="date_to">Date To:</label>
        <input type="date" name="date_to" value="<?= date('Y-m-d', strtotime($date_to)) ?>"><br><br>

        <button type="submit">Filter</button>
    </form>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Status</th>
                <th>FTD</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($statuses as $status): ?>
                <tr>
                    <td><?= $status['id'] ?></td>
                    <td><?= $status['email'] ?></td>
                    <td><?= !empty($status['status']) ? $status['status'] : 'N/A' ?></td>
                    <td><?= $status['ftd'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Пагінація -->
    <div>
        <p>Page: <?= $page + 1 ?> of <?= $total_pages ?></p>
        <nav>
            <ul>
                <?php if ($page > 0): ?>
                    <li><a href="?page=<?= $page - 1 ?>&date_from=<?= date('Y-m-d', strtotime($date_from)) ?>&date_to=<?= date('Y-m-d', strtotime($date_to)) ?>">Previous</a></li>
                <?php endif; ?>

                <?php if ($page < $total_pages - 1): ?>
                    <li><a href="?page=<?= $page + 1 ?>&date_from=<?= date('Y-m-d', strtotime($date_from)) ?>&date_to=<?= date('Y-m-d', strtotime($date_to)) ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <br><br>
    <a href="index.php">Go to Add Lead</a>
</body>
</html>