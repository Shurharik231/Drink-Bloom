<!-- admin-edit-product.php -->
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

// Получаем ID продукта
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Загружаем продукты
$productsJson = file_get_contents('products.json');
$products = json_decode($productsJson, true) ?? [];

// Находим ключ продукта
$key = array_search($id, array_column($products, 'id'));
if ($key === false) {
    header('Location: admin-dashboard.php');
    exit;
}
$product = $products[$key];

// Функция для формирования абсолютного URL
function getAbsoluteUrl($relativePath) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $dir = dirname($_SERVER['SCRIPT_NAME']); // папка скрипта
    $dir = rtrim($dir, '/');
    return "$protocol://$host$dir/$relativePath";
}

// Обработка POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mediaDir = 'image/'; // папка для всех медиа
    if (!is_dir($mediaDir)) mkdir($mediaDir, 0777, true);

    // Видео
    $videoPath = $_POST['video'] ?? '';
    if (!empty($_FILES['video_file']['name']) && $_FILES['video_file']['error'] === 0) {
        $fileName = time() . '_video_' . basename($_FILES['video_file']['name']);
        $targetFile = $mediaDir . $fileName;
        if (move_uploaded_file($_FILES['video_file']['tmp_name'], $targetFile)) {
            $videoPath = getAbsoluteUrl($targetFile);
        }
    } else if ($videoPath) {
        // если введён URL, оставляем как есть
        $videoPath = $videoPath;
    }

    // Фото
    $images = array_filter(explode(',', $_POST['images'] ?? []));
    $uploadedImages = [];
    if (!empty($_FILES['images_files']['name'][0])) {
        foreach ($_FILES['images_files']['name'] as $index => $name) {
            if ($_FILES['images_files']['error'][$index] === 0) {
                $fileName = time() . '_img_' . basename($name);
                $targetFile = $mediaDir . $fileName;
                if (move_uploaded_file($_FILES['images_files']['tmp_name'][$index], $targetFile)) {
                    $uploadedImages[] = getAbsoluteUrl($targetFile);
                }
            }
        }
    }

    // Объединяем URL из формы и загруженные
    $images = array_merge($images, $uploadedImages);

    // Сохраняем продукт
    $products[$key] = [
        'id' => $id,
        'name' => $_POST['name'],
        'price' => (int)$_POST['price'],
        'description' => $_POST['description'],
        'video' => $videoPath,
        'images' => $images
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
<title>Редактировать Продукт</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
</head>
<body class="bg-gradient-to-br from-gray-900 to-black text-white pt-20 container mx-auto px-6 py-20">
<h1 class="text-4xl font-bold mb-8">Редактировать Продукт</h1>

<form method="post" enctype="multipart/form-data">
    <label class="mb-1 block">Название:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" class="w-full p-4 mb-4 bg-gray-700 rounded" required>

    <label class="mb-1 block">Цена:</label>
    <input type="number" name="price" value="<?php echo $product['price']; ?>" class="w-full p-4 mb-4 bg-gray-700 rounded" required>

    <label class="mb-1 block">Описание:</label>
    <textarea name="description" class="w-full p-4 mb-4 bg-gray-700 rounded" required><?php echo htmlspecialchars($product['description']); ?></textarea>

    <!-- Видео -->
    <label class="mb-1 block">Видео (URL или файл):</label>
    <input type="text" name="video" value="<?php echo htmlspecialchars($product['video'] ?? ''); ?>" class="w-full p-4 mb-2 bg-gray-700 rounded" placeholder="URL видео">
    <input type="file" name="video_file" class="w-full p-2 mb-4 bg-gray-700 rounded" accept="video/*">

    <?php if (!empty($product['video'])): ?>
        <video controls class="w-full mb-4 rounded">
            <source src="<?php echo htmlspecialchars($product['video']); ?>" type="video/mp4">
            Ваш браузер не поддерживает видео.
        </video>
    <?php endif; ?>

    <!-- Изображения -->
    <label class="mb-1 block">Изображения (URL через запятую или загрузка файлов):</label>
    <input type="text" name="images" value="<?php echo htmlspecialchars(implode(',', $product['images'] ?? [])); ?>" class="w-full p-4 mb-2 bg-gray-700 rounded" placeholder="URL фото через запятую">
    <input type="file" name="images_files[]" class="w-full p-2 mb-4 bg-gray-700 rounded" accept="image/*" multiple>

    <?php if (!empty($product['images'])): ?>
        <div class="grid grid-cols-3 gap-4 mb-4">
            <?php foreach ($product['images'] as $img): ?>
                <img src="<?php echo htmlspecialchars($img); ?>" class="w-full rounded">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <button type="submit" class="gold-btn px-6 py-3 rounded bg-yellow-500 text-black font-bold hover:bg-yellow-400">Сохранить</button>
</form>
</body>
</html>