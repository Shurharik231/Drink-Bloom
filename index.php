<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Drink & Bloom - Премиальные Подарки 2 в 1</title>

<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
/* Фон */
body {
    background: url('/DrinkBloom/image/main.jpeg') no-repeat center center fixed;
    background-size: cover;
    color: #e0e0e0;
    font-family: 'Inter', sans-serif;
}

/* Header */
header {
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(6px); /* уменьшено для производительности */
    border-bottom: 1px solid rgba(212,175,55,0.15);
}

/* Hero */
.hero {
    position: relative;
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}
.hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(6px);
}
.hero-content {
    position: relative;
    z-index: 10;
    text-align: center;
}

/* Заголовки */
h1, h2, .product-title {
    text-shadow: 0 1px 4px rgba(0,0,0,0.6);
}

/* Карточки */
.product-card {
    background: rgba(0,0,0,0.35);
    backdrop-filter: blur(4px);
    border-radius: 1rem;
    overflow: hidden;
    border: 1px solid rgba(212,175,55,0.15);
    box-shadow: 0 4px 18px rgba(0,0,0,0.35);
    transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 28px rgba(0,0,0,0.55);
    border-color: rgba(212,175,55,0.35);
}

/* Кнопки */
.gold-btn {
    background: linear-gradient(135deg, #d4af37 0%, #b8972e 100%);
    color: #0f0f0f;
    padding: 0.5rem 1.5rem;
    border-radius: 50px;
    box-shadow: 0 4px 10px rgba(212,175,55,0.3);
    transition: all 0.25s ease;
    display: inline-flex;
    align-items: center;
    font-size: 0.9rem;
}
.gold-btn i { margin-right: 0.5rem; }
.gold-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(212,175,55,0.5);
    background: linear-gradient(135deg, #e5c068 0%, #d4af37 100%);
}

/* Секции */
section { position: relative; z-index: 5; }
.container { max-width: 1200px; margin: 0 auto; }

/* Footer */
footer {
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(6px);
    border-top: 1px solid rgba(212,175,55,0.15);
    padding: 4rem 0;
    text-align: center;
}

/* Адаптивность */
@media (max-width: 768px) {
    .hero h1 { font-size: 2.5rem; }
    .hero p { font-size: 1.1rem; }
    .grid-cols-3 { grid-template-columns: 1fr; }
}
</style>
</head>
<body class="font-sans antialiased">

<header class="fixed top-0 left-0 w-full z-50">
    <nav class="container mx-auto px-6 py-5 flex justify-between items-center">
        <a href="index.php" class="text-3xl font-bold text-gold tracking-wider">Drink & Bloom</a>
        <ul class="flex space-x-8 text-lg">
            <li><a href="catalog.php" class="hover:text-gold transition duration-300">Каталог</a></li>
            <li><a href="about.php" class="hover:text-gold transition duration-300">О нас</a></li>
            <li><a href="#contact" class="hover:text-gold transition duration-300">Контакты</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="hero">
        <div class="hero-content px-4 max-w-5xl" data-aos="fade-up" data-aos-duration="1500">
            <h1 class="text-5xl sm:text-6xl md:text-7xl font-bold mb-6 leading-tight tracking-tight text-white">
                Премиальные Подарки:<br>Вино и Цветы в Одном
            </h1>
            <p class="text-xl md:text-2xl mb-10 font-light opacity-90">
                Изысканный симбиоз элитного алкоголя и свежих цветов
            </p>
            <a href="catalog.php" class="gold-btn inline-flex items-center gap-3">
                <i class="fas fa-wine-glass-alt text-xl"></i>
                Просмотреть Коллекцию
            </a>
        </div>
    </section>

    <section class="py-20 md:py-32 container px-6">
        <h2 class="text-4xl md:text-5xl font-bold text-center mb-16" data-aos="fade-in">Наши Преимущества</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
            <div class="product-card p-8" data-aos="zoom-in">
                <h3 class="text-3xl font-bold mb-6 text-gold">Уникальный Дизайн</h3>
                <p class="text-lg opacity-90">Каждый букет создан вручную с премиальными материалами и вниманием к деталям.</p>
            </div>
            <div class="product-card p-8" data-aos="zoom-in" data-aos-delay="200">
                <h3 class="text-3xl font-bold mb-6 text-gold">Долговечность</h3>
                <p class="text-lg opacity-90">Цветы остаются свежими 5–7 дней благодаря специальной флористической губке.</p>
            </div>
            <div class="product-card p-8" data-aos="zoom-in" data-aos-delay="400">
                <h3 class="text-3xl font-bold mb-6 text-gold">Персонализация</h3>
                <p class="text-lg opacity-90">Выберите свою бутылку или доверьтесь нам — создадим идеальный подарок именно для вас.</p>
            </div>
        </div>
    </section>
</main>

<footer id="contact">
    <p class="text-lg">&copy; 2026 Drink & Bloom. Все права защищены.</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
AOS.init({ duration: 1200, once: true });
</script>
</body>
</html>