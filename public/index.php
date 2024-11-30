<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Статичні значення
    $box_id = 28;
    $offer_id = 5;
    $countryCode = 'GB';
    $language = 'en';
    $password = 'qwerty12';
    $ip = $_SERVER['REMOTE_ADDR'];
    $landingUrl = $_SERVER['HTTP_HOST'];

    // Валідація номера телефону
    if (!preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
        $message = "Error: Invalid phone number format.";
    } else {
        // Підготовка даних для API-запиту
        $data = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'phone' => $phone,
            'email' => $email,
            'countryCode' => $countryCode,
            'box_id' => $box_id,
            'offer_id' => $offer_id,
            'landingUrl' => $landingUrl,
            'ip' => $ip,
            'password' => $password,
            'language' => $language,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://crm.belmar.pro/api/v1/addlead');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'token: ba67df6a-a17c-476f-8e95-bcdb75ed3958',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);
        curl_close($ch);

        // Обробка відповіді від API
        $response_data = json_decode($response, true);
        if (isset($response_data['status']) && $response_data['status'] === true) {
            $message = "Lead successfully added: " . $response_data['id'] . " (" . $response_data['email'] . ")";
        } else {
            $message = "Error: " . ($response_data['error'] ?? 'Unknown error');
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Lead</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Add Lead</h1>
    <form method="POST">
        <label for="firstName">First Name:</label><br>
        <input type="text" name="firstName" required><br><br>

        <label for="lastName">Last Name:</label><br>
        <input type="text" name="lastName" required><br><br>

        <label for="phone">Phone:</label><br>
        <input type="text" name="phone" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" name="email" required><br><br>

        <button type="submit">Add Lead</button>
    </form>

    <?php if (isset($message)): ?>
        <p><?= $message ?></p>
    <?php endif; ?>

    <br><br>
    <a href="statuses.php">Go to Lead Statuses</a>
</body>
</html>
