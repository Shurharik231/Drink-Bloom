<?php
$productsJson = file_get_contents('products.json');
$products = json_decode($productsJson, true) ?? [];
$discountsJson = file_get_contents('discounts.json');
$discounts = json_decode($discountsJson, true) ?? [];
?>

<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Каталог — Drink & Bloom</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #d4af37;
            --gold-light: #e5c068;
            --gold-dark: #b8972e;
            --bg-dark: #0a0a0a;
            --text-light: #f8f8f8;
            --text-muted: #c0c0c0;
            --glass: rgba(15,15,15,0.65);
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-dark);
            color: var(--text-light);
            margin: 0;
            overflow-x: hidden;
        }

        h1, h2, .product-title {
            font-family: 'Playfair Display', serif;
            letter-spacing: -0.02em;
        }

        .video-background {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
            filter: brightness(0.55) contrast(1.05) saturate(0.92);
        }

        .overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(to bottom, rgba(10,10,10,0.55), rgba(10,10,10,0.95) 60%);
            backdrop-filter: blur(8px) saturate(0.9);
            z-index: -1;
        }

        header {
            background: rgba(10,10,10,0.82);
            backdrop-filter: blur(20px) saturate(0.9);
            border-bottom: 1px solid rgba(212,175,55,0.3);
            box-shadow: 0 6px 25px rgba(0,0,0,0.6);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 2.5rem;
            padding: 2rem 0;
        }

        .product-card {
            background: var(--glass);
            backdrop-filter: blur(12px) saturate(0.9);
            border-radius: 1.5rem;
            overflow: hidden;
            border: 1px solid rgba(212,175,55,0.25);
            box-shadow: 0 12px 40px rgba(0,0,0,0.65);
            transition: all 0.45s ease;
            display: flex;
            flex-direction: column;
            height: 520px; /* фиксированная высота — все карточки ровные */
        }

        .product-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 30px 80px rgba(212,175,55,0.3), 0 0 0 1px rgba(212,175,55,0.45);
            border-color: var(--gold);
        }

        .media-container {
            height: 320px;
            flex-shrink: 0;
            position: relative;
            overflow: hidden;
        }

        .media-container video,
        .media-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s ease, filter 0.7s ease;
        }

        .product-card:hover .media-container video,
        .product-card:hover .media-container img {
            transform: scale(1.08);
            filter: brightness(1.05) contrast(1.08);
        }

        .premium-discount {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            background: rgba(15,15,15,0.82);
            backdrop-filter: blur(16px);
            color: var(--gold);
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: 1.35rem;
            padding: 0.6rem 1.4rem;
            border-radius: 9999px;
            border: 1px solid rgba(212,175,55,0.45);
            box-shadow: 0 8px 32px rgba(0,0,0,0.6),
                        inset 0 1px 4px rgba(255,255,255,0.1);
            z-index: 20;
            letter-spacing: 0.06em;
            transition: all 0.5s ease;
        }

        .product-card:hover .premium-discount {
            transform: translateY(-5px) scale(1.06);
            box-shadow: 0 16px 48px rgba(212,175,55,0.3);
            border-color: var(--gold);
        }

        .product-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.5rem;
        }

        .product-title {
            font-size: 1.75rem;
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 0.75rem;
            color: white;
        }

        .product-description {
            flex-grow: 1;
            font-size: 1rem;
            color: var(--text-muted);
            line-height: 1.65;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 5;
            -webkit-box-orient: vertical;
            margin-bottom: 1rem;
        }

        .product-card:hover .product-description {
            overflow-y: auto;
            max-height: 180px;
        }

        .price-block {
            margin-top: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .price-main {
            font-size: 1.85rem;
            font-weight: 700;
            color: var(--gold);
            letter-spacing: -0.02em;
        }

        .price-old {
            font-size: 1.25rem;
            color: rgba(255,255,255,0.4);
            text-decoration: line-through;
            margin-left: 1rem;
        }

        .gold-btn {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
            color: #0f0f0f;
            font-weight: 600;
            padding: 0.9rem 2rem;
            border-radius: 9999px;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            box-shadow: 0 8px 25px rgba(212,175,55,0.25);
            letter-spacing: 0.04em;
            width: 100%;
            text-align: center;
        }

        .gold-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(212,175,55,0.4);
            background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 100%);
        }

        footer {
            background: rgba(0,0,0,0.8);
            backdrop-filter: blur(12px);
            border-top: 1px solid rgba(212,175,55,0.08);
            padding: 4rem 0 2rem;
            text-align: center;
        }
    </style>
</head>
<body>

<video autoplay muted loop playsinline preload="metadata" class="video-background">
    <source src="/DrinkBloom/image/catalog.mp4" type="video/mp4">
</video>

<div class="overlay"></div>

<header class="fixed top-0 left-0 w-full z-50">
    <nav class="container mx-auto px-6 lg:px-12 py-6 flex justify-between items-center">
        <a href="index.php" class="text-3xl lg:text-4xl font-bold tracking-wider text-white">
            Drink & Bloom
        </a>
        <div class="space-x-10 lg:space-x-12 text-lg">
            <a href="catalog.php" class="hover:text-[var(--gold)] transition-colors">Каталог</a>
            <a href="about.php" class="hover:text-[var(--gold)] transition-colors">О нас</a>
        </div>
    </nav>
</header>

<main class="pt-32 lg:pt-40 pb-24 lg:pb-32 container mx-auto px-6 lg:px-12">
    <h1 class="text-5xl lg:text-7xl font-bold text-center mb-16 lg:mb-24 bg-clip-text text-transparent bg-gradient-to-r from-[var(--gold-light)] to-[var(--gold)]">
        Наш каталог
    </h1>

    <div class="product-grid">
        <?php foreach ($products as $product):
            $disc = $discounts[(string)$product['id']] ?? 0;
            $finalPrice = $disc > 0 ? round($product['price'] * (1 - $disc / 100)) : $product['price'];
        ?>
        <div class="product-card group">
            <div class="media-container">
                <?php if ($disc > 0): ?>
                    <div class="premium-discount">-<?= $disc ?>%</div>
                <?php endif; ?>

                <?php if (!empty($product['video'])): ?>
                    <video autoplay muted loop playsinline preload="metadata" class="product-video">
                        <source src="<?= htmlspecialchars($product['video']) ?>" type="video/mp4">
                    </video>
                <?php elseif (!empty($product['images'])): ?>
                    <img src="<?= htmlspecialchars($product['images'][0] ?? '') ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         class="transition-transform duration-700 group-hover:scale-105">
                <?php else: ?>
                    <div class="w-full h-full bg-gradient-to-br from-gray-900 to-black flex items-center justify-center text-gray-600 text-2xl">
                        Изображение
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-info">
                <h2 class="product-title"><?= htmlspecialchars($product['name']) ?></h2>
                <p class="product-description"><?= htmlspecialchars($product['description'] ?? 'Описание отсутствует') ?></p>

                <div class="price-block">
                    <span class="price-main"><?= number_format($finalPrice, 0, ' ', ' ') ?> ₽</span>
                    <?php if ($disc > 0): ?>
                        <span class="price-old"><?= number_format($product['price'], 0, ' ', ' ') ?> ₽</span>
                    <?php endif; ?>
                </div>

                <a href="product.php?id=<?= $product['id'] ?>" class="gold-btn mt-4">
                    <i class="fas fa-eye mr-3"></i> Подробнее
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<footer class="py-16 lg:py-20 text-center text-[var(--text-muted)] border-t border-white/5">
    <p class="text-lg">&copy; 2026 Drink & Bloom. Все права защищены.</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true, easing: 'ease-out' });
</script>

</body>
</html>