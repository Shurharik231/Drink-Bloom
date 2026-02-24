<!-- admin-add-product.php -->
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productsJson = file_get_contents('products.json');
    $products = json_decode($productsJson, true) ?? [];
    $newId = count($products) + 1;
    $products[] = [
        'id' => $newId,
        'name' => $_POST['name'],
        'price' => (int)$_POST['price'],
        'description' => $_POST['description'],
        'video' => $_POST['video'],
        'images' => array_filter(explode(',', $_POST['images'])) // Удаляем пустые значения
    ];
    file_put_contents('products.json', json_encode($products, JSON_UNESCAPED_UNICODE));
    header('Location: admin-dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить Продукт</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gradient-to-br from-gray-900 to-black text-white pt-20 container mx-auto px-6 py-20">
    <h1 class="text-4xl font-bold mb-8">Добавить Новый Продукт</h1>
    <form method="post">
        <input type="text" name="name" placeholder="Название" class="w-full p-4 mb-4 bg-gray-700 rounded" required>
        <input type="number" name="price" placeholder="Цена" class="w-full p-4 mb-4 bg-gray-700 rounded" required>
        <textarea name="description" placeholder="Описание" class="w-full p-4 mb-4 bg-gray-700 rounded" required></textarea>
        <input type="text" name="video" placeholder="URL видео (если есть)" class="w-full p-4 mb-4 bg-gray-700 rounded">
        <input type="text" name="images" placeholder="URL фото через запятую (если нет видео)" class="w-full p-4 mb-4 bg-gray-700 rounded">
        <button type="submit" class="gold-btn">Добавить</button>
    </form>
</body>
</html>