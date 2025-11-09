<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';
include 'include/cart-functions.php';

// Validate product ID
if (!isset($_GET['id']) || (int)$_GET['id'] <= 0) {
    header("Location: shops.php");
    exit;
}

$product_id = (int)$_GET['id'];

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// --- Fetch Product Details ---
$product = [];
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
if ($stmt) {
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc() ?: [];
    $stmt->close();
}

// Fallback if product not found
if (empty($product)) {
    $product = [
        'title' => 'Sample Product',
        'description' => 'Description not available.',
        'price' => 1000,
        'discount_price' => null,
        'image' => 'assets/images/default-product.png',
        'category_name' => 'Uncategorized',
        'brand_name' => 'No Brand',
        'category_id' => 0,
        'tags' => 'eCommerce,shop,fashion',
    ];
}

// Use detailed description if available
$description = $product['pd_description'] ?? $product['description'] ?? 'No description available.';

// --- Fetch Product Images ---
$images = [];
$images_stmt = $conn->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
if ($images_stmt) {
    $images_stmt->bind_param("i", $product_id);
    $images_stmt->execute();
    $images = $images_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $images_stmt->close();
}
if (empty($images)) {
    $images[] = ['image_url' => $product['image'] ?? 'assets/images/default-product.png'];
}

// --- Fetch Colors ---
$colors = [];
$colors_stmt = $conn->prepare("SELECT color FROM product_colors WHERE product_id = ?");
if ($colors_stmt) {
    $colors_stmt->bind_param("i", $product_id);
    $colors_stmt->execute();
    $colors = $colors_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $colors_stmt->close();
}
if (empty($colors)) {
    $colors = [['color'=>'black'], ['color'=>'red'], ['color'=>'blue'], ['color'=>'green']];
}

// --- Fetch Sizes ---
$sizes = [];
$sizes_stmt = $conn->prepare("SELECT size FROM product_sizes WHERE product_id = ?");
if ($sizes_stmt) {
    $sizes_stmt->bind_param("i", $product_id);
    $sizes_stmt->execute();
    $sizes = $sizes_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $sizes_stmt->close();
}
if (empty($sizes)) {
    if (in_array($product['category_id'], [3,5])) { // Footwear or Slippers
        $sizes = [['size'=>'36/3'], ['size'=>'37/4'], ['size'=>'38/5'], ['size'=>'39/6'], ['size'=>'40/7']];
    } else {
        $sizes = [['size'=>'S'], ['size'=>'M'], ['size'=>'L'], ['size'=>'XL'], ['size'=>'2XL']];
    }
}

// --- Fetch Reviews ---
$reviews = [];
$reviews_stmt = $conn->prepare("SELECT user_name, rating, comment, created_at FROM reviews WHERE product_id = ?");
if ($reviews_stmt) {
    $reviews_stmt->bind_param("i", $product_id);
    $reviews_stmt->execute();
    $reviews = $reviews_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $reviews_stmt->close();
}

// --- Fetch Related Products ---
$related_products = [];
$related_stmt = $conn->prepare("
    SELECT p.*, b.name AS brand_name, c.name AS category_name
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.id
    LEFT JOIN categories c ON p.category_id = c.id
    WHERE p.category_id = ? AND p.id != ? LIMIT 8
");
if ($related_stmt) {
    $related_stmt->bind_param("ii", $product['category_id'], $product_id);
    $related_stmt->execute();
    $related_products = $related_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $related_stmt->close();
}

// Fallback: if less than 4 related, get popular
if (count($related_products) < 4) {
    $limit = 8 - count($related_products);
    $popular_stmt = $conn->prepare("
        SELECT p.*, b.name AS brand_name, c.name AS category_name
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.id
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE p.id != ? ORDER BY p.reviews DESC LIMIT ?
    ");
    if ($popular_stmt) {
        $popular_stmt->bind_param("ii", $product_id, $limit);
        $popular_stmt->execute();
        $popular_products = $popular_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $related_products = array_merge($related_products, $popular_products);
        $popular_stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="<?= htmlspecialchars(substr($description, 0, 160)) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($product['tags'] ?? $product['category_name'] ?? 'eCommerce,shop,fashion') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($product['title']) ?> - Product Details</title>
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
    <link rel="stylesheet" href="include/custom.css">
</head>
<body>
    <!--====== Preloader ======-->
    <div class="preloader">
        <div class="loader">
            <img src="assets/images/loader.gif" alt="Loader">
        </div>
    </div>

    <!--====== Toast for Success/Error Messages ======-->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="toast align-items-center text-white bg-success border-0 position-fixed bottom-0 end-0 m-3" role="alert" id="success-toast">
            <div class="d-flex">
                <div class="toast-body"><?php echo $_SESSION['message']; ?></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    <div class="toast align-items-center text-white bg-danger border-0 position-fixed bottom-0 end-0 m-3" role="alert" id="error-toast">
        <div class="d-flex">
            <div class="toast-body">Please select a color and size!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>

    <!--====== Start Overlay ======-->
    <?php include 'include/sidecart.php' ?>
    <?php include 'include/wishlistcart.php' ?>

    <!--====== Start Header Section ======-->
    <header class="header-area">
        <?php include 'include/header.php'; ?>
        <?php include 'include/nav.php'; ?>
    </header>
    <!--====== End Header Section ======-->
   
    <main class="main-bg">
        <section class="page-banner">
            <div class="page-banner-wrapper p-r z-1">
                <!-- <svg class="lineanm" viewBox="0 0 1920 347" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line" d="M-39 345.187C70 308.353 397.628 293.477 436 145.186C490 -63.5 572 -57.8156 688 255.186C757.071 441.559 989.5 -121.315 1389 98.6856C1708.6 274.686 1940.33 156.519 1964.5 98.6856" stroke="white" stroke-width="3" stroke-dasharray="2 2"/>
                </svg> -->
                <div class="page-image"><img src="assets/images/bg/page-img-1.png" alt="image"></div>
                <!-- <svg class="page-svg" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.1742 33.0065C14.029 35.2507 7.5486 39.0636 0 40.7339V86H1937V64.9942C1933.1 60.1623 1912.65 65.1777 1904.51 62.6581C1894.22 59.4678 1884.93 55.0079 1873.77 52.7742C1861.2 50.2585 1823.41 36.3854 1811.99 39.9252C1805.05 42.0727 1796.94 37.6189 1789.36 36.6007C1769.18 33.8879 1747.19 31.1848 1726.71 29.7718C1703.81 28.1919 1678.28 27.0012 1657.53 34.4442C1636.45 42.005 1606.07 60.856 1579.5 55.9191C1561.6 52.5906 1543.41 47.0959 1528.45 56.9075C1510.85 68.4592 1485.74 74.2518 1460.44 76.136C1432.32 78.2297 1408.53 70.6879 1384.73 62.2987C1339.52 46.361 1298.19 27.1677 1255.08 9.28534C1242.58 4.10111 1214.68 15.4762 1200.55 16.6533C1189.77 17.5509 1181.74 15.4508 1172.12 12.8795C1152.74 7.70033 1133.23 2.88525 1111.79 2.63621C1088.85 2.36971 1073.94 7.88289 1056.53 15.8446C1040.01 23.3996 1027.48 26.1777 1007.8 26.1777C993.757 26.1777 975.854 25.6887 962.844 28.9632C941.935 34.2258 932.059 38.7874 914.839 28.6037C901.654 20.8061 866.261 -2.56499 844.356 7.12886C831.264 12.9222 820.932 21.5146 807.663 27.5255C798.74 31.5679 779.299 42.0561 766.33 39.1166C758.156 37.2637 751.815 31.6349 745.591 28.2443C730.967 20.2774 715.218 13.2948 695.846 10.723C676.168 8.11038 658.554 23.1787 641.606 27.4357C617.564 33.4742 602.283 27.7951 579.244 27.7951C568.142 27.噴7951 548.414 30.4002 541.681 23.6618C535.297 17.2722 530.162 9.74921 523.263 3.71444C517.855 -1.01577 505.798 -0.852017 498.318 2.09709C479.032 9.7007 453.07 10.0516 431.025 9.64475C407.556 9.21163 368.679 1.61612 346.618 10.3636C319.648 21.0575 291.717 53.8338 254.67 45.2266C236.134 40.9201 225.134 37.5813 204.78 40.7339C186.008 43.6415 171.665 50.7785 156.051 57.3567C146.567 61.3523 152.335 52.6281 151.12 47.9222C149.535 41.7853 139.994 34.5585 132.991 30.4008C120.206 22.8098 90.2848 24.3246 74.2546 24.6502C55.5552 25.0301 37.9201 27.747 21.1742 33.0065Z" fill="#FFFAF3"/>
                </svg> -->
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
                                    <li class="active"><?= htmlspecialchars($product['title']) ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!--================= Product Details Modern Section =================-->
        

<section class="product-details-modern py-120">
    <div class="container">
        <div class="row g-5 align-items-start">
            <!-- Left Gallery -->
            <div class="col-lg-6">
                <div class="product-gallery shadow-sm rounded-4 p-3 bg-white" data-aos="fade-right" data-aos-duration="1000">
                    <!-- Main Image -->
                    <div class="main-image position-relative overflow-hidden rounded-3">
                        <img id="main-product-image"
                             src="<?= htmlspecialchars($images[0]['image_url'] ?? 'assets/images/default-product.png') ?>"
                             alt="<?= htmlspecialchars($product['title']) ?>"
                             class="img-fluid w-100 main-img-zoom">
                    </div>
                    <!-- Thumbnails -->
                    <div class="thumb-slider d-flex gap-3 mt-4 justify-content-center">
                        <?php if (!empty($images)): ?>
                            <?php foreach ($images as $index => $image): ?>
                                <img src="<?= htmlspecialchars($image['image_url']) ?>"
                                     class="img-fluid thumb-img border border-2 rounded-3 <?= $index === 0 ? 'active' : '' ?>"
                                     style="width:90px; height:90px; object-fit:cover; cursor:pointer;"
                                     onclick="document.getElementById('main-product-image').src='<?= htmlspecialchars($image['image_url']) ?>'">
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right Info -->
            <div class="col-lg-6">
                <div class="product-info-card shadow-sm rounded-4 p-4 bg-white" data-aos="fade-left" data-aos-duration="1000">
                    <?php if ($product['discount_price']): ?>
                        <span class="badge bg-danger mb-3 px-3 py-2 rounded-pill">
                            <i class="fas fa-tags"></i>
                            <?= round((($product['price'] - $product['discount_price']) / $product['price']) * 100) ?>% OFF
                        </span>
                    <?php endif; ?>

                    <h2 class="fw-bold mb-3"><?= htmlspecialchars($product['title']) ?></h2>

                    <!-- Rating -->
                    <div class="d-flex align-items-center mb-3">
                        <?php for ($i = 0; $i < floor($product['rating'] ?? 0); $i++): ?>
                            <i class="fas fa-star text-warning me-1"></i>
                        <?php endfor; ?>
                        <?php if (($product['rating'] ?? 0) - floor($product['rating'] ?? 0) >= 0.5): ?>
                            <i class="fas fa-star-half-alt text-warning me-1"></i>
                        <?php endif; ?>
                        <span class="ms-2 text-muted">(<?= $product['reviews'] ?? 0 ?> Reviews)</span>
                    </div>

                    <!-- Price -->
                    <div class="product-price mb-4">
                        <?php if ($product['discount_price']): ?>
                            <h3 class="fw-bold text-dark mb-0">₨ <?= number_format($product['discount_price'], 0) ?></h3>
                            <span class="text-muted text-decoration-line-through ms-2 small">
                                ₨ <?= number_format($product['price'], 0) ?>
                            </span>
                        <?php else: ?>
                            <h3 class="fw-bold text-dark">₨ <?= number_format($product['price'], 0) ?></h3>
                        <?php endif; ?>
                    </div>

                    <!-- Add to Cart Form -->
                    <form id="product-form" method="POST" action="cart.php">
                        <input type="hidden" name="product_id" value="<?= $product_id ?>">
                        <input type="hidden" name="title" value="<?= htmlspecialchars($product['title']) ?>">
                        <input type="hidden" name="image" value="<?= htmlspecialchars($images[0]['image_url'] ?? 'assets/images/default-product.png') ?>">
                        <input type="hidden" name="price" value="<?= $product['discount_price'] ?: $product['price'] ?>">

                        <!-- Colors -->
                        <?php if (!empty($colors)): ?>
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-2">Select Color</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php foreach ($colors as $index => $color): ?>
                                        <label class="color-option">
                                            <input type="radio" name="color" value="<?= htmlspecialchars($color['color']) ?>" <?= $index === 0 ? 'checked' : '' ?> required>
                                            <span class="color-circle" style="background: <?= htmlspecialchars($color['color']) ?>;"></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Sizes -->
                        <?php if (!empty($sizes)): ?>
                            <div class="mb-4">
                                <h6 class="fw-semibold mb-2">Select Size</h6>
                                <div class="d-flex gap-2 flex-wrap">
                                    <?php foreach ($sizes as $index => $size): ?>
                                        <label class="size-option">
                                            <input type="radio" name="size" value="<?= htmlspecialchars($size['size']) ?>" <?= $index === 0 ? 'checked' : '' ?> required>
                                            <span><?= htmlspecialchars($size['size']) ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Quantity + Add to Cart -->
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="quantity-input-modern">
                                <button type="button" class="quantity-down"><i class="far fa-minus"></i></button>
                                <input class="quantity" type="number" value="1" name="quantity" style="width:60px; text-align:center;">
                                <button type="button" class="quantity-up"><i class="far fa-plus"></i></button>
                            </div>
                            <button type="submit" class="btn btn-dark px-4 py-2 rounded-pill" id="add-to-cart-btn">
                                <i class="far fa-shopping-cart me-2"></i>Add to Cart
                            </button>
                        </div>
                    </form>

                    <ul class="list-unstyled small text-muted mt-4">
                        <li><b>Category:</b> <?= htmlspecialchars($product['category_name'] ?? 'N/A') ?></li>
                        <li><b>Brand:</b> <?= htmlspecialchars($product['brand_name'] ?? 'N/A') ?></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Full Description Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="product-description shadow-sm rounded-4 p-4 bg-white" id="description">
                    <h3 class="fw-bold mb-3">Product Description</h3>
                    <p><?= htmlspecialchars($description) ?></p>
                    <?php if ($product['fabric_type'] || $product['care_instructions'] || $product['features']): ?>
                        <ul class="list-unstyled">
                            <?php if ($product['fabric_type']): ?><li><b>Fabric:</b> <?= htmlspecialchars($product['fabric_type']) ?></li><?php endif; ?>
                            <?php if ($product['care_instructions']): ?><li><b>Care Instructions:</b> <?= htmlspecialchars($product['care_instructions']) ?></li><?php endif; ?>
                            <?php if ($product['features']): ?><li><b>Features:</b> <?= htmlspecialchars($product['features']) ?></li><?php endif; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.quantity-down').off('click').on('click', function() {
        let input = $(this).siblings('.quantity');
        let value = parseInt(input.val()) || 1;
        value = Math.max(1, value - 1);
        input.val(value);
    });

    $('.quantity-up').off('click').on('click', function() {
        let input = $(this).siblings('.quantity');
        let value = parseInt(input.val()) || 1;
        input.val(value + 1);
    });
});
</script>





        <!--====== Related Products Section ======-->
        <section class="features-products pt-90 pb-60">
            <div class="container">
                <!-- Section Heading -->
                <div class="row align-items-center mb-40">
                    <div class="col-lg-6 col-md-12 text-center text-lg-start mb-3 mb-lg-0">
                        <div class="section-title">
                            <h2 class="title">Related Products</h2>
                            <p class="subtitle">Customers also purchased</p>
                        </div>
                    </div>
                </div>

                <!-- Product Grid -->
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
                    <?php if (!empty($related_products)): ?>
                        <?php foreach ($related_products as $related): ?>
                            <div class="col">
                                <div class="product-card">
                                    <!-- Product Image -->
                                    <div class="product-thumbnail">
                                        <img src="<?= htmlspecialchars($related['image'] ?? 'assets/images/default-product.png') ?>" 
                                             alt="<?= htmlspecialchars($related['title']) ?>">
                                        <?php if (!empty($related['discount_price'])): ?>
                                            <div class="discount-badge">
                                                <?= round((($related['price'] - $related['discount_price']) / $related['price']) * 100) ?>% OFF
                                            </div>
                                        <?php endif; ?>
                                        <div class="hover-content">
                                            <form method="POST" action="product-details.php?id=<?= $related['id'] ?>">
                                                <input type="hidden" name="add_to_wishlist" value="1">
                                                <input type="hidden" name="id" value="<?= $related['id'] ?>">
                                                <input type="hidden" name="title" value="<?= htmlspecialchars($related['title']) ?>">
                                                <input type="hidden" name="image" value="<?= htmlspecialchars($related['image'] ?? 'assets/images/default-product.png') ?>">
                                                <input type="hidden" name="price" value="<?= $related['discount_price'] ?: $related['price'] ?>">
                                                <button type="submit"
                                                        class="icon-btn <?= in_array($related['id'], array_column($_SESSION['wishlist'] ?? [], 'id')) ? 'wishlist-added' : '' ?>"
                                                        title="Add to Wishlist">
                                                    <i class="fa fa-heart"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    <!-- Product Info -->
                                    <div class="product-info-wrap">
                                        <div class="product-info">
                                            <h4 class="title"><a href="product-details.php?id=<?= $related['id'] ?>"><?= htmlspecialchars($related['title']) ?></a></h4>
                                            <p><?= htmlspecialchars(substr($related['description'] ?? 'Stylish and high-quality product.', 0, 100)) ?>...</p>
                                        </div>
                                        <!-- Product Price -->
                                        <div class="product-price">
                                            <?php if (!empty($related['discount_price'])): ?>
                                                <span class="real-price">₨ <?= number_format($related['price'], 2) ?></span>
                                                <span class="discount-price">₨ <?= number_format($related['discount_price'], 2) ?></span>
                                            <?php else: ?>
                                                <span class="discount-price">₨ <?= number_format($related['price'], 2) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Buttons -->
                                        <div class="product-actions">
                                            <form method="POST" action="product-details.php?id=<?= $related['id'] ?>" style="margin:0;">
                                                <input type="hidden" name="add_to_cart" value="1">
                                                <input type="hidden" name="id" value="<?= $related['id'] ?>">
                                                <input type="hidden" name="title" value="<?= htmlspecialchars($related['title']) ?>">
                                                <input type="hidden" name="image" value="<?= htmlspecialchars($related['image'] ?? 'assets/images/default-product.png') ?>">
                                                <input type="hidden" name="price" value="<?= $related['discount_price'] ?: $related['price'] ?>">
                                                <button type="submit" class="btn btn-cart">Add to Cart</button>
                                            </form>
                                            <a href="product-details.php?id=<?= $related['id'] ?>" class="btn btn-details">View Details</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <h5 class="text-muted">No related products found!</h5>
                            <p class="text-secondary">Explore other categories for more options.</p>
                            <a href="shops.php" class="btn btn-dark rounded-pill">Shop Now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
        <!--====== End Related Products Section ======-->

        <!-- Newsletter -->
        <?php include 'include/newslatter.php' ?>
        <!-- Newsletter End -->
    </main>

    <!-- Footer -->
    <?php include 'include/footer.php' ?>
    <!-- Footer End -->

    <!-- Scripts -->
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/slick/slick.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script src="assets/js/wishlist.js"></script>
    <script>
    $(document).ready(function() {
    // Initialize Toasts
    $('.toast').toast({ delay: 3000 });
    <?php if (isset($_SESSION['message'])): ?>
        $('#success-toast').toast('show');
    <?php endif; ?>

    // Quantity Controls
    $('.quantity-down').click(function() {
        let input = $(this).siblings('.quantity');
        let value = parseInt(input.val()) || 1;
        input.val(Math.max(1, value - 1));
    });
    $('.quantity-up').click(function() {
        let input = $(this).siblings('.quantity');
        let value = parseInt(input.val()) || 1;
        input.val(value + 1);
    });

    // Validate Quantity Input
    $('.quantity').on('input', function() {
        let value = parseInt($(this).val());
        if (isNaN(value) || value < 1) $(this).val(1);
    });

    // Enable Add to Cart Button only when color & size selected
    const addToCartBtn = $('#add-to-cart-btn');
    $('input[name="color"], input[name="size"]').change(function() {
        addToCartBtn.prop('disabled', $('input[name="color"]:checked').length === 0 || $('input[name="size"]:checked').length === 0);
    });
    addToCartBtn.prop('disabled', $('input[name="color"]:checked').length === 0 || $('input[name="size"]:checked').length === 0);

    // Form validation
    $('#product-form').submit(function(e) {
        if ($('input[name="color"]:checked').length === 0 || $('input[name="size"]:checked').length === 0) {
            e.preventDefault();
            $('#error-toast').toast('show');
        }
    });

    // Initialize AOS
    AOS.init();
});

// Thumbnail click function
function changeMainImage(src, el) {
    if (src && el) {
        document.getElementById('main-product-image').src = src;
        document.querySelectorAll('.thumb-img').forEach(img => img.classList.remove('active'));
        el.classList.add('active');
    } else {
        console.error('Invalid image source or element');
    }
}

    </script>

    <!-- CSS for Modern Product Page -->
    <style>
        .product-details-modern { background: #f8f9fa; }
        .main-img-zoom {
            transition: transform 0.5s ease-in-out;
            width: 100%;
            height: 500px;
            object-fit: cover;
        }
        .main-img-zoom:hover { transform: scale(1.2); }
        .thumb-img.active { border-color: #000 !important; }
        .color-circle {
            width: 28px; height: 28px; border-radius: 50%;
            border: 2px solid #ddd; cursor: pointer; display: inline-block;
        }
        .color-option input:checked + .color-circle { border-color: #000; box-shadow: 0 0 0 2px #fff inset; }
        .size-option span {
            display: inline-block; padding: 6px 14px;
            border: 1px solid #ddd; border-radius: 6px; cursor: pointer;
            transition: 0.2s;
        }
        .size-option input:checked + span, .size-option span:hover {
            background: #000; color: #fff;
        }
        .quantity-input-modern {
            display: flex; align-items: center;
            border: 1px solid #ddd; border-radius: 8px; overflow: hidden;
        }
        .quantity-input-modern button {
            background: #f1f1f1; border: none;
            width: 36px; height: 36px;
            transition: background 0.2s;
        }
        .quantity-input-modern button:hover { background: #e0e0e0; }
        .quantity-input-modern input {
            width: 50px; text-align: center; border: none; outline: none;
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .wishlist-added {
            color: #ff0000;
            border-color: #ff0000 !important;
        }
        @media (max-width: 576px) {
            .thumb-img { width: 60px; height: 60px; }
            .main-img-zoom { height: 300px; }
        }
    </style>
</body>
</html>