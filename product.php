<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Читаем продукты
$productsJson = @file_get_contents('products.json');
$products = json_decode($productsJson, true) ?? [];

// Читаем скидки
$discountsJson = @file_get_contents('discounts.json');
$discounts = json_decode($discountsJson, true) ?? [];

// Находим продукт
$product = null;
foreach ($products as $p) {
    if (isset($p['id']) && $p['id'] == $id) {
        $product = $p;
        break;
    }
}

// Если продукт не найден
if (!$product) {
    $product = [
        'id' => 0,
        'name' => 'Товар не найден',
        'price' => 0,
        'description' => 'К сожалению, такого товара сейчас нет в каталоге.',
        'video' => '',
        'images' => []
    ];
}

// Проверяем скидку
$discount = isset($discounts[$product['id']]) ? (float)$discounts[$product['id']] : 0;
$discountedPrice = $product['price'];
if ($discount > 0) {
    $discountedPrice = round($product['price'] * (1 - $discount / 100));
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($product['name']) ?> — Drink & Bloom</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="styles.css">
<style>
body, html { margin:0; padding:0; height:100%; width:100%; overflow-x:hidden; }
.video-background { position:fixed; top:0; left:0; width:100%; height:100%; object-fit:cover; z-index:-1; }
.overlay { position:fixed; inset:0; background: rgba(0,0,0,0.3); backdrop-filter: blur(6px); z-index:0; }
main, header, footer { position: relative; z-index: 10; }
.product-description { max-height:300px; overflow-y:auto; padding-right:5px; color:#fff; }
.product-description::-webkit-scrollbar { width:6px; }
.product-description::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); border-radius:10px; backdrop-filter: blur(4px);}
.product-description::-webkit-scrollbar-thumb { background: rgba(212,175,55,0.4); border-radius:10px; backdrop-filter: blur(2px);}
.product-description::-webkit-scrollbar-thumb:hover { background: rgba(212,175,55,0.7); box-shadow:0 0 8px rgba(212,175,55,0.6);}
.media-container { position: relative; width: 100%; border-radius: 1rem; overflow: hidden; border: 1px solid rgba(212,175,55,0.2);}
.product-video, .carousel img { width:100%; height:100%; object-fit:cover; display:block; transition: transform 0.5s ease;}
.product-video:hover, .carousel img:hover { transform: scale(1.05); }

/* Премиальный лейбл скидки — как в люксовых магазинах */
.premium-discount {
    position: absolute;
    top: 1.5rem;
    left: 1.5rem;
    background: rgba(15, 15, 15, 0.75);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    color: #d4af37;
    font-family: 'Playfair Display', serif;
    font-weight: 600;
    font-size: 1.35rem;
    padding: 0.6rem 1.4rem;
    border-radius: 9999px;
    border: 1px solid rgba(212, 175, 55, 0.45);
    box-shadow: 0 8px 32px rgba(0,0,0,0.6),
                inset 0 1px 4px rgba(255,255,255,0.12);
    z-index: 30;
    letter-spacing: 0.08em;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.media-container:hover .premium-discount {
    transform: translateY(-6px) scale(1.05);
    box-shadow: 0 16px 48px rgba(212, 175, 55, 0.3);
    border-color: #d4af37;
}

/* Модальное окно */
#orderModal { position: fixed; inset:0; display:none; align-items:center; justify-content:center; z-index:100; background: rgba(0,0,0,0.65); backdrop-filter:blur(8px);}
#orderModal .modal-content { background: rgba(15,15,15,0.92); backdrop-filter:blur(10px); border-radius:1.75rem; padding:2rem; max-width:420px; width:92%; position:relative; border:1px solid rgba(212,175,55,0.18);}
#orderModal .closeModal { position:absolute; top:1rem; right:1.25rem; font-size:2.25rem; color:#d4af37; cursor:pointer; transition:0.3s;}
#orderModal .closeModal:hover { color:#fff; transform:rotate(90deg); }
input, textarea { width:100%; margin-bottom:1rem; padding:0.9rem 1.25rem; border-radius:1rem; background:rgba(255,255,255,0.04); color:#fff; border:1px solid rgba(212,175,55,0.25); font-size:1.05rem;}
input:focus, textarea:focus { outline:none; border-color:#d4af37; box-shadow:0 0 0 3px rgba(212,175,55,0.15); }
.gold-btn { background: linear-gradient(135deg, #d4af37 0%, #b8972e 100%); color:#0f0f0f; padding:1rem 2rem; border-radius:9999px; font-weight:700; font-size:1.15rem; display:inline-flex; align-items:center; justify-content:center; transition:0.35s; width:100%; letter-spacing:0.5px;}
.gold-btn:hover { background: linear-gradient(135deg, #e5c068 0%, #d4af37 100%); transform:translateY(-3px); box-shadow:0 15px 35px rgba(212,175,55,0.35); }
</style>
</head>
<body class="text-white">

<video autoplay muted loop playsinline class="video-background">
    <source src="/DrinkBloom/image/product.mp4" type="video/mp4">
</video>
<div class="overlay"></div>

<header class="fixed top-0 left-0 w-full bg-black bg-opacity-90 backdrop-blur-xl z-50 border-b border-gold/10">
<nav class="container mx-auto px-6 py-5 flex justify-between items-center">
<a href="index.php" class="text-3xl font-bold text-gold tracking-wider">Drink & Bloom</a>
<ul class="flex space-x-8 text-lg">
<li><a href="catalog.php" class="hover:text-gold transition duration-300">Каталог</a></li>
<li><a href="about.php" class="hover:text-gold transition duration-300">О нас</a></li>
</ul>
</nav>
</header>

<main class="pt-28 md:pt-32 container mx-auto px-6 md:px-10 lg:px-16 py-16 md:py-24">
<a href="catalog.php" class="inline-flex items-center text-gold hover:text-gold-light mb-10 transition duration-300 text-lg">
<i class="fas fa-arrow-left mr-3"></i> Вернуться в каталог
</a>

<div class="grid md:grid-cols-2 gap-12 lg:gap-16 items-start">
    <div class="media-container">
        <?php if ($discount > 0): ?>
            <div class="premium-discount">-<?= $discount ?>%</div>
        <?php endif; ?>
        <?php if (!empty($product['video'])): ?>
        <video autoplay muted loop playsinline preload="metadata" class="product-video" loading="lazy">
            <source src="<?= htmlspecialchars($product['video']); ?>" type="video/mp4">
        </video>
        <?php elseif (!empty($product['images'])): ?>
        <div class="carousel">
            <?php foreach ($product['images'] as $index => $img): ?>
            <img src="<?= htmlspecialchars($img); ?>" alt="<?= htmlspecialchars($product['name']) ?> фото <?= $index+1 ?>" class="<?= $index===0?'active':'' ?>" loading="lazy">
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="placeholder">Скоро добавим</div>
        <?php endif; ?>
    </div>

    <div class="flex flex-col justify-center bg-black/30 backdrop-blur-md rounded-xl p-8 border border-gold/20">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight tracking-tight text-white">
            <?= htmlspecialchars($product['name']) ?>
        </h1>

        <div class="flex items-center mb-8 relative">
            <span class="text-4xl md:text-5xl font-bold text-gold">
                <?= number_format($discountedPrice,0,'',' ') ?> ₽
            </span>
            <?php if ($discount > 0): ?>
                <span class="text-white/50 line-through ml-3 text-lg">
                    <?= number_format($product['price'],0,'',' ') ?> ₽
                </span>
            <?php endif; ?>
        </div>

        <p class="text-xl leading-relaxed opacity-90 mb-10 product-description">
            <?= nl2br(htmlspecialchars($product['description'])) ?>
        </p>
        <button id="openOrderModal" class="gold-btn text-xl px-12 py-6 w-full md:w-auto">
            <i class="fas fa-shopping-cart mr-4 text-2xl"></i> Заказать сейчас
        </button>
    </div>
</div>
</main>

<footer class="py-16 bg-black/90 text-center border-t border-gold/10 mt-24">
<p class="text-lg">&copy; 2026 Drink & Bloom. Все права защищены.</p>
</footer>

<!-- Модальное окно -->
<div id="orderModal" class="flex">
    <div class="modal-content">
        <span class="closeModal">×</span>

        <!-- Шаг 1: форма заказа -->
        <div id="stepForm">
            <h2 class="text-2xl font-bold text-gold mb-4">Оформление заказа</h2>
            <p class="text-white mb-2">Товар: <strong><?= htmlspecialchars($product['name']) ?></strong></p>
            <p class="text-white mb-4">Сумма: <strong><?= number_format($discountedPrice,0,'',' ') ?> ₽</strong></p>
            <form id="orderForm">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <label class="text-white block mb-1">Имя и фамилия</label>
                <input type="text" name="name" required>
                <label class="text-white block mb-1">Телефон</label>
                <input type="text" name="phone" required>
                <label class="text-white block mb-1">Адрес доставки</label>
                <textarea name="address" rows="2" required></textarea>
                <label class="text-white block mb-1">Комментарий (по желанию)</label>
                <textarea name="comment" rows="2"></textarea>
                <button type="submit" class="gold-btn mt-2">Продолжить к оплате</button>
            </form>
        </div>

        <!-- Шаг 2: оплата -->
        <div id="stepPay" class="hidden text-center">
            <h2 class="text-2xl font-bold text-gold mb-4">Оплата заказа</h2>
            <p class="text-white mb-4">Сумма: <strong><?= number_format($discountedPrice,0,'',' ') ?> ₽</strong></p>
            <p class="text-white mb-4">Переведите сумму на карту: <strong>1234 5678 9012 3456</strong></p>
            <p class="mt-2 text-sm text-white/70">После перевода вернитесь на сайт для подтверждения заказа.</p>
            <button id="backStep" class="gold-btn mt-4">Назад</button>
        </div>
    </div>
</div>

<script>
// Карусель
document.querySelectorAll('.carousel').forEach(carousel => {
    const images = carousel.querySelectorAll('img');
    if(images.length<=1) return;
    let current = 0;
    setInterval(()=>{
        images[current].classList.remove('active');
        current = (current+1)%images.length;
        images[current].classList.add('active');
    },4000);
});

// Модальное окно
const modal = document.getElementById('orderModal');
const openBtn = document.getElementById('openOrderModal');
const closeBtn = modal.querySelector('.closeModal');
const stepForm = document.getElementById('stepForm');
const stepPay = document.getElementById('stepPay');
const backBtn = document.getElementById('backStep');

openBtn.onclick = () => modal.style.display='flex';
closeBtn.onclick = () => {
    modal.style.display='none';
    stepForm.classList.remove('hidden');
    stepPay.classList.add('hidden');
};
backBtn.onclick = () => {
    stepForm.classList.remove('hidden');
    stepPay.classList.add('hidden');
};

// Обработка формы
document.getElementById('orderForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const response = await fetch('order.php', {method:'POST', body:formData});
    const data = await response.text();
    stepForm.classList.add('hidden');
    stepPay.classList.remove('hidden');
});
</script>

</body>
</html>