<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

if (!isset($_GET['id'])) {
    header("Location: shops.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Initialize wishlist and cart if not set
if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Wishlist Addition
if (isset($_POST['add_to_wishlist'])) {
    $wishlist_item = [
        'id' => $product_id,
        'title' => $_POST['title'],
        'image' => $_POST['image'],
        'price' => $_POST['price']
    ];
    if (!in_array($wishlist_item, $_SESSION['wishlist'])) {
        $_SESSION['wishlist'][] = $wishlist_item;
    }
    $wishlist_message = "Product added to wishlist!";
}

// Fetch Product Details
$stmt = $conn->prepare("
    SELECT p.*, pd.description AS pd_description, pd.sku, pd.tags, pd.fabric_type, pd.care_instructions, 
           pd.occasion_type, pd.sleeve_type, pd.pattern, pd.closure_type, pd.country_of_origin, 
           pd.features, b.name AS brand_name, c.name AS category_name, c.id AS category_id
    FROM products p
    LEFT JOIN product_details pd ON p.id = pd.product_id
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.id = ?
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo "<h2>Product not found!</h2>";
    exit;
}

// Use product_details.description if available, else products.description
$description = $product['pd_description'] ?? $product['description'] ?? 'No description available.';

// Fetch Product Images
$images_stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
$images_stmt->bind_param("i", $product_id);
$images_stmt->execute();
$images = $images_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback: Use main product image if no additional images
if (empty($images)) {
    $images[] = ['image_url' => $product['image']];
}

// Fetch Colors
$colors_stmt = $conn->prepare("SELECT color FROM product_colors WHERE product_id = ?");
$colors_stmt->bind_param("i", $product_id);
$colors_stmt->execute();
$colors = $colors_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback: Default colors if none in DB
if (empty($colors)) {
    $colors = [
        ['color' => 'black'], ['color' => 'red'], ['color' => 'blue'], ['color' => 'green']
    ];
}

// Fetch Sizes
$sizes_stmt = $conn->prepare("SELECT size FROM product_sizes WHERE product_id = ?");
$sizes_stmt->bind_param("i", $product_id);
$sizes_stmt->execute();
$sizes = $sizes_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fallback: Default sizes based on category
if (empty($sizes)) {
    if (in_array($product['category_id'], [3, 5])) { // Footwear or Casual Slippers
        $sizes = [
            ['size' => '7'], ['size' => '8'], ['size' => '9'], ['size' => '10'], ['size' => '11']
        ];
    } else {
        $sizes = [
            ['size' => 'S'], ['size' => 'M'], ['size' => 'L'], ['size' => 'XL'], ['size' => '2XL']
        ];
    }
}

// Fetch Reviews
$reviews_stmt = $conn->prepare("SELECT user_name, rating, comment, created_at FROM reviews WHERE product_id = ?");
$reviews_stmt->bind_param("i", $product_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch Related Products
$related_stmt = $conn->prepare("
    SELECT p.*, b.name AS brand_name, c.name AS category_name
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.id != ?
    LIMIT 8
");
$related_stmt->bind_param("ii", $product['category_id'], $product_id);
$related_stmt->execute();
$related_products = $related_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="eCommerce,shop,fashion">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Product-details</title>
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
    <div class="preloader">
        <div class="loader">
            <img src="assets/images/loader.gif" alt="Loader">
        </div>
    </div>
     <!--====== Start Sidemenu-wrapper-cart Area ======-->
        <div class="sidemenu-wrapper-cart">
            <div class="sidemenu-content">
                <div class="widget widget-shopping-cart">
                    <h4>My cart</h4>
                    <div class="sidemenu-cart-close"><i class="far fa-times"></i></div>
                    <div class="widget-shopping-cart-content">
                        <ul class="pesco-mini-cart-list">
                            <li class="sidebar-cart-item">
                                <a href="#" class="remove-cart"><i class="far fa-trash-alt"></i></a>
                                <a href="#">
                                    <img src="assets/images/products/cart-1.jpg" alt="cart image">
                                    leggings with mesh panels
                                </a>
                                <span class="quantity">1 × <span><span class="currency">$</span>940.00</span></span>
                            </li>
                            <li class="sidebar-cart-item">
                                <a href="#" class="remove-cart"><i class="far fa-trash-alt"></i></a>
                                <a href="#">
                                    <img src="assets/images/products/cart-2.jpg" alt="cart image">
                                    Summer dress with belt
                                </a>
                                <span class="quantity">1 × <span><span class="currency">$</span>940.00</span></span>
                            </li>
                            <li class="sidebar-cart-item">
                                <a href="#" class="remove-cart"><i class="far fa-trash-alt"></i></a>
                                <a href="#">
                                    <img src="assets/images/products/cart-3.jpg" alt="cart image">
                                    Floral print sundress
                                </a>
                                <span class="quantity">1 × <span><span class="currency">$</span>940.00</span></span>
                            </li>
                            <li class="sidebar-cart-item">
                                <a href="#" class="remove-cart"><i class="far fa-trash-alt"></i></a>
                                <a href="#">
                                    <img src="assets/images/products/cart-4.jpg" alt="cart image">
                                    Sheath Gown Red Colors
                                </a>
                                <span class="quantity">1 × <span><span class="currency">$</span>940.00</span></span>
                            </li>
                        </ul>
                        <div class="cart-mini-total">
                            <div class="cart-total">
                                <span><strong>Subtotal:</strong></span> <span class="amount">1 × <span><span class="currency">$</span>940.00</span></span>
                            </div>
                        </div>
                        <div class="cart-button-box">
                            <a href="checkout.html" class="theme-btn style-one">Proceed to checkout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--====== End Sidemenu-wrapper-cart Area ======-->
          <!--===  Header Navigation  ===-->
        <div class="header-navigation style-one">
            <div class="container">
                <!--=== Primary Menu ===-->
                <div class="primary-menu">
                    <div class="site-branding d-lg-none d-block">
                        <a href="index.html" class="brand-logo"><img src="assets/images/logo/logo.jpg" alt="Logo"></a>
                    </div>
                    <!--=== Nav Inner Menu ===-->
                    <div class="nav-inner-menu">
                        <!--=== Main Category ===-->
                        <div class="main-categories-wrap d-none d-lg-block">
                            <a class="categories-btn-active" href="#">
                                <span class="fas fa-list"></span>
                                <span class="text">Product Categories <i class="fas fa-angle-down"></i></span>
                            </a>

                            <div class="categories-dropdown-wrap categories-dropdown-active">
                                <div class="categori-dropdown-item">
                                    <ul>
                                        <li><a href="shops.html"><img src="assets/images/icon/unsuited.png"
                                                    alt="Unstitched">Unstitched Suits</a></li>
                                        <li><a href="shops.html"><img src="assets/images/icon/suited.png"
                                                    alt="Stitched">Stitched suite</a></li>
                                        <li><a href="shops.html"><img src="assets/images/icon/chapal.png"
                                                    alt="Footwear">Casual Slippers</a></li>
                                        <li><a href="shops.html"><img src="assets/images/icon/foot.png"
                                                    alt="Fancy Footwear">Fancy Footwear</a></li>
                                    </ul>
                                </div>

                                <div class="more_slide_open">
                                    <div class="categori-dropdown-item">
                                        <ul>
                                            <li><a href="shops.html"><img src="assets/images/icon/parras.png"
                                                        alt="Luxury">Handbags</a></li>
                                        </ul>
                                    </div>
                                </div>

                                <div class="more_categories"><span class="icon"></span> <span>Show more...</span></div>
                            </div>
                        </div>

                        <!--=== Pesco Nav Main ===-->
                        <div class="pesco-nav-main">
                            <!--=== Pesco Nav Menu ===-->
                            <div class="pesco-nav-menu">
                                <!--=== Responsive Menu Search ===-->
                                <div class="nav-search mb-40 d-block d-lg-none">
                                    <div class="form-group">
                                        <input type="search" class="form_control" placeholder="Search Here"
                                            name="search">
                                        <button class="search-btn"><i class="far fa-search"></i></button>
                                    </div>
                                </div>
                                <!--=== Responsive Menu Tab ===-->
                                <div class="pesco-tabs style-three d-block d-lg-none">
                                    <ul class="nav nav-tabs mb-30" role="tablist">
                                        <li>
                                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav1"
                                                role="tab">Menu</button>
                                        </li>
                                        <li>
                                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav2"
                                                role="tab">Category</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane fade show active" id="nav1">
                                            <nav class="main-menu">
                                                <ul>
                                                    <li><a href="index.html">Home</a></li>
                                                    <li><a href="about-us.html">About Us</a></li>
                                                    <li class="menu-item has-children"><a href="#">Products</a>
                                                        <ul class="sub-menu">
                                                            <li><a href="shops.html">All Products</a></li>
                                                            <li><a href="unstitched.html">Unstitched Collection</a></li>
                                                            <li><a href="stitched.html">Stitched Collection</a></li>
                                                            <li><a href="footwear.html">Footwear</a></li>
                                                            <li><a href="handbags.html">Handbags</a></li>
                                                            <li><a href="handbags.html">Casual Slippers</a></li>

                                                            <li><a href="cart.html">Cart</a></li>
                                                            <li><a href="checkout.html">Checkout</a></li>
                                                        </ul>
                                                    </li>
                                                    <li><a href="faq.html">FAQs</a></li>

                                                    <li><a href="contact.html">Contact</a></li>
                                                </ul>
                                            </nav>
                                        </div>
                                        <div class="tab-pane fade" id="nav2">
                                            <div class="categori-dropdown-item">
                                                <ul>
                                                    <li><a href="shops.html"><img src="assets/images/icon/unsuited.png"
                                                                alt="Unstitched">Unstitched Suits</a></li>
                                                    <li><a href="shops.html"><img src="assets/images/icon/suited.png"
                                                                alt="Stitched">Stitched suite</a></li>
                                                    <li><a href="shops.html"><img src="assets/images/icon/chapal.png"
                                                                alt="Footwear">Casual Slippers</a></li>
                                                    <li><a href="shops.html"><img src="assets/images/icon/foot.png"
                                                                alt="Fancy Footwear">Fancy Footwear</a></li>
                                                    <li><a href="shops.html"><img src="assets/images/icon/parras.png"
                                                                alt="Luxury">Handbags</a></li>
                                                </ul>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <!--===  Hotline Support  ===-->
                                <div class="hotline-support d-flex d-lg-none mt-30">
                                    <div class="icon">
                                        <i class="flaticon-support"></i>
                                    </div>
                                    <div class="info">
                                        <span>24/7 Support</span>
                                        <h5><a href="tel:+923462744165">+923462744165</a></h5>
                                    </div>
                                </div>
                                <!--=== Main Menu ===-->
                                <nav class="main-menu d-none d-lg-block">
                                    <ul>
                                        <li><a href="index.html">Home</a></li>
                                        <li><a href="about-us.html">About Us</a></li>
                                        <li class="menu-item has-children"><a href="#">Products</a>
                                            <ul class="sub-menu">
                                                <li><a href="shops.html">All Products</a></li>
                                                <li><a href="unstitched.html">Unstitched Collection</a></li>
                                                <li><a href="stitched.html">Stitched Collection</a></li>
                                                <li><a href="footwear.html">Footwear</a></li>
                                                <li><a href="handbags.html">Handbags</a></li>
                                                <li><a href="handbags.html">Casual Slippers</a></li>

                                                <li><a href="cart.html">Cart</a></li>
                                                <li><a href="checkout.html">Checkout</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="faq.html">FAQs</a></li>

                                        <li><a href="contact.html">Contact</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <!--=== Nav Right Item ===-->
                    <div class="nav-right-item style-one">
                        <ul>
                            <li>
                                <div class="deals d-lg-block d-none"><i class="far fa-fire-alt"></i>Deal</div>
                            </li>
                            <li>
                                <div class="wishlist-btn d-lg-block d-none"><i class="far fa-heart"></i><span
                                        class="pro-count">0</span></div>
                            </li>
                            <li>
                                <div class="cart-button d-flex align-items-center">
                                    <div class="icon">
                                        <i class="fas fa-shopping-bag"></i><span class="pro-count">0</span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <div class="navbar-toggler d-block d-lg-none">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header><!--====== End Header Section ======-->
    <main class="main-bg">
        <section class="page-banner">
            <div class="page-banner-wrapper p-r z-1">
                <svg class="lineanm" viewBox="0 0 1920 347" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line" d="M-39 345.187C70 308.353 397.628 293.477 436 145.186C490 -63.5 572 -57.8156 688 255.186C757.071 441.559 989.5 -121.315 1389 98.6856C1708.6 274.686 1940.33 156.519 1964.5 98.6856" stroke="white" stroke-width="3" stroke-dasharray="2 2"/>
                </svg>
                <div class="page-image"><img src="assets/images/bg/page-img-1.png" alt="image"></div>
                <svg class="page-svg" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.1742 33.0065C14.029 35.2507 7.5486 39.0636 0 40.7339V86H1937V64.9942C1933.1 60.1623 1912.65 65.1777 1904.51 62.6581C1894.22 59.4678 1884.93 55.0079 1873.77 52.7742C1861.2 50.2585 1823.41 36.3854 1811.99 39.9252C1805.05 42.0727 1796.94 37.6189 1789.36 36.6007C1769.18 33.8879 1747.19 31.1848 1726.71 29.7718C1703.81 28.1919 1678.28 27.0012 1657.53 34.4442C1636.45 42.005 1606.07 60.856 1579.5 55.9191C1561.6 52.5906 1543.41 47.0959 1528.45 56.9075C1510.85 68.4592 1485.74 74.2518 1460.44 76.136C1432.32 78.2297 1408.53 70.6879 1384.73 62.2987C1339.52 46.361 1298.19 27.1677 1255.08 9.28534C1242.58 4.10111 1214.68 15.4762 1200.55 16.6533C1189.77 17.5509 1181.74 15.4508 1172.12 12.8795C1152.74 7.70033 1133.23 2.88525 1111.79 2.63621C1088.85 2.36971 1073.94 7.88289 1056.53 15.8446C1040.01 23.3996 1027.48 26.1777 1007.8 26.1777C993.757 26.1777 975.854 25.6887 962.844 28.9632C941.935 34.2258 932.059 38.7874 914.839 28.6037C901.654 20.8061 866.261 -2.56499 844.356 7.12886C831.264 12.9222 820.932 21.5146 807.663 27.5255C798.74 31.5679 779.299 42.0561 766.33 39.1166C758.156 37.2637 751.815 31.6349 745.591 28.2443C730.967 20.2774 715.218 13.2948 695.846 10.723C676.168 8.11038 658.554 23.1787 641.606 27.4357C617.564 33.4742 602.283 27.7951 579.244 27.7951C568.142 27.7951 548.414 30.4002 541.681 23.6618C535.297 17.2722 530.162 9.74921 523.263 3.71444C517.855 -1.01577 505.798 -0.852017 498.318 2.09709C479.032 9.7007 453.07 10.0516 431.025 9.64475C407.556 9.21163 368.679 1.61612 346.618 10.3636C319.648 21.0575 291.717 53.8338 254.67 45.2266C236.134 40.9201 225.134 37.5813 204.78 40.7339C186.008 43.6415 171.665 50.7785 156.051 57.3567C146.567 61.3523 152.335 52.6281 151.12 47.9222C149.535 41.7853 139.994 34.5585 132.991 30.4008C120.206 22.8098 90.2848 24.3246 74.2546 24.6502C55.5552 25.0301 37.9201 27.747 21.1742 33.0065Z" fill="#FFFAF3"/>
                </svg>
                <div class="shape shape-one"><span></span></div>
                <div class="shape shape-two"><span></span></div>
                <div class="shape shape-three"><span><img src="assets/images/shape/curved-arrow.png" alt=""></span></div>
                <div class="shape shape-four"><span><img src="assets/images/shape/stars.png" alt=""></span></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-banner-content">
                                <h1>Shop Details</h1>
                                <ul class="breadcrumb-link">
                                    <li><a href="index.html">Home</a></li>
                                    <li><i class="far fa-long-arrow-right"></i></li>
                                    <li class="active">Shop Details</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="shop-details-section pt-120 pb-80">
            <div class="container">
                <?php if (isset($wishlist_message)): ?>
                    <div class="alert alert-success"><?php echo $wishlist_message; ?></div>
                <?php endif; ?>
                <div class="shop-details-wrapper">
                    <div class="row">
                        <div class="col-xl-6">
                            <div class="product-gallery-area mb-50" data-aos="fade-up" data-aos-duration="1200">
                                <div class="product-big-slider mb-30">
                                    <?php foreach ($images as $image): ?>
                                        <div class="product-img">
                                            <a href="<?= htmlspecialchars($image['image_url']) ?>" class="img-popup">
                                                <img src="<?= htmlspecialchars($image['image_url']) ?>" alt="Product">
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="product-thumb-slider">
                                    <?php foreach ($images as $image): ?>
                                        <div class="product-img">
                                            <img src="<?= htmlspecialchars($image['image_url']) ?>" alt="Product">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6">
                            <div class="product-info mb-50" data-aos="fade-up" data-aos-duration="1400">
                                <?php if ($product['discount_price']): ?>
                                    <span class="sale"><i class="fas fa-tags"></i>
                                        <?= round((($product['price'] - $product['discount_price']) / $product['price']) * 100) ?>% OFF
                                    </span>
                                <?php endif; ?>
                                <h4 class="title"><?= htmlspecialchars($product['title']) ?></h4>
                                <ul class="ratings rating<?= $product['rating'] ?>">
                                    <?php for ($i = 0; $i < $product['rating']; $i++): ?>
                                        <li><i class="fas fa-star"></i></li>
                                    <?php endfor; ?>
                                    <li><a href="#reviews">(<?= $product['reviews'] ?> Reviews)</a></li>
                                </ul>
                                <p><?= htmlspecialchars($description) ?></p>
                                <div class="product-price">
                                    <span class="price prev-price"><span class="currency">$</span><?= number_format($product['price'], 2) ?></span>
                                    <?php if ($product['discount_price']): ?>
                                        <span class="price new-price"><span class="currency">$</span><?= number_format($product['discount_price'], 2) ?></span>
                                    <?php endif; ?>
                                </div>
                                <form id="product-form" method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    <input type="hidden" name="title" value="<?= htmlspecialchars($product['title']) ?>">
                                    <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
                                    <input type="hidden" name="price" value="<?= $product['discount_price'] ?: $product['price'] ?>">
                                    <div class="product-color">
                                        <h4 class="mb-15">Color</h4>
                                        <ul class="color-list mb-20">
                                            <?php foreach ($colors as $index => $color): ?>
                                                <li>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="color" value="<?= htmlspecialchars($color['color']) ?>" id="color<?= $index + 1 ?>" required>
                                                        <label class="form-check-label" for="color<?= $index + 1 ?>">
                                                            <span class="color<?= $index + 1 ?>" style="background: <?= htmlspecialchars($color['color']) ?>;"></span>
                                                        </label>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div class="product-size">
                                        <h4 class="mb-15">Size</h4>
                                        <ul class="size-list mb-30">
                                            <?php foreach ($sizes as $index => $size): ?>
                                                <li>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="size" value="<?= htmlspecialchars($size['size']) ?>" id="size<?= $index + 2 ?>" required>
                                                        <label class="form-check-label" for="size<?= $index + 2 ?>">
                                                            <?= htmlspecialchars($size['size']) ?>
                                                        </label>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div class="product-cart-variation">
                                        <ul>
                                            <li>
                                                <div class="quantity-input">
                                                    <button type="button" class="quantity-down"><i class="far fa-minus"></i></button>
                                                    <input class="quantity" type="text" value="1" name="quantity">
                                                    <button type="button" class="quantity-up"><i class="far fa-plus"></i></button>
                                                </div>
                                            </li>
                                            <li>
                                                <button type="submit" class="theme-btn style-one">Add To Cart</button>
                                            </li>
                                            <li>
                                                <form method="POST">
                                                    <input type="hidden" name="add_to_wishlist" value="1">
                                                    <input type="hidden" name="title" value="<?= htmlspecialchars($product['title']) ?>">
                                                    <input type="hidden" name="image" value="<?= htmlspecialchars($product['image']) ?>">
                                                    <input type="hidden" name="price" value="<?= $product['discount_price'] ?: $product['price'] ?>">
                                                    <button type="submit" class="icon-btn <?= in_array($product_id, array_column($_SESSION['wishlist'], 'id')) ? 'wishlist-added' : '' ?>">
                                                        <i class="far fa-heart"></i>
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <a href="#" class="icon-btn"><i class="far fa-sync"></i></a>
                                            </li>
                                        </ul>
                                    </div>
                                </form>
                                <!-- < addicted to cart message --> 
                                <?php if (isset($_SESSION['cart_message'])): ?>
                                    <div class="alert alert-success"><?php echo $_SESSION['cart_message']; unset($_SESSION['cart_message']); ?></div>
                                <?php endif; ?>
                                <div class="product-meta">
                                    <ul>
                                        <li><span>SKU :</span><?= htmlspecialchars($product['sku'] ?? 'N/A') ?></li>
                                        <li><span>Category :</span><?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></li>
                                        <li><span>Brand :</span><?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></li>
                                        <li><span>Tags :</span>
                                            <?php
                                            $tags = explode(',', $product['tags'] ?? '');
                                            foreach ($tags as $tag): ?>
                                                <a href="#"><?= htmlspecialchars(trim($tag)) ?></a>,
                                            <?php endforeach; ?>
                                        </li>
                                        <li><span>Share :</span>
                                            <a href="#"><i class="fab fa-facebook-f"></i></a>
                                            <a href="#"><i class="fab fa-linkedin-in"></i></a>
                                            <a href="#"><i class="fab fa-instagram"></i></a>
                                            <a href="#"><i class="fab fa-twitter"></i></a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="special-features">
                                    <span><i class="far fa-shipping-fast"></i>Free Shipping</span>
                                    <span><i class="far fa-box-open"></i>Easy Returns</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="additional-information-wrapper" data-aos="fade-up" data-aos-delay="30" data-aos-duration="1000">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="additional-info-box mb-40">
                                    <h3>Additional Information:</h3>
                                    <ul>
                                        <li>Fabric type <span><?= htmlspecialchars($product['fabric_type'] ?? 'N/A') ?></span></li>
                                        <li>Care instructions: <span><?= htmlspecialchars($product['care_instructions'] ?? 'N/A') ?></span></li>
                                        <li>Occasion type: <span><?= htmlspecialchars($product['occasion_type'] ?? 'N/A') ?></span></li>
                                        <li>Sleeve type: <span><?= htmlspecialchars($product['sleeve_type'] ?? 'N/A') ?></span></li>
                                        <li>Pattern: <span><?= htmlspecialchars($product['pattern'] ?? 'N/A') ?></span></li>
                                        <li>Closure type: <span><?= htmlspecialchars($product['closure_type'] ?? 'N/A') ?></span></li>
                                        <li>Country of Origin: <span><?= htmlspecialchars($product['country_of_origin'] ?? 'N/A') ?></span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-lg-7">
                                <div class="description-wrapper mb-40">
                                    <div class="pesco-tabs style-two mb-50">
                                        <ul class="nav nav-tabs">
                                            <li><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#description">Description</button></li>
                                            <li><button class="nav-link" data-bs-toggle="tab" data-bs-target="#reviews">Reviews</button></li>
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        <div class="tab-pane fade active show" id="description">
                                            <h4>Description</h4>
                                            <p><?= htmlspecialchars($description) ?></p>
                                            <h4>Features</h4>
                                            <ul class="list">
                                                <?php
                                                $features = explode(',', $product['features'] ?? '');
                                                foreach ($features as $feature): ?>
                                                    <li><?= htmlspecialchars(trim($feature)) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="reviews">
                                            <div class="pesco-comment-area mb-80">
                                                <h4>Total Reviews (<?= count($reviews) ?>)</h4>
                                                <ul>
                                                    <?php foreach ($reviews as $review): ?>
                                                        <li class="comment">
                                                            <div class="pesco-reviews-item">
                                                                <div class="author-thumb-info">
                                                                    <div class="author-thumb">
                                                                        <img src="assets/images/products/review-1.jpg" alt="Author">
                                                                    </div>
                                                                    <div class="author-info">
                                                                        <h5><?= htmlspecialchars($review['user_name']) ?></h5>
                                                                        <div class="author-meta">
                                                                            <ul class="ratings">
                                                                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                                                                    <li><i class="fas fa-star"></i></li>
                                                                                <?php endfor; ?>
                                                                            </ul>
                                                                            <span><?= date('d M Y', strtotime($review['created_at'])) ?></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="author-review-content">
                                                                    <p><?= htmlspecialchars($review['comment']) ?></p>
                                                                </div>
                                                                <a href="#" class="reply"><i class="fas fa-reply-all"></i> Reply</a>
                                                            </div>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <div class="reviews-contact-area">
                                                <h4>Write Comment</h4>
                                                <ul class="ratings rating5 mb-40">
                                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                                        <li><i class="fas fa-star"></i></li>
                                                    <?php endfor; ?>
                                                    <li><a href="#">(<?= count($reviews) ?>)</a></li>
                                                </ul>
                                                <form class="pesco-contact-form" method="POST" action="submit-review.php">
                                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <input type="text" placeholder="Name" class="form_control" name="name" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="form-group">
                                                                <input type="email" placeholder="Email" class="form_control" name="email" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="form-group">
                                                                <textarea class="form_control" placeholder="Write Reviews" name="comment" cols="5" rows="10" required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="form-group">
                                                                <button class="theme-btn style-one">Submit Review</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="releted-product-section pb-90">
            <div class="container">
                <div class="row">
                    <div class="col-md-8">
                        <div class="section-title mb-50" data-aos="fade-right" data-aos-delay="50" data-aos-duration="1000">
                            <div class="sub-heading d-inline-flex align-items-center">
                                <i class="flaticon-sparkler"></i>
                                <span class="sub-title">Related Products</span>
                            </div>
                            <h2>Customers also purchased</h2>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="releted-product-arrows style-one mb-50" data-aos="fade-left" data-aos-delay="70" data-aos-duration="1300"></div>
                    </div>
                </div>
                <div class="releted-product-slider">
                    <?php foreach ($related_products as $related): ?>
                        <div class="product-item style-one mb-40" data-aos="fade-up" data-aos-delay="90" data-aos-duration="1500">
                            <a href="product-details.php?id=<?= $related['id'] ?>" class="product-link">
                                <div class="product-thumbnail">
                                    <img src="<?= htmlspecialchars($related['image']) ?>" alt="<?= htmlspecialchars($related['title']) ?>">
                                    <?php if ($related['discount_price']): ?>
                                        <div class="discount">
                                            <?= round((($related['price'] - $related['discount_price']) / $related['price']) * 100) ?>% Off
                                        </div>
                                    <?php endif; ?>
                                    <div class="hover-content">
                                        <form method="POST" action="product-details.php?id=<?= $related['id'] ?>">
                                            <input type="hidden" name="add_to_wishlist" value="1">
                                            <input type="hidden" name="title" value="<?= htmlspecialchars($related['title']) ?>">
                                            <input type="hidden" name="image" value="<?= htmlspecialchars($related['image']) ?>">
                                            <input type="hidden" name="price" value="<?= $related['discount_price'] ?: $related['price'] ?>">
                                            <button type="submit" class="icon-btn <?= in_array($related['id'], array_column($_SESSION['wishlist'], 'id')) ? 'wishlist-added' : '' ?>">
                                                <i class="fa fa-heart"></i>
                                            </button>
                                        </form>
                                        <span class="img-popup icon-btn"><i class="fa fa-eye"></i></span>
                                    </div>
                                    <div class="cart-button">
                                        <a href="cart.php?product_id=<?= $related['id'] ?>" class="cart-btn"><i class="far fa-shopping-basket"></i> <span class="text">Add To Cart</span></a>
                                    </div>
                                </div>
                                <div class="product-info-wrap">
                                    <div class="product-info">
                                        <ul class="ratings rating<?= $related['rating'] ?>">
                                            <?php for ($i = 0; $i < $related['rating']; $i++): ?>
                                                <li><i class="fas fa-star"></i></li>
                                            <?php endfor; ?>
                                            <li><a href="product-details.php?id=<?= $related['id'] ?>">(<?= $related['reviews'] ?>)</a></li>
                                        </ul>
                                        <h4 class="title"><a href="product-details.php?id=<?= $related['id'] ?>"><?= htmlspecialchars($related['title']) ?></a></h4>
                                    </div>
                                    <div class="product-price">
                                        <span class="price prev-price"><span class="currency">$</span><?= number_format($related['price'], 2) ?></span>
                                        <?php if ($related['discount_price']): ?>
                                            <span class="price new-price"><span class="currency">$</span><?= number_format($related['discount_price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <section class="newsletter-section pb-95">
            <div class="container">
                <div class="newsletter-wrapper white-bg p-r z-1" data-aos="fade-up" data-aos-duration="1000">
                    <div class="newsletter-shape pattern-one"><span><img src="assets/images/newsletter/pattern-1.png" alt="Pattern Shape"></span></div>
                    <div class="newsletter-shape pattern-two"><span><img src="assets/images/newsletter/pattern-2.png" alt="Pattern Shape"></span></div>
                    <div class="newsletter-shape shape-one"><span><img src="assets/images/newsletter/shape-1.png" alt="Shape"></span></div>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="newsletter-content-box">
                                <span class="sub-text">Our Newsletter</span>
                                <h3>Get weekly update. Sign up and get up to <span>20% off</span> your first purchase</h3>
                                <form>
                                    <div class="form-group">
                                        <input type="email" class="form_control" placeholder="Write your Email Address" name="email">
                                        <button class="theme-btn style-one">Subscribe</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="newsletter-image">
                                <img src="assets/images/newsletter/newsletter-1.png" alt="Image">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer class="footer-main">
        <div class="footer-bg-wrapper gray-bg">
            <div class="footer-shape shape-one"><span><img src="assets/images/footer/shape-1.png" alt="shape"></span></div>
            <svg id="footer-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1920 75" fill="none">
                <path d="M1888.99 40.9061C1901.65 33.5506 1917.87 10.0999 1920 0.000160217L2.48878 0.110695C-18.5686 5.37782 100.829 31.8098 104.136 32.5745C126.908 37.8407 182.163 45.7157 196.02 59.5798C199.049 62.6106 214.802 72.2205 222.15 72.2205C228.696 72.2205 237.893 62.3777 241.388 59.5798C254.985 48.6964 317.621 62.748 338.154 55.5577C378.089 41.5729 396.6 21.3246 452.148 27.4033C469.55 29.3076 497.787 39.4201 516.467 36.022C529.695 33.6155 539.612 26.7953 554.369 23.9558C576.978 19.6057 584.786 12.6555 612.371 13.0388C629.18 13.2724 648.084 27.6499 658.6 33.8673C672.059 41.8242 673.268 47.0554 692.77 41.4805C711.954 35.9964 746.756 38.27 766.852 40.0441C779.483 41.1593 819.866 52.3111 831.458 47.8009C837.236 45.5528 840.64 43.5162 847.537 41.3369C869.486 34.402 905.397 34.0022 929.946 38.6077C947.224 41.8489 987.666 45.9365 999.721 52.9722C1005.16 56.1489 1004.78 60.6539 1010.35 63.6019C1018.09 67.7037 1021.56 68.3083 1029.01 67.4803C1042.77 65.9505 1045.29 61.7272 1056.86 58.1434C1090.94 47.59 1121.71 32.7536 1160.52 26.5415C1182.98 22.9457 1193.92 36.1401 1209.04 41.4806C1240.16 52.468 1262.92 57.9972 1299.78 49.2374C1331.73 41.6466 1369.13 23.3813 1405.73 23.3813C1419.55 23.3813 1427.96 32.734 1435.31 37.4585C1451.38 47.7919 1467 56.9943 1493.89 56.9943C1532.36 56.9943 1544.2 49.9853 1574.29 39.0386C1588.58 33.8384 1616.86 22.826 1635.73 23.3813C1651.4 23.8424 1656.97 43.603 1667.89 48.6629C1683.26 55.7835 1710.61 49.5903 1723.88 43.7789C1736.22 38.3771 1758.43 20.6985 1777.29 30.1327C1788.48 35.7274 1794.71 53.9926 1801.12 61.5909C1815.62 78.7687 1819.96 77.5598 1843.05 68.4859C1861.58 61.2028 1873.63 49.8315 1888.99 40.9061Z" fill="#FFFAF3"/>
            </svg>
            <div class="footer-widget-area pb-80">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-3 col-sm-6">
                            <div class="footer-widget about-company-widget mb-40" data-aos="fade-up" data-aos-delay="10" data-aos-duration="1000">
                                <div class="widget-content">
                                    <a href="index.html" class="footer-logo"><img src="assets/images/logo/logo-main.png" alt="Brand Logo"></a>
                                    <p>Pesco is an exciting International brand we provide high quality cloths</p>
                                    <ul class="ct-info-list mb-30">
                                        <li><i class="fas fa-envelope"></i><a href="mailto:info@mydomain.com">info@mydomain.com</a></li>
                                        <li><i class="fas fa-phone-alt"></i><a href="mailto:info@mydomain.com">info@mydomain.com</a></li>
                                    </ul>
                                    <ul class="social-link">
                                        <li><span>Find Us:</span></li>
                                        <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                                        <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                                        <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                                        <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="footer-widget footer-nav-widget mb-40" data-aos="fade-up" data-aos-delay="15" data-aos-duration="1200">
                                <div class="widget-content">
                                    <h4 class="widget-title">Customer Services</h4>
                                    <ul class="widget-menu">
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Collections & Delivery</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Returns & Refunds</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Terms & Conditions</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Delivery Return</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Store Locations</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6 col-sm-6">
                            <div class="footer-widget footer-nav-widget mb-40" data-aos="fade-up" data-aos-delay="20" data-aos-duration="1400">
                                <div class="widget-content">
                                    <h4 class="widget-title">Quick Link</h4>
                                    <ul class="widget-menu">
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Privacy Policy</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Terms Of Use</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">FAQ</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Contact</a></li>
                                        <li><img src="assets/images/icon/star-3.svg" alt="star icon"><a href="#">Login / Register</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-sm-6">
                            <div class="footer-widget footer-recent-post-widget mb-40" data-aos="fade-up" data-aos-delay="25" data-aos-duration="1600">
                                <h4 class="widget-title">Recent Post</h4>
                                <div class="widget-content">
                                    <div class="recent-post-item">
                                        <div class="thumb">
                                            <img src="assets/images/footer/recent-post-1.png" alt="post thumb">
                                        </div>
                                        <div class="content">
                                            <h4><a href="blog-details.html">Tips on Finding Affordable Fashion Gems Online</a></h4>
                                            <span><a href="blog-details.html">July 11, 2023</a></span>
                                        </div>
                                    </div>
                                    <div class="recent-post-item">
                                        <div class="thumb">
                                            <img src="assets/images/footer/recent-post-2.png" alt="post thumb">
                                        </div>
                                        <div class="content">
                                            <h4><a href="blog-details.html">Mastering the Art of Fashion E-commerce Marketing</a></h4>
                                            <span><a href="blog-details.html">JUly 11, 2024</a></span>
                                        </div>
                                    </div>
                                    <div class="recent-post-item">
                                        <div class="thumb">
                                            <img src="assets/images/footer/recent-post-3.png" alt="post thumb">
                                        </div>
                                        <div class="content">
                                            <h4><a href="blog-details.html">Must-Have Trends You Can Shop Online Now</a></h4>
                                            <span><a href="blog-details.html">July 11, 2024</a></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="copyright-area">
                    <div class="container">
                        <div class="row align-items-center">
                            <div class="col-lg-6">
                                <div class="copyright-text">
                                    <p>&copy; 2024. All rights reserved by <span>Pixelfit</span></p>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="payment-method text-lg-end">
                                    <a href="#"><img src="assets/images/footer/payment-method.png" alt="payment-method"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <div class="back-to-top"><i class="far fa-angle-up"></i></div>
        <script src="assets/vendor/jquery-3.7.1.min.js"></script>
        <script src="assets/vendor/popper/popper.min.js"></script>
        <script src="assets/vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="assets/vendor/slick/slick.min.js"></script>
        <script src="assets/vendor/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
        <script src="assets/vendor/nice-select/js/jquery.nice-select.min.js"></script>
        <script src="assets/vendor/jquery-ui/jquery-ui.min.js"></script>
        <script src="assets/vendor/simplyCountdown.min.js"></script>
        <script src="assets/vendor/aos/aos.js"></script>
        <script src="assets/js/theme.js"></script>
        <script>
            $(document).ready(function(){
                $('.product-big-slider').slick({
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    arrows: true,
                    fade: true,
                    asNavFor: '.product-thumb-slider'
                });
                $('.product-thumb-slider').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    asNavFor: '.product-big-slider',
                    focusOnSelect: true,
                    arrows: false
                });
                $('.releted-product-slider').slick({
                    slidesToShow: 4,
                    slidesToScroll: 1,
                    arrows: true,
                    infinite: true,
                    autoplay: true,
                    autoplaySpeed: 2000,
                    responsive: [
                        { breakpoint: 768, settings: { slidesToShow: 2 } },
                        { breakpoint: 576, settings: { slidesToShow: 1 } }
                    ]
                });
                $('.quantity-down').click(function() {
                    let input = $(this).siblings('.quantity');
                    let value = parseInt(input.val());
                    if (value > 1) input.val(value - 1);
                });
                $('.quantity-up').click(function() {
                    let input = $(this).siblings('.quantity');
                    let value = parseInt(input.val());
                    input.val(value + 1);
                });
                $('#product-form').submit(function(e) {
                    if (!$('input[name="color"]:checked').length || !$('input[name="size"]:checked').length) {
                        e.preventDefault();
                        alert('Please select a color and size.');
                    }
                });
            });
        </script>
    </body>
</html>