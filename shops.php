<?php
session_start();
include 'config.php';


// Initialize sessions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

// === HANDLE ADD TO CART ===
if (isset($_POST['add_to_cart']) && $_POST['add_to_cart'] == 1) {
    // Log POST data for debugging
    error_log("Add to Cart POST received: " . print_r($_POST, true));

    // Validate POST data
    if (empty($_POST['id']) || empty($_POST['title']) || empty($_POST['image']) || !isset($_POST['price'])) {
        error_log("Invalid POST data for add_to_cart: " . print_r($_POST, true));
        header("Location: " . ($_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'shops.php') . "?error=invalid_data");
        exit;
    }

    $product_id = (int)$_POST['id'];
    $price = (float)($_POST['discount_price'] ?: $_POST['price']);
    
    // Validate price
    if ($price <= 0 || !is_numeric($price)) {
        error_log("Invalid price for product ID $product_id: $price");
        header("Location: " . ($_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'shops.php') . "?error=invalid_price");
        exit;
    }

    $cart_item = [
        'id' => $product_id,
        'title' => trim($_POST['title']),
        'image' => trim($_POST['image']),
        'price' => $price,
        'quantity' => 1
    ];

    // Check if item already in cart
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $cart_item['id']) {
            $item['quantity'] = (int)$item['quantity'] + 1;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = $cart_item;
    }

    // Log cart contents after update
    error_log("Cart updated: " . print_r($_SESSION['cart'], true));

    // Redirect back to the same page
    $redirect = $_POST['redirect_url'] ?? $_SERVER['HTTP_REFERER'] ?? 'shops.php';
    header("Location: $redirect?success=added_to_cart");
    exit;
}

// === HANDLE REMOVE FROM CART ===
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$index]);
            break;
        }
    }
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    error_log("Item removed from cart, ID: $remove_id. New cart: " . print_r($_SESSION['cart'], true));
    header("Location: cart.php?success=removed_from_cart");
    exit;
}
// === HANDLE ADD TO WISHLIST ===
if (isset($_POST['add_to_wishlist'])) {
    $product_id = (int)$_POST['id'];
    $wishlist_item = [
        'id' => $product_id,
        'title' => $_POST['title'],
        'image' => $_POST['image'],
        'price' => (float)($_POST['price'])
    ];
    $found = false;
    foreach ($_SESSION['wishlist'] as $index => $item) {
        if ($item['id'] == $wishlist_item['id']) {
            unset($_SESSION['wishlist'][$index]);
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['wishlist'][] = $wishlist_item;
    }
    $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
    header("Location: shops.php?" . http_build_query($_GET));
    exit;
}

// === HANDLE REMOVE FROM WISHLIST ===
if (isset($_GET['remove_wishlist'])) {
    $remove_id = (int)$_GET['remove_wishlist'];
    foreach ($_SESSION['wishlist'] as $index => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['wishlist'][$index]);
            break;
        }
    }
    $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
    // Redirect based on 'from' parameter
    $redirect = isset($_GET['from']) && $_GET['from'] == 'wishlist' ? 'wishlist.php' : 'shops.php?' . http_build_query(array_diff_key($_GET, ['remove_wishlist' => '', 'from' => '']));
    header("Location: $redirect");
    exit;
}

// === CONFIG ===
$products_per_page = 9;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $products_per_page;

// === FILTERS ===
$category_ids = isset($_GET['category']) ? array_map('intval', (array)$_GET['category']) : [];
$brand_ids = isset($_GET['brand']) ? array_map('intval', (array)$_GET['brand']) : [];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// === BUILD WHERE CLAUSE ===
$where = [];
$params = [];
$types = '';

if (!empty($category_ids)) {
    $placeholders = str_repeat('?,', count($category_ids) - 1) . '?';
    $where[] = "p.category_id IN ($placeholders)";
    $params = array_merge($params, $category_ids);
    $types .= str_repeat('i', count($category_ids));
}

if (!empty($brand_ids)) {
    $placeholders = str_repeat('?,', count($brand_ids) - 1) . '?';
    $where[] = "p.brand_id IN ($placeholders)";
    $params = array_merge($params, $brand_ids);
    $types .= str_repeat('i', count($brand_ids));
}

if ($search) {
    $where[] = "(p.title LIKE ? OR p.description LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= 'ss';
}

$where_clause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// === COUNT TOTAL PRODUCTS ===
$count_sql = "SELECT COUNT(*) as total FROM products p $where_clause";
$count_stmt = $conn->prepare($count_sql);
if (!empty($params)) {
    $count_stmt->bind_param($types, ...$params);
}
$count_stmt->execute();
$total_products = $count_stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total_products / $products_per_page);

// === FETCH PRODUCTS ===
$sql = "SELECT p.*, b.name AS brand_name, c.name AS category_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN categories c ON p.category_id = c.id
        $where_clause
        ORDER BY p.id DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$params[] = $products_per_page;
$params[] = $offset;
$types .= 'ii';
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// === HELPER FUNCTION (URL with filters + page) ===
function build_url($page) {
    $params = $_GET;
    $params['page'] = $page;
    return 'shops.php?' . http_build_query($params);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="eCommerce,shop,fashion">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Pesco - eCommerce HTML Template</title>
    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Aoboshi+One&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/fonts/flaticon/flaticon_pesco.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/slick/slick.css">
    <link rel="stylesheet" href="assets/vendor/nice-select/css/nice-select.css">
    <link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
    <link rel="stylesheet" href="assets/vendor/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/vendor/aos/aos.css">
    <link rel="stylesheet" href="assets/css/default.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
:root{
  --header-top-height: 60px;
  --header-nav-height: 50px;
}

.header-area {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1100;
  background: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}

.header-area .header-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 10px 18px;
  background: #fff;
  flex-wrap: wrap;
}

.header-area .header-nav {
  display: flex;
  align-items: center;
  justify-content: center;
  background: #fafafa;
  border-top: 1px solid #eee;
  border-bottom: 1px solid #eee;
  padding: 8px 16px;
}

.header-area .header-nav .nav-inner {
  width: 100%;
  max-width: 1200px;
}

.nav-right-item ul {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  align-items: center;
  gap: 12px;
}

.nav-right-item .wishlist-btn,
.nav-right-item .cart-button {
  position: relative;
  font-size: 20px;
  color: #333;
}

.nav-right-item .pro-count {
  position: absolute;
  top: -6px;
  right: -8px;
  background: #de3576;
  color: #fff;
  width: 18px;
  height: 18px;
  line-height: 18px;
  font-size: 11px;
  font-weight: 600;
  text-align: center;
  border-radius: 50%;
  box-shadow: 0 1px 2px rgba(0,0,0,0.15);
}

.sidemenu-wrapper-wishlist {
    position: fixed;
    top: 0;
    right: -360px;
    width: 360px;
    height: 100%;
    background: #fff;
    z-index: 1200;
    transition: right 0.3s ease;
    box-shadow: -2px 0 5px rgba(0, 0, 0, 0.1);
}

.sidemenu-wrapper-wishlist.open {
    right: 0;
}

.sidemenu-content {
    padding: 20px;
    position: relative;
    height: 100%;
    overflow-y: auto;
}

.sidemenu-wishlist-close {
    position: absolute;
    top: 10px;
    right: 10px;
    cursor: pointer;
    font-size: 20px;
    color: #333;
}

.pesco-mini-wishlist-list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
}

.sidebar-wishlist-item {
    display: flex;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.sidebar-wishlist-item img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    margin-right: 10px;
}

.sidebar-wishlist-item a {
    flex: 1;
    color: #333;
    text-decoration: none;
}

.sidebar-wishlist-item .remove-wishlist {
    margin-right: 10px;
    color: #de3576;
}

.sidebar-wishlist-item .price {
    font-weight: 600;
    color: #de3576;
}

.wishlist-button-box {
    text-align: center;
}

.wishlist-button-box .theme-btn {
    display: inline-block;
    padding: 10px 20px;
    background: #de3576;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
}

.wishlist-button-box .theme-btn:hover {
    background: #c02c65;
}

.icon-btn.wishlist-added {
    color: #de3576;
}

.icon-btn.wishlist-added i {
    color: #de3576;
}

.offcanvas__overlay.open {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1199;
}

.pesco-pagination { text-align: center; margin-top: 30px; }
.pesco-pagination ul { display: inline-block; padding: 0; margin: 0; }
.pesco-pagination ul li { display: inline-block; margin: 0 6px; }

@media (max-width: 991px) {
  .header-area .header-top { padding: 8px 12px; }
  .header-area .header-top .site-branding { flex: 0 0 auto; }
  .header-area .header-top .search-block { flex: 1 1 100%; order: 3; margin-top: 8px; }
  .header-area .header-top .nav-icons { order: 2; }
  .header-area .header-nav { padding: 6px 10px; }
}
/* Product Card */
.product-card {
    background: #fff;
    border: 1px solid #de3576;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    border-color: #de3576;
}

/* Thumbnail */
.product-thumbnail {
    position: relative;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #de3576;
}

.product-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-thumbnail img {
    transform: scale(1.05);
}

/* Hover icons */
.hover-content {
    position: absolute;
    top: 10px;
    right: 10px;
    display: flex;
    gap: 8px;
}

.icon-btn {
    background: rgba(255,255,255,0.85);
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #333;
    cursor: pointer;
    transition: background 0.3s ease;
}

.icon-btn:hover {
    background: #fff;
}

.wishlist-added {
    color: #de3576;
}

/* Info Section */
.product-info-wrap {
    padding: 15px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    text-align: center;
    background-color:#13172B;
}

/* Title */
.product-info .title {
    font-size: 17px;
    font-weight: 600;
    color: #de3576;
    margin-bottom: 8px;
}

/* Description */
.product-info p {
    color: #fff;
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Prices */
.product-price {
    text-align: center;
    margin-bottom: 10px;
}

.product-price .real-price {
    color: #999;
    font-size: 14px;
    text-decoration: line-through;
    display: block;
}

.product-price .discount-price {
    color: #de3576;
    font-size: 18px;
    font-weight: bold;
}

/* Buttons */
.product-actions {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: auto;
}

.product-actions .btn {
    flex: 1 1 45%;
    text-align: center;
    border: none;
    border-radius: 6px;
    padding: 8px 14px;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Add to Cart */
.btn-cart {
    background: #de3576;
    color: #fff;
}
.btn-cart:hover {
    background: #de3576;
    color: #fff;
}

/* View Details */
.btn-details {
    background: #de3576;
    color: white;
}
.btn-details:hover {
    background: #f2f2f2;
    color: #333;
}

/* Responsive */
@media (max-width: 768px) {
    /* .product-actions {
        flex-direction: column;
    } */
    .product-actions .btn {
        width: 100%;
    }
    .product-info .title {
        font-size: 16px;
    }
}
/* === Cart Sidebar Styles === */
.sidebar-cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 12px;
    transition: all 0.3s ease;
}

.sidebar-cart-item:hover {
    background: #f9f9f9;
}

.cart-item-info {
    display: flex;
    align-items: center;
    flex: 1;
}

.cart-item-info img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 6px;
    margin-right: 10px;
}

.cart-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.cart-text .title {
    font-size: 14px;
    font-weight: 600;
    color: #333;
    margin-bottom: 4px;
}

.cart-text .quantity {
    font-size: 13px;
    color: #555;
}

.cart-text .currency {
    font-weight: 600;
    color: #111;
    margin-left: 90px;
}

/* === Delete Icon === */
.remove-cart {
    background: none;
    border: none;
    color: #c0392b;
    font-size: 16px;
    margin-left: 8px;
    cursor: pointer;
    transition: none; /* no hover animation */
    text-decoration: none;
}

.remove-cart:hover {
    color: #c0392b; /* same color (no hover change) */
}

/* === Button Box === */
.cart-button-box {
    display: flex;
    flex-direction: column;
    gap: 10px; /* adds space between buttons */
    margin-top: 20px;
}

.cart-button-box .theme-btn {
    text-align: center;
    border-radius: 6px;
    padding: 10px 0;
}



body { transition: padding-top .12s ease; }
    </style>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader">
            <img src="assets/images/loader.gif" alt="Loader">
        </div>
    </div>
    <!-- Overlay -->
    <div class="offcanvas__overlay"></div>
<div class="sidemenu-wrapper-cart">
    <div class="sidemenu-content">
        <h4>My Cart</h4>
        <div class="sidemenu-cart-close"><i class="far fa-times"></i></div>

        <ul class="pesco-mini-cart-list">
            <?php if (empty($_SESSION['cart'])): ?>
                <li class="empty-cart">Your cart is empty</li>
            <?php else: ?>
                <?php foreach ($_SESSION['cart'] as $item): ?>
                    <li class="sidebar-cart-item" id="cart-item-<?= $item['id'] ?>">
                        
                        <!-- Remove Button (No Hover Effect) -->
                        <a href="cart.php?remove=<?= $item['id'] ?>" class="remove-cart" title="Remove Item">
                            <i style="color: #de3576;" class="far fa-trash-alt"></i>
                        </a>

                        <!-- Product Info -->
                        <div class="cart-item-info">
                            <img src="<?= htmlspecialchars($item['image']) ?>" alt="cart image">
                            <div class="cart-text">
                                <span class="title"><?= htmlspecialchars($item['title']) ?></span>
                                <span class="quantity">
                                    <?= $item['quantity'] ?> × 
                                    <span class="currency">Rs.<?= number_format($item['price'], 0) ?></span>
                                </span>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>

        <div class="cart-mini-total">
            <strong>Subtotal:</strong>
            <span class="currency">Rs.<?= number_format(array_sum(array_map(function($item) {
                return $item['price'] * $item['quantity'];
            }, $_SESSION['cart'])), 0) ?></span>
        </div>

        <div class="cart-button-box">
            <a href="cart.php" class="theme-btn style-one">View Cart</a>
            <a href="checkout.php" class="theme-btn style-one checkout-btn">Checkout</a>
        </div>
    </div>
</div>


    <!-- Sidebar Wishlist -->
    <div class="sidemenu-wrapper-wishlist">
        <div class="sidemenu-content">
            <h4>My Wishlist</h4>
            <div class="sidemenu-wishlist-close"><i class="far fa-times"></i></div>
            <ul class="pesco-mini-wishlist-list">
                <?php if (empty($_SESSION['wishlist'])): ?>
                    <li>Your wishlist is empty</li>
                <?php else: ?>
                    <?php foreach ($_SESSION['wishlist'] as $item): ?>
                        <li class="sidebar-wishlist-item">
                            <a href="shops.php?remove_wishlist=<?= $item['id'] ?>&from=shops&<?= http_build_query(array_diff_key($_GET, ['remove_wishlist' => '', 'from' => ''])) ?>" class="remove-wishlist"><i class="far fa-trash-alt"></i></a>
                            <a href="product-details.php?id=<?= $item['id'] ?>">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="wishlist image">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                            <span class="price currency"><?= number_format($item['price'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            
        </div>
    </div>
    <!-- Header -->
    <header class="header-area">
        <!-- <div class="header-top">
            <div class="site-branding">
                <a href="index.html"><img src="assets/images/logo.png" alt="Logo"></a>
            </div>
            <div class="search-block">
                <form method="GET" action="shops.php">
                    <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="nav-icons nav-right-item">
                <ul>
                    <li>
                        <a href="javascript:void(0)" class="cart-button">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="pro-count cart-count"><?= count($_SESSION['cart']) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div> -->
        <?php include 'include/header.php'; ?>
        <?php include 'include/nav.php'; ?>
    </header>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="page-banner-wrapper p-r z-1">
            <svg class="lineanm" viewBox="0 0 1920 347" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="line" d="M-39 345.187C70 308.353 397.628 293.477 436 145.186C490 -63.5 572 -57.8156 688 255.186C757.071 441.559 989.5 -121.315 1389 98.6856C1708.6 274.686 1940.33 156.519 1964.5 98.6856" stroke="white" stroke-width="3" stroke-dasharray="2 2" />
            </svg>
            <div class="page-image"><img src="assets/images/products/unstiched/product-banner.jpg" alt="image"></div>
            <svg class="page-svg" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.1742 33.0065C14.029 35.2507 7.5486 39.0636 0 40.7339V86H1937V64.9942C1933.1 60.1623 1912.65 65.1777 1904.51 62.6581C1894.22 59.4678 1884.93 55.0079 1873.77 52.7742C1861.2 50.2585 1823.41 36.3854 1811.99 39.9252C1805.05 42.0727 1796.94 37.6189 1789.36 36.6007C1769.18 33.8879 1747.19 31.1848 1726.71 29.7718C1703.81 28.1919 1678.28 27.0012 1657.53 34.4442C1636.45 42.005 1606.07 60.856 1579.5 55.9191C1561.6 52.5906 1543.41 47.0959 1528.45 56.9075C1510.85 68.4592 1485.74 74.2518 1460.44 76.136C1432.32 78.2297 1408.53 70.6879 1384.73 62.2987C1339.52 46.361 1298.19 27.1677 1255.08 9.28534C1242.58 4.10111 1214.68 15.4762 1200.55 16.6533C1189.77 17.5509 1181.74 15.4508 1172.12 12.8795C1152.74 7.70033 1133.23 2.88525 1111.79 2.63621C1088.85 2.36971 1073.94 7.88289 1056.53 15.8446C1040.01 23.3996 1027.48 26.1777 1007.8 26.1777C993.757 26.1777 975.854 25.6887 962.844 28.9632C941.935 34.2258 932.059 38.7874 914.839 28.6037C901.654 20.8061 866.261 -2.56499 844.356 7.12886C831.264 12.9222 820.932 21.5146 807.663 27.5255C798.74 31.5679 779.299 42.0561 766.33 39.1166C758.156 37.2637 751.815 31.6349 745.591 28.2443C730.967 20.2774 715.218 13.2948 695.846 10.723C676.168 8.11038 658.554 23.1787 641.606 27.4357C617.564 33.4742 602.283 27.7951 579.244 27.7951C568.142 27.7951 548.414 30.4002 541.681 23.6618C535.297 17.2722 530.162 9.74921 523.263 3.71444C517.855 -1.01577 505.798 -0.852017 498.318 2.09709C479.032 9.7007 453.07 10.0516 431.025 9.64475C407.556 9.21163 368.679 1.61612 346.618 10.3636C319.648 21.0575 291.717 53.8338 254.67 45.2266C236.134 40.9201 225.134 37.5813 204.78 40.7339C186.008 43.6415 171.665 50.7785 156.051 57.3567C146.567 61.3523 152.335 52.6281 151.12 47.9222C149.535 41.7853 139.994 34.5585 132.991 30.4008C120.206 22.8098 90.2848 24.3246 74.2546 24.6502C55.5552 25.0301 37.9201 27.747 21.1742 33.0065Z" fill="#FFFAF3" />
            </svg>
            <div class="shape shape-one"><span></span></div>
            <div class="shape shape-two"><span></span></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="page-banner-content">
                            <h1>All Products</h1>
                            <ul class="breadcrumb-link">
                                <li><a href="index.html" style="color: #de3576;">Home</a></li>
                                <li><i class="far fa-long-arrow-right"></i></li>
                                <li class="active">All Products</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Shop Page Section -->
    <section class="shop-page-section pt-120 pb-80">
        <div class="container">
            <div class="row">
                <!-- Sidebar: Filters -->
                <div class="col-xl-3">
                    <div class="shop-sidebar-area">
                        <form method="GET" id="filterForm">
                            <input type="hidden" name="page" value="1">
                            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                            <!-- Categories -->
                            <div class="product-widget product-categories-widget mb-40">
                                <div class="widget-content">
                                    <h4 class="widget-title" style="color: #de3576;">Product Categories</h4>
                                    <ul class="categories-list">
                                        <?php
                                        $cat_sql = "SELECT c.id, c.name, COUNT(p.id) AS total FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id";
                                        $cat_result = $conn->query($cat_sql);
                                        while ($cat = $cat_result->fetch_assoc()):
                                            $checked = in_array($cat['id'], $category_ids) ? 'checked' : '';
                                        ?>
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="cat<?= $cat['id'] ?>" name="category[]" value="<?= $cat['id'] ?>" <?= $checked ?> onchange="document.getElementById('filterForm').submit()">
                                                    <label class="form-check-label" for="cat<?= $cat['id'] ?>">
                                                        <?= htmlspecialchars($cat['name']) ?> <span>(<?= $cat['total'] ?>)</span>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                            <!-- Brands -->
                            <div class="product-widget product-categories-widget mb-40">
                                <div class="widget-content">
                                    <h4 class="widget-title" style="color: #de3576;">Brands</h4>
                                    <ul class="categories-list">
                                        <?php
                                        $brand_sql = "SELECT b.id, b.name, COUNT(p.id) AS total FROM brands b LEFT JOIN products p ON p.brand_id = b.id GROUP BY b.id";
                                        $brand_result = $conn->query($brand_sql);
                                        while ($brand = $brand_result->fetch_assoc()):
                                            $checked = in_array($brand['id'], $brand_ids) ? 'checked' : '';
                                        ?>
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="br<?= $brand['id'] ?>" name="brand[]" value="<?= $brand['id'] ?>" <?= $checked ?> onchange="document.getElementById('filterForm').submit()">
                                                    <label class="form-check-label" for="br<?= $brand['id'] ?>">
                                                        <?= htmlspecialchars($brand['name']) ?> <span>(<?= $brand['total'] ?>)</span>
                                                    </label>
                                                </div>
                                            </li>
                                        <?php endwhile; ?>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- Product Grid -->
                <div class="col-xl-9">
                    <div class="shop-page-wrapper">
                        <!-- Filter Info -->
                        <div class="shop-filter mb-60">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-sm-6">
                                    <p>
                                        <strong>Showing <?= $result->num_rows ?> of <?= $total_products ?> products</strong>
                                        <?php if (!empty($category_ids) || !empty($brand_ids) || $search): ?>
                                            <small class="text-muted">
                                                (Filtered: <?= count($category_ids) ?> cat, <?= count($brand_ids) ?> brand<?php if ($search): ?>, Search: "<?= htmlspecialchars($search) ?>"<?php endif; ?>)
                                            </small>
                                        <?php endif; ?>
                                    </p>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <a href="shops.php" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
<!-- Products -->
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col">
                <div class="product-card">
                    
                    <!-- Thumbnail -->
                    <div class="product-thumbnail">
                        <img src="<?= htmlspecialchars($row['image']) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
                        <div class="hover-content">
                            <form method="POST" action="shops.php?<?= http_build_query($_GET) ?>">
                                <input type="hidden" name="add_to_wishlist" value="1">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="title" value="<?= htmlspecialchars($row['title']) ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($row['image']) ?>">
                                <input type="hidden" name="price" value="<?= $row['discount_price'] ?: $row['price'] ?>">
                                <button type="submit" class="icon-btn <?= in_array($row['id'], array_column($_SESSION['wishlist'], 'id')) ? 'wishlist-added' : '' ?>" title="Add to Wishlist">
                                    <i class="fa fa-heart"></i>
                                </button>
                            </form>

                            
                        </div>
                    </div>

                    <!-- Info Section -->
                    <div class="product-info-wrap">
                        <div class="product-info">
                            <h4 class="title"><?= htmlspecialchars($row['title']) ?></h4>
                            <p><?= htmlspecialchars($row['description'] ?: 'Stylish and comfortable fashion item.') ?></p>
                        </div>

                        <!-- Prices -->
<div class="product-price">
    <?php if (!empty($row['discount_price'])): ?>
        <span class="real-price">₨: <?= number_format($row['price']) ?></span>
        <span class="discount-price">₨: <?= number_format($row['discount_price']) ?></span>
    <?php else: ?>
        <span class="discount-price">₨: <?= number_format($row['price']) ?></span>
    <?php endif; ?>
</div>


                        <!-- Buttons -->
                        <div class="product-actions">
                            <form method="POST" action="shops.php?<?= http_build_query($_GET) ?>" style="margin:0;">
                                <input type="hidden" name="add_to_cart" value="1">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="title" value="<?= htmlspecialchars($row['title']) ?>">
                                <input type="hidden" name="image" value="<?= htmlspecialchars($row['image']) ?>">
                                <input type="hidden" name="price" value="<?= $row['price'] ?>">
                                <input type="hidden" name="discount_price" value="<?= $row['discount_price'] ?>">
                                <button type="submit" class="btn btn-cart">Add to Cart</button>
                            </form>

                            <a href="product-details.php?id=<?= $row['id'] ?>" class="btn btn-details">View Details</a>
                        </div>
                    </div>

                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="col-12 text-center py-5">
            <h5>No products found!</h5>
            <p>Try adjusting filters or <a href="shops.php">reset all</a>.</p>
        </div>
    <?php endif; ?>
</div>

                        <!-- Pagination -->
<?php if ($total_pages > 1): ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="pesco-pagination mb-40">
                <ul>
                    <li class="<?= $page <= 1 ? 'disabled' : '' ?>">
                        <a href="<?= $page > 1 ? build_url($page - 1) : '#' ?>">&lt;</a>
                    </li>

                    <?php
                    $start = max(1, $page - 2);
                    $end = min($total_pages, $page + 2);

                    if ($start > 1): ?>
                        <li><a href="<?= build_url(1) ?>">1</a></li>
                        <?php if ($start > 2): ?><li>...</li><?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li>
                            <a href="<?= build_url($i) ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $total_pages): ?>
                        <?php if ($end < $total_pages - 1): ?><li>...</li><?php endif; ?>
                        <li><a href="<?= build_url($total_pages) ?>"><?= $total_pages ?></a></li>
                    <?php endif; ?>

                    <li class="<?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a href="<?= $page < $total_pages ? build_url($page + 1) : '#' ?>">&gt;</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
<?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Newsletter -->
    <?php include 'include/newslatter.php'; ?>
    <!-- Footer -->
    <?php include 'include/footer.php'; ?>
    <!-- Scripts -->
    <script>
        $(document).ready(function() {
            // Initialize Magnific Popup for eye icon
            $('.img-popup').magnificPopup({
                type: 'image',
                mainClass: 'mfp-zoom-in',
                removalDelay: 500,
                zoom: {
                    enabled: true,
                    duration: 300,
                    opener: function(element) {
                        return element.find('img') || element;
                    }
                }
            });

            // Cart Sidebar Toggle
            $('.cart-button').click(function(e) {
                e.preventDefault();
                $('.sidemenu-wrapper-cart').toggleClass('open');
                $('.offcanvas__overlay').toggleClass('open');
            });
            $('.sidemenu-cart-close').click(function() {
                $('.sidemenu-wrapper-cart').removeClass('open');
                $('.offcanvas__overlay').removeClass('open');
            });

            // Wishlist Sidebar Toggle
            $('.wishlist-btn').click(function(e) {
                e.preventDefault();
                $('.sidemenu-wrapper-wishlist').toggleClass('open');
                $('.offcanvas__overlay').toggleClass('open');
            });
            $('.sidemenu-wishlist-close').click(function() {
                $('.sidemenu-wrapper-wishlist').removeClass('open');
                $('.offcanvas__overlay').removeClass('open');
            });

            // Close sidebars when overlay is clicked
            $('.offcanvas__overlay').click(function() {
                $('.sidemenu-wrapper-cart').removeClass('open');
                $('.sidemenu-wrapper-wishlist').removeClass('open');
                $(this).removeClass('open');
            });

            // Mobile Menu Toggle
            $('.navbar-toggler').click(function() {
                $('.pesco-nav-menu').toggleClass('open');
            });

            // Ensure filter form submits on checkbox change
            $('.form-check-input').on('change', function() {
                $('#filterForm').submit();
            });

            // Update wishlist count after removal
            $('.remove-wishlist').click(function(e) {
                $('.sidemenu-wrapper-wishlist').addClass('open');
                $('.offcanvas__overlay').addClass('open');
            });
        });
    </script>
</body>
</html>