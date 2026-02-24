<?php
session_start();

$correct_hash = '3c9bf9dd7d8ea41320a8fb8c1cdb93421aafe3ed0877a3df462430077483b498';

$max_attempts = 3;
$lockout_time = 300; // 5 минут

$error = '';

if (isset($_SESSION['login_lockout']) && time() - $_SESSION['login_lockout'] < $lockout_time) {
    $error = 'Слишком много попыток. Подождите 5 минут.';
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';

    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    $input_hash = hash('sha256', $password);

    if ($input_hash === $correct_hash) {
        $_SESSION['admin_logged_in'] = true;
        unset($_SESSION['login_attempts']);
        unset($_SESSION['login_lockout']);
        header('Location: admin-dashboard.php');
        exit;
    }

    $_SESSION['login_attempts']++;
    sleep(1);

    if ($_SESSION['login_attempts'] >= $max_attempts) {
        $_SESSION['login_lockout'] = time();
        $error = 'Слишком много попыток. Аккаунт заблокирован на 5 минут.';
    } else {
        $error = 'Неверный пароль. Осталось попыток: ' . ($max_attempts - $_SESSION['login_attempts']);
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Админ</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-gray-900 to-black min-h-screen flex items-center justify-center text-white">

    <div class="bg-gray-800 p-8 rounded-xl shadow-2xl w-full max-w-sm">

        <h1 class="text-3xl font-bold text-center mb-8 text-yellow-400">
            Вход в админ-панель
        </h1>

        <?php if ($error): ?>
        <div class="bg-red-900/60 text-red-200 p-4 rounded-lg mb-6 text-center">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <input
                type="password"
                name="password"
                placeholder="Пароль"
                required
                autocomplete="off"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:border-yellow-500 text-white"
            >
            <button
                type="submit"
                class="w-full py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 rounded-lg font-bold text-lg transition"
            >
                Войти
            </button>
        </form>

    </div>

</body>
</html>