<!-- admin-delete-product.php -->
<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin-login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$productsJson = file_get_contents('products.json');
$products = json_decode($productsJson, true) ?? [];
$key = array_search($id, array_column($products, 'id'));
if ($key !== false) {
    unset($products[$key]);
    $products = array_values($products);
    file_put_contents('products.json', json_encode($products, JSON_UNESCAPED_UNICODE));
}
header('Location: admin-dashboard.php');
exit;
?>