<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Спасибо за заказ! — Drink & Bloom</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="styles.css">
<style>
    /* Видео на весь экран */
    body, html {
        margin: 0;
        padding: 0;
        height: 100%;
        width: 100%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .video-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
    }

    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.35);
        backdrop-filter: blur(6px);
        z-index: 0;
    }

    .content {
        position: relative;
        z-index: 10;
        text-align: center;
        max-width: 600px;
        padding: 2rem;
        background: rgba(0,0,0,0.3);
        backdrop-filter: blur(6px);
        border-radius: 1rem;
    }
</style>
</head>
<body>

<!-- Видео-фон -->
<video autoplay muted loop playsinline class="video-background">
    <source src="https://cdn.pixabay.com/video/2024/11/27/243647_tiny.mp4" type="video/mp4">
    Ваш браузер не поддерживает видео.
</video>

<!-- Полупрозрачный слой -->
<div class="overlay"></div>

<!-- Контент поверх видео -->
<div class="content">
    <h1 class="text-4xl font-bold mb-4 text-white">Спасибо за ваш заказ!</h1>
    <p class="text-lg text-white mb-8">Мы свяжемся с вами в ближайшее время.</p>
    <a href="index.php" class="gold-btn inline-block text-lg">
        Вернуться на главную
    </a>
</div>

</body>
</html>