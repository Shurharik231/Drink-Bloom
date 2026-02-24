<?php

session_start();

// Если не авторизован → на страницу входа
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: admin-login.php');
    exit;
}

// Выход из системы
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php'); // или admin-login.php — как удобнее
    exit;
}

// Файлы данных
$products_file  = 'products.json';
$orders_file    = 'orders.json';
$discounts_file = 'discounts.json';

// Чтение данных (с защитой от ошибок)
$products  = json_decode(@file_get_contents($products_file), true)  ?? [];
$orders    = json_decode(@file_get_contents($orders_file), true)    ?? [];
$discounts = json_decode(@file_get_contents($discounts_file), true) ?? [];

// Карта товаров по ID для быстрого доступа
$productsById = [];
foreach ($products as $p) {
    $id = (int)($p['id'] ?? 0);
    if ($id > 0) {
        $productsById[$id] = $p;
    }
}

/* ──────────────── Обработка действий с заказами ──────────────── */
if (isset($_GET['confirm_order'], $_GET['index'])) {
    $i = (int)$_GET['index'];
    if (isset($orders[$i])) {
        $orders[$i]['status'] = 'Подтверждён';
        file_put_contents($orders_file, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    header("Location: admin-dashboard.php#orders");
    exit;
}

if (isset($_GET['delete_order'], $_GET['index'])) {
    $i = (int)$_GET['index'];
    if (isset($orders[$i])) {
        unset($orders[$i]);
        $orders = array_values($orders); // переиндексация
        file_put_contents($orders_file, json_encode($orders, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    header("Location: admin-dashboard.php#orders");
    exit;
}

/* ──────────────── Управление скидками ──────────────── */
if (isset($_POST['save_discount'])) {
    $pid     = trim($_POST['product_id'] ?? '');
    $percent = (int)($_POST['percent'] ?? 0);

    $percent = max(0, min(100, $percent)); // 0–100

    if ($percent === 0) {
        unset($discounts[$pid]);
    } else {
        $discounts[$pid] = $percent;
    }

    file_put_contents($discounts_file, json_encode($discounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: admin-dashboard.php#discounts");
    exit;
}

if (isset($_GET['delete_discount'], $_GET['pid'])) {
    $pid = trim($_GET['pid']);
    unset($discounts[$pid]);
    file_put_contents($discounts_file, json_encode($discounts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    header("Location: admin-dashboard.php#discounts");
    exit;
}

/* ──────────────── Подсчёт статистики ──────────────── */
$totalIncome   = 0;
$todayIncome   = 0;
$ordersPerDay  = [];
$topProducts   = [];
$orderStatuses = [];

$currentDate = date('Y-m-d');

foreach ($orders as $o) {
    $pid   = (int)($o['product_id'] ?? 0);
    $base  = $productsById[$pid]['price'] ?? 0;
    $discP = $discounts[(string)$pid] ?? 0;
    $final = max(0, $base * (1 - $discP / 100));

    $totalIncome += $final;

    $date = date('Y-m-d', strtotime($o['date'] ?? 'now'));
    if (!isset($ordersPerDay[$date])) {
        $ordersPerDay[$date] = ['orders' => 0, 'income' => 0];
    }
    $ordersPerDay[$date]['orders']++;
    $ordersPerDay[$date]['income'] += $final;

    if ($date === $currentDate) {
        $todayIncome += $final;
    }

    if (!isset($topProducts[$pid])) {
        $topProducts[$pid] = [
            'count' => 0,
            'income' => 0,
            'name' => $productsById[$pid]['name'] ?? '—'
        ];
    }
    $topProducts[$pid]['count']++;
    $topProducts[$pid]['income'] += $final;

    $st = $o['status'] ?? 'Новый';
    $orderStatuses[$st] = ($orderStatuses[$st] ?? 0) + 1;
}

ksort($ordersPerDay);
arsort($topProducts);
$topProducts = array_slice($topProducts, 0, 5, true);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ — Drink & Bloom</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        .gold-btn {
            background: linear-gradient(135deg, #facc15, #eab308);
        }
        .gold-btn:hover {
            background: linear-gradient(135deg, #fde047, #facc15);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-900 via-black to-gray-950 text-white min-h-screen"
      x-data="{
          activeTab: 'dashboard',
          discountModal: false,
          editingPid: '',
          editingPercent: 0,
          openDiscountModal(pid = '', percent = 0) {
              this.editingPid = pid;
              this.editingPercent = percent;
              this.discountModal = true;
          }
      }">

<!-- Шапка -->
<header class="fixed top-0 left-0 right-0 bg-black/90 backdrop-blur-md z-50 border-b border-gray-800/50">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <span class="text-3xl font-extrabold text-yellow-400 tracking-tight">Админ Drink & Bloom</span>
        <a href="?logout=1" class="text-lg hover:text-yellow-400 transition">Выход</a>
    </div>
</header>

<main class="pt-28 container mx-auto px-6 pb-24">

    <h1 class="text-5xl md:text-6xl font-black text-center mb-16 bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 to-yellow-600" data-aos="fade-down">
        АДМИН-ПАНЕЛЬ
    </h1>

    <!-- Вкладки -->
    <div class="flex justify-center flex-wrap gap-3 mb-14">
        <button @click="activeTab = 'dashboard'"   :class="{ 'gold-btn shadow-2xl shadow-yellow-500/30': activeTab === 'dashboard',   'bg-gray-800 hover:bg-gray-700': activeTab !== 'dashboard' }"   class="px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300">Дашборд</button>
        <button @click="activeTab = 'orders'"      :class="{ 'gold-btn shadow-2xl shadow-yellow-500/30': activeTab === 'orders',      'bg-gray-800 hover:bg-gray-700': activeTab !== 'orders' }"      class="px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300">Заказы</button>
        <button @click="activeTab = 'products'"    :class="{ 'gold-btn shadow-2xl shadow-yellow-500/30': activeTab === 'products',    'bg-gray-800 hover:bg-gray-700': activeTab !== 'products' }"    class="px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300">Товары</button>
        <button @click="activeTab = 'discounts'"   :class="{ 'gold-btn shadow-2xl shadow-yellow-500/30': activeTab === 'discounts',   'bg-gray-800 hover:bg-gray-700': activeTab !== 'discounts' }"   class="px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300">Скидки</button>
        <button @click="activeTab = 'analytics'"   :class="{ 'gold-btn shadow-2xl shadow-yellow-500/30': activeTab === 'analytics',   'bg-gray-800 hover:bg-gray-700': activeTab !== 'analytics' }"   class="px-8 py-4 rounded-xl font-bold text-lg transition-all duration-300">Аналитика</button>
    </div>

    <!-- Дашборд -->
    <div x-show="activeTab === 'dashboard'" x-transition.origin.top data-aos="fade-up">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-8 rounded-2xl border border-gray-700/50 text-center shadow-xl">
                <h3 class="text-xl text-gray-400 mb-3">Общий доход</h3>
                <p class="text-4xl font-black text-yellow-400"><?= number_format($totalIncome, 0, ' ', ' ') ?> ₽</p>
            </div>
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-8 rounded-2xl border border-gray-700/50 text-center shadow-xl">
                <h3 class="text-xl text-gray-400 mb-3">Сегодня (<?= $currentDate ?>)</h3>
                <p class="text-4xl font-black text-yellow-400"><?= number_format($todayIncome, 0, ' ', ' ') ?> ₽</p>
            </div>
            <div class="bg-gradient-to-br from-gray-800 to-gray-900 p-8 rounded-2xl border border-gray-700/50 text-center shadow-xl">
                <h3 class="text-xl text-gray-400 mb-3">Заказов всего</h3>
                <p class="text-4xl font-black text-yellow-400"><?= count($orders) ?></p>
            </div>
        </div>

        <div class="mb-16">
            <h2 class="text-3xl font-bold mb-6 text-center">Доход по дням</h2>
            <div class="bg-gray-900/40 p-6 rounded-2xl border border-gray-800">
                <canvas id="incomeChart" height="140"></canvas>
            </div>
        </div>
    </div>

    <!-- Заказы -->
    <div x-show="activeTab === 'orders'" id="orders" x-transition.origin.top data-aos="fade-up">
        <h2 class="text-4xl font-black mb-10 text-center bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 to-yellow-600">Заказы</h2>

        <?php if (empty($orders)): ?>
            <div class="text-center text-gray-400 text-xl py-12">Пока нет заказов</div>
        <?php else: ?>
        <div class="overflow-x-auto bg-gray-900/40 rounded-2xl border border-gray-800 shadow-2xl">
            <table class="w-full min-w-max">
                <thead class="bg-gray-800/80">
                    <tr>
                        <th class="p-5 text-left">Клиент</th>
                        <th class="p-5 text-left">Телефон</th>
                        <th class="p-5 text-left">Товар</th>
                        <th class="p-5 text-right">Цена</th>
                        <th class="p-5 text-left">Дата</th>
                        <th class="p-5 text-left">Статус</th>
                        <th class="p-5 text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $i => $o):
                    $pid = (int)($o['product_id'] ?? 0);
                    $product = $productsById[$pid] ?? null;
                    $disc = $discounts[(string)$pid] ?? 0;
                ?>
                    <tr class="border-t border-gray-800 hover:bg-gray-800/60 transition-colors">
                        <td class="p-5"><?= htmlspecialchars($o['name']   ?? '—') ?></td>
                        <td class="p-5"><?= htmlspecialchars($o['phone']  ?? '—') ?></td>
                        <td class="p-5">
                            <?php if ($product): ?>
                                <?= htmlspecialchars($product['name']) ?>
                                <?php if ($disc > 0): ?>
                                    <span class="ml-2 text-xs bg-green-600/40 text-green-300 px-2 py-1 rounded-full">−<?= $disc ?>%</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-red-400">Товар удалён</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-5 text-right font-medium">
                            <?php if ($product): ?>
                                <?php if ($disc > 0): ?>
                                    <span class="line-through text-gray-500 mr-2"><?= number_format($product['price'], 0, ' ', ' ') ?> ₽</span>
                                    <span class="text-green-400"><?= number_format($product['price'] * (1 - $disc/100), 0, ' ', ' ') ?> ₽</span>
                                <?php else: ?>
                                    <?= number_format($product['price'], 0, ' ', ' ') ?> ₽
                                <?php endif; ?>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td class="p-5"><?= htmlspecialchars($o['date'] ?? '—') ?></td>
                        <td class="p-5">
                            <span class="<?= ($o['status'] ?? '') === 'Подтверждён' ? 'text-green-400' : 'text-yellow-400' ?> font-medium">
                                <?= htmlspecialchars($o['status'] ?? 'Новый') ?>
                            </span>
                        </td>
                        <td class="p-5 text-center flex gap-5 justify-center">
                            <?php if (($o['status'] ?? '') !== 'Подтверждён'): ?>
                                <a href="?confirm_order=1&index=<?= $i ?>" class="text-2xl text-green-400 hover:text-green-300 transition" title="Подтвердить">✔</a>
                            <?php endif; ?>
                            <a href="?delete_order=1&index=<?= $i ?>" onclick="return confirm('Удалить заказ?')" class="text-2xl text-red-500 hover:text-red-400 transition" title="Удалить">✕</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Товары -->
    <div x-show="activeTab === 'products'" id="products" x-transition.origin.top data-aos="fade-up">
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
            <h2 class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 to-yellow-600">Товары</h2>
            <a href="admin-add-product.php" class="gold-btn px-8 py-4 rounded-xl font-bold shadow-lg shadow-yellow-600/20 transition-all duration-300 transform hover:scale-105">+ Добавить товар</a>
        </div>

        <?php if (empty($products)): ?>
            <div class="text-center text-gray-400 text-xl py-12">Товаров пока нет</div>
        <?php else: ?>
        <div class="overflow-x-auto bg-gray-900/40 rounded-2xl border border-gray-800 shadow-2xl">
            <table class="w-full min-w-max">
                <thead class="bg-gray-800/80">
                    <tr>
                        <th class="p-5 text-left">ID</th>
                        <th class="p-5 text-left">Название</th>
                        <th class="p-5 text-right">Цена</th>
                        <th class="p-5 text-center">Скидка</th>
                        <th class="p-5 text-right">Со скидкой</th>
                        <th class="p-5 text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $p):
                    $disc = $discounts[(string)$p['id']] ?? 0;
                    $final = $p['price'] * (1 - $disc / 100);
                ?>
                    <tr class="border-t border-gray-800 hover:bg-gray-800/60 transition-colors">
                        <td class="p-5 font-mono text-gray-400"><?= htmlspecialchars($p['id'] ?? '—') ?></td>
                        <td class="p-5 font-medium"><?= htmlspecialchars($p['name'] ?? '—') ?></td>
                        <td class="p-5 text-right"><?= number_format($p['price'] ?? 0, 0, ' ', ' ') ?> ₽</td>
                        <td class="p-5 text-center">
                            <?php if ($disc > 0): ?>
                                <span class="bg-green-700/50 text-green-300 px-3 py-1 rounded-full text-sm font-bold">−<?= $disc ?>%</span>
                            <?php else: ?>
                                <span class="text-gray-500">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-5 text-right font-bold <?= $disc > 0 ? 'text-green-400' : '' ?>">
                            <?= number_format($final, 0, ' ', ' ') ?> ₽
                        </td>
                        <td class="p-5 text-center flex gap-5 justify-center">
                            <a href="admin-edit-product.php?id=<?= urlencode($p['id'] ?? '') ?>" class="text-blue-400 hover:text-blue-300 text-2xl transition" title="Редактировать">✎</a>
                            <a href="admin-delete-product.php?id=<?= urlencode($p['id'] ?? '') ?>" onclick="return confirm('Удалить товар и все связанные данные?')" class="text-red-500 hover:text-red-400 text-2xl transition" title="Удалить">✕</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Скидки -->
    <div x-show="activeTab === 'discounts'" id="discounts" x-transition.origin.top data-aos="fade-up">
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-6">
            <h2 class="text-4xl font-black bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 to-yellow-600">Скидки</h2>
            <button @click="openDiscountModal()" class="gold-btn px-8 py-4 rounded-xl font-bold shadow-lg shadow-yellow-600/20 transition-all duration-300 transform hover:scale-105">+ Добавить / изменить скидку</button>
        </div>

        <div class="overflow-x-auto bg-gray-900/40 rounded-2xl border border-gray-800 shadow-2xl">
            <table class="w-full min-w-max">
                <thead class="bg-gray-800/80">
                    <tr>
                        <th class="p-5 text-left">Товар</th>
                        <th class="p-5 text-center">Скидка</th>
                        <th class="p-5 text-center">Действия</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($discounts)): ?>
                    <tr><td colspan="3" class="p-12 text-center text-gray-400 text-xl">Скидок пока нет</td></tr>
                <?php else: ?>
                    <?php foreach ($discounts as $pid => $percent):
                        $p = $productsById[(int)$pid] ?? null;
                        $name = $p ? htmlspecialchars($p['name']) : "<span class='text-red-400'>Товар удалён (ID: $pid)</span>";
                    ?>
                        <tr class="border-t border-gray-800 hover:bg-gray-800/60 transition-colors">
                            <td class="p-5 font-medium"><?= $name ?></td>
                            <td class="p-5 text-center">
                                <span class="bg-green-700/50 text-green-300 px-4 py-1.5 rounded-full text-base font-bold">−<?= $percent ?>%</span>
                            </td>
                            <td class="p-5 text-center flex gap-6 justify-center">
                                <button @click="openDiscountModal('<?= addslashes($pid) ?>', <?= $percent ?>)" class="text-blue-400 hover:text-blue-300 text-2xl transition" title="Изменить">✎</button>
                                <a href="?delete_discount=1&pid=<?= urlencode($pid) ?>" onclick="return confirm('Удалить скидку?')" class="text-red-500 hover:text-red-400 text-2xl transition" title="Удалить">✕</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Модальное окно скидки -->
    <div x-show="discountModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 p-4" @click.away="discountModal = false">
        <div class="bg-gray-800 rounded-2xl p-8 w-full max-w-md border border-gray-700 shadow-2xl" @click.stop>
            <h3 class="text-2xl font-bold mb-6 text-yellow-400" x-text="editingPid ? 'Изменить скидку' : 'Новая скидка'"></h3>

            <form method="post">
                <input type="hidden" name="save_discount" value="1">

                <div class="mb-6">
                    <label class="block text-gray-300 mb-2">Товар</label>
                    <select name="product_id" required class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500">
                        <option value="">Выберите товар...</option>
                        <?php foreach ($products as $p): ?>
                            <option value="<?= htmlspecialchars($p['id']) ?>" x-bind:selected="editingPid == '<?= htmlspecialchars($p['id']) ?>'">
                                <?= htmlspecialchars($p['name']) ?> (ID: <?= htmlspecialchars($p['id']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-300 mb-2">Скидка (%)</label>
                    <input type="number" name="percent" min="0" max="100" x-model="editingPercent" class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-yellow-500" required>
                </div>

                <div class="flex justify-end gap-4">
                    <button type="button" @click="discountModal = false" class="px-6 py-3 text-gray-400 hover:text-white transition">Отмена</button>
                    <button type="submit" class="gold-btn px-8 py-3 rounded-lg font-bold transition">Сохранить</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Аналитика -->
    <div x-show="activeTab === 'analytics'" x-transition.origin.top data-aos="fade-up">
        <h2 class="text-5xl font-black text-center mb-12 bg-clip-text text-transparent bg-gradient-to-r from-yellow-300 to-yellow-600">Аналитика</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="bg-gray-900/40 p-6 rounded-2xl border border-gray-800">
                <h3 class="text-2xl font-bold mb-6 text-center">Доход по дням</h3>
                <canvas id="incomeChartFull" height="160"></canvas>
            </div>
            <div class="bg-gray-900/40 p-6 rounded-2xl border border-gray-800">
                <h3 class="text-2xl font-bold mb-6 text-center">Заказы по дням</h3>
                <canvas id="ordersChart" height="160"></canvas>
            </div>
            <div class="bg-gray-900/40 p-6 rounded-2xl border border-gray-800">
                <h3 class="text-2xl font-bold mb-6 text-center">Топ-5 товаров по доходу</h3>
                <canvas id="topProductsChart" height="160"></canvas>
            </div>
            <div class="bg-gray-900/40 p-6 rounded-2xl border border-gray-800">
                <h3 class="text-2xl font-bold mb-6 text-center">Распределение статусов</h3>
                <canvas id="statusPieChart" height="160"></canvas>
            </div>
        </div>
    </div>

</main>

<!-- Графики -->
<script>
// Данные из PHP → JavaScript
const dates       = <?= json_encode(array_keys($ordersPerDay)) ?>;
const incomeData  = <?= json_encode(array_column($ordersPerDay, 'income')) ?>;
const ordersData  = <?= json_encode(array_column($ordersPerDay, 'orders')) ?>;
const topNames    = <?= json_encode(array_column($topProducts, 'name')) ?>;
const topIncome   = <?= json_encode(array_column($topProducts, 'income')) ?>;
const statusLabels = <?= json_encode(array_keys($orderStatuses)) ?>;
const statusValues = <?= json_encode(array_values($orderStatuses)) ?>;

// График дохода (маленький на дашборде)
new Chart(document.getElementById('incomeChart'), {
    type: 'line',
    data: {
        labels: dates,
        datasets: [{
            label: 'Доход ₽',
            data: incomeData,
            borderColor: '#facc15',
            backgroundColor: 'rgba(250,204,21,0.15)',
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { labels: { color: '#e5e7eb' } } }
    }
});

// Полноэкранные графики в аналитике
new Chart(document.getElementById('incomeChartFull'), {
    type: 'line',
    data: { labels: dates, datasets: [{ label: 'Доход ₽', data: incomeData, borderColor: '#facc15', backgroundColor: 'rgba(250,204,21,0.18)', fill: true, tension: 0.4 }] },
    options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { labels: { color: '#e5e7eb' } } } }
});

new Chart(document.getElementById('ordersChart'), {
    type: 'line',
    data: { labels: dates, datasets: [{ label: 'Заказы', data: ordersData, borderColor: '#22d3ee', backgroundColor: 'rgba(34,211,238,0.15)', fill: true, tension: 0.4 }] },
    options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { labels: { color: '#e5e7eb' } } } }
});

new Chart(document.getElementById('topProductsChart'), {
    type: 'bar',
    data: { labels: topNames, datasets: [{ label: 'Доход ₽', data: topIncome, backgroundColor: '#facc15', borderColor: '#eab308', borderWidth: 1 }] },
    options: { scales: { y: { beginAtZero: true } }, plugins: { legend: { labels: { color: '#e5e7eb' } } } }
});

new Chart(document.getElementById('statusPieChart'), {
    type: 'pie',
    data: { labels: statusLabels, datasets: [{ data: statusValues, backgroundColor: ['#facc15', '#22d3ee', '#ef4444', '#8b5cf6', '#10b981'] }] },
    options: { responsive: true, plugins: { legend: { position: 'bottom', labels: { color: '#e5e7eb' } } } }
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    AOS.init({ duration: 900, once: true, easing: 'ease-out' });
</script>

</body>
</html>