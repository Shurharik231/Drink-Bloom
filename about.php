<!DOCTYPE html>
<html lang="ru" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>О нас — Drink & Bloom</title>

    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
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
        }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--bg-dark);
            color: var(--text-light);
            line-height: 1.8;
            margin: 0;
            overflow-x: hidden;
        }

        h1, h2, .section-title {
            font-family: 'Playfair Display', serif;
            letter-spacing: -0.03em;
        }

        header {
            background: rgba(10,10,10,0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(212,175,55,0.08);
        }

        .hero {
            min-height: 80vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 6rem;
            background: linear-gradient(to bottom, rgba(10,10,10,0.4), rgba(10,10,10,0.85));
        }

        .hero h1 {
            font-size: clamp(4rem, 10vw, 9rem);
            font-weight: 700;
            line-height: 1;
            background: linear-gradient(to right, var(--gold-light), var(--gold));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .section {
            padding: 8rem 0;
        }

        .content-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .content-text {
            font-size: 1.25rem;
            color: var(--text-muted);
            line-height: 1.9;
            margin-bottom: 2.5rem;
        }

        .content-text strong {
            color: white;
            font-weight: 500;
        }

        .gold-accent {
            color: var(--gold);
            font-weight: 600;
        }

        .divider {
            width: 120px;
            height: 2px;
            background: linear-gradient(to right, transparent, var(--gold), transparent);
            margin: 4rem auto;
        }

        footer {
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(16px);
            border-top: 1px solid rgba(212,175,55,0.08);
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: clamp(3.5rem, 12vw, 6rem); }
            .section { padding: 6rem 0; }
        }
    </style>
</head>
<body>

<!-- Видео-фон (можно оставить, если есть подходящее спокойное видео) -->
<!-- <video autoplay muted loop playsinline class="video-background">
    <source src="/DrinkBloom/image/about-bg.mp4" type="video/mp4">
</video> -->

<header class="fixed top-0 left-0 w-full z-50">
    <nav class="container mx-auto px-6 lg:px-12 py-6 flex justify-between items-center">
        <a href="index.php" class="text-3xl lg:text-4xl font-bold tracking-wider text-white">
            Drink & Bloom
        </a>
        <div class="space-x-10 lg:space-x-12 text-lg">
            <a href="index.php" class="hover:text-[var(--gold)] transition-colors">Главная</a>
            <a href="catalog.php" class="hover:text-[var(--gold)] transition-colors">Каталог</a>
            <a href="#contact" class="hover:text-[var(--gold)] transition-colors">Контакты</a>
        </div>
    </nav>
</header>

<div class="hero">
    <div class="max-w-5xl">
        <h1 data-aos="fade-down" data-aos-duration="1400">О нас</h1>
        <p class="text-2xl md:text-3xl text-[var(--text-muted)] max-w-3xl mx-auto mt-6 leading-relaxed" data-aos="fade-up" data-aos-delay="400">
            Искусство дарить эмоции
        </p>
    </div>
</div>

<div class="section bg-gradient-to-b from-[var(--bg-dark)] to-black/95">
    <div class="content-container">
        <div class="prose prose-xl prose-invert max-w-none">
            <p class="content-text" data-aos="fade-up">
                Drink & Bloom — это не просто магазин подарков. Это философия, воплощённая в каждом наборе: <span class="gold-accent">роскошь должна быть простой, а внимание — ощутимым</span>.
            </p>

            <p class="content-text" data-aos="fade-up" data-aos-delay="200">
                Мы верим, что настоящий подарок — это не вещь, а <strong>эмоция</strong>, которую он вызывает. Поэтому каждый наш набор — это тщательно продуманная история: элитный алкоголь, свежайшие цветы, собранные вручную, и безупречная подача.
            </p>

            <div class="divider" data-aos="zoom-in" data-aos-delay="400"></div>

            <p class="content-text" data-aos="fade-up" data-aos-delay="600">
                Наша аудитория — мужчины 35–55 лет, которые ценят статус и качество, женщины 25–45 лет, ищущие утончённые эмоции, и корпоративные клиенты, для которых важны <span class="gold-accent">впечатление и репутация</span>.
            </p>

            <p class="content-text" data-aos="fade-up" data-aos-delay="800">
                Мы не гонимся за количеством. Мы выбираем только лучшее: премиальный алкоголь, цветы высшего сорта и упаковку, которая сама по себе является произведением искусства. Потому что ваш подарок должен говорить о вас громче любых слов.
            </p>

            <p class="content-text font-medium text-2xl text-center mt-16 italic text-[var(--gold)]" data-aos="fade-up" data-aos-delay="1000">
                Drink & Bloom — когда важен не просто жест, а то, что за ним стоит.
            </p>
        </div>
    </div>
</div>

<footer class="py-20 lg:py-24 text-center text-[var(--text-muted)] border-t border-white/5">
    <p class="text-xl">&copy; 2026 Drink & Bloom. Создано с вниманием к деталям.</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({
        duration: 1200,
        once: true,
        easing: 'ease-out'
    });
</script>

</body>
</html>