<?php
session_start();
include 'config.php';

// Initialize sessions
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

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
    header("Location: wishlist.php");
    exit;
}

// === HANDLE ADD TO CART FROM WISHLIST ===
if (isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['id'];
    $cart_item = [
        'id' => $product_id,
        'title' => $_POST['title'],
        'image' => $_POST['image'],
        'price' => (float)$_POST['price'],
        'quantity' => 1
    ];
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $cart_item['id']) {
            $item['quantity'] += 1;
            $found = true;
            break;
        }
    }
    if (!$found) {
        $_SESSION['cart'][] = $cart_item;
    }
    header("Location: wishlist.php");
    exit;
}

// Fetch product details for stock status
function get_product_stock($conn, $product_id) {
    $sql = "SELECT stock FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['stock'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="eCommerce,shop,fashion">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Wishlist - Pesco</title>
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
        .wishlist-page-section {
            padding-top: 120px;
            padding-bottom: 80px;
        }

        .cart-list.table-responsive {
            overflow-x: auto;
        }

        .cart-list table {
            width: 100%;
            border-collapse: collapse;
        }

        .cart-list th {
            background: #fafafa;
            color: #333;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #eee;
        }

        .cart-list th i {
            color: #de3576;
            margin-right: 5px;
        }

        .cart-list td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }

        .product-thumb-item {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .product-img img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }

        .product-info .title {
            font-size: 18px;
            color: #de3576;
            text-decoration: none;
        }

        .product-info .title:hover {
            text-decoration: underline;
        }

        .price {
            font-weight: 600;
            color: #333;
        }

        .price .currency {
            font-size: 14px;
        }

        .product-stock {
            color: #28a745;
            font-weight: 500;
        }

        .product-stock.out-of-stock {
            color: #dc3545;
        }

        .action-cart {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-cart .theme-btn {
            background-color: #de3576;
            color: #fff;
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
        }

        .action-cart .theme-btn:hover {
            background-color: #c02c65;
        }

        .action-cart .remove-cart {
            color: #dc3545;
            font-size: 18px;
            cursor: pointer;
        }

        .action-cart .remove-cart:hover {
            color: #c82333;
        }

        .empty-wishlist {
            text-align: center;
            padding: 50px 0;
        }

        .empty-wishlist h3 {
            color: #333;
            margin-bottom: 20px;
        }

        .empty-wishlist a {
            background-color: #de3576;
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }

        .empty-wishlist a:hover {
            background-color: #c02c65;
        }

        @media (max-width: 767px) {
            .cart-list th, .cart-list td {
                padding: 10px;
            }
            .product-img img {
                width: 60px;
                height: 60px;
            }
            .product-info .title {
                font-size: 16px;
            }
            .action-cart .theme-btn {
                padding: 6px 10px;
                font-size: 12px;
            }
        }
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
    <!-- Sidebar Cart -->
    <div class="sidemenu-wrapper-cart">
        <div class="sidemenu-content">
            <h4>My Cart</h4>
            <div class="sidemenu-cart-close"><i class="far fa-times"></i></div>
            <ul class="pesco-mini-cart-list">
                <?php if (empty($_SESSION['cart'])): ?>
                    <li>Your cart is empty</li>
                <?php else: ?>
                    <?php foreach ($_SESSION['cart'] as $item): ?>
                        <li class="sidebar-cart-item">
                            <a href="cart.php?remove=<?= $item['id'] ?>" class="remove-cart"><i class="far fa-trash-alt"></i></a>
                            <a href="product-details.php?id=<?= $item['id'] ?>">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="cart image">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                            <span class="quantity"><?= $item['quantity'] ?> × <span class="currency"><?= number_format($item['price'], 2) ?></span></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="cart-mini-total">
                <strong>Subtotal:</strong>
                <span class="currency"><?= number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $_SESSION['cart'])), 2) ?></span>
            </div>
            <div class="cart-button-box">
                <a href="cart.php" class="theme-btn style-one">View Cart</a>
                <a href="checkout.php" class="theme-btn style-one">Checkout</a>
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
                            <a href="wishlist.php?remove_wishlist=<?= $item['id'] ?>" class="remove-wishlist"><i class="far fa-trash-alt"></i></a>
                            <a href="product-details.php?id=<?= $item['id'] ?>">
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="wishlist image">
                                <?= htmlspecialchars($item['title']) ?>
                            </a>
                            <span class="price currency"><?= number_format($item['price'], 2) ?></span>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="wishlist-button-box">
                <a href="wishlist.php" class="theme-btn style-one">View Wishlist</a>
            </div>
        </div>
    </div>
    <!-- Header -->
    <header class="header-area">
        <div class="header-top">
            <div class="site-branding">
                <a href="index.html"><img src="assets/images/logo.png" alt="Logo"></a>
            </div>
            <div class="search-block">
                <form method="GET" action="shops.php">
                    <input type="text" name="search" placeholder="Search products...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="nav-icons nav-right-item">
                <ul>
                    <li>
                        <a href="javascript:void(0)" class="wishlist-btn">
                            <i class="far fa-heart"></i>
                            <span class="pro-count wishlist-count"><?= count($_SESSION['wishlist']) ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" class="cart-button">
                            <i class="fa fa-shopping-cart"></i>
                            <span class="pro-count cart-count"><?= count($_SESSION['cart']) ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php include 'include/nav.php'; ?>
    </header>
    <!-- Page Banner -->
    <section class="page-banner">
        <div class="page-banner-wrapper p-r z-1">
            <svg class="lineanm" viewBox="0 0 1920 347" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="line" d="M-39 345.187C70 308.353 397.628 293.477 436 145.186C490 -63.5 572 -57.8156 688 255.186C757.071 441.559 989.5 -121.315 1389 98.6856C1708.6 274.686 1940.33 156.519 1964.5 98.6856" stroke="white" stroke-width="3" stroke-dasharray="2 2"/>
            </svg>
            <div class="page-image"><img src="assets/images/products/unstiched/product-banner.jpg" alt="image"></div>
            <svg class="page-svg" xmlns="http://www.w3.org/2000/svg">
                <path d="M21.1742 33.0065C14.029 35.2507 7.5486 39.0636 0 40.7339V86H1937V64.9942C1933.1 60.1623 1912.65 65.1777 1904.51 62.6581C1894.22 59.4678 1884.93 55.0079 1873.77 52.7742C1861.2 50.2585 1823.41 36.3854 1811.99 39.9252C1805.05 42.0727 1796.94 37.6189 1789.36 36.6007C1769.18 33.8879 1747.19 31.1848 1726.71 29.7718C1703.81 28.1919 1678.28 27.0012 1657.53 34.4442C1636.45 42.005 1606.07 60.856 1579.5 55.9191C1561.6 52.5906 1543.41 47.0959 1528.45 56.9075C1510.85 68.4592 1485.74 74.2518 1460.44 76.136C1432.32 78.2297 1408.53 70.6879 1384.73 62.2987C1339.52 46.361 1298.19 27.1677 1255.08 9.28534C1242.58 4.10111 1214.68 15.4762 1200.55 16.6533C1189.77 17.5509 1181.74 15.4508 1172.12 12.8795C1152.74 7.70033 1133.23 2.88525 1111.79 2.63621C1088.85 2.36971 1073.94 7.88289 1056.53 15.8446C1040.01 23.3996 1027.48 26.1777 1007.8 26.1777C993.757 26.1777 975.854 25.6887 962.844 28.9632C941.935 34.2258 932.059 38.7874 914.839 28.6037C901.654 20.8061 866.261 -2.56499 844.356 7.12886C831.264 12.9222 820.932 21.5146 807.663 27.5255C798.74 31.5679 779.299 42.0561 766.33 39.1166C758.156 37.2637 751.815 31.6349 745.591 28.2443C730.967 20.2774 715.218 13.2948 695.846 10.723C676.168 8.11038 658.554 23.1787 641.606 27.4357C617.564 33.4742 602.283 27.7951 579.244 27.7951C568.142 27.7951 548.414 30.4002 541.681 23.6618C535.297 17.2722 530.162 9.74921 523.263 3.71444C517.855 -1.01577 505.798 -0.852017 498.318 2.09709C479.032 9.7007 453.07 10.0516 431.025 9.64475C407.556 9.21163 368.679 1.61612 346.618 10.3636C319.648 21.0575 291.717 53.8338 254.67 45.2266C236.134 40.9201 225.134 37.5813 204.78 40.7339C186.008 43.6415 171.665 50.7785 156.051 57.3567C146.567 61.3523 152.335 52.6281 151.12 47.9222C149.535 41.7853 139.994 34.5585 132.991 30.4008C120.206 22.8098 90.2848 24.3246 74.2546 24.6502C55.5552 25.0301 37.9201 27.747 21.1742 33.0065Z" fill="#FFFAF3"/>
            </svg>
            <div class="shape shape-one"><span></span></div>
            <div class="shape shape-two"><span></span></div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="page-banner-content">
                            <h1>Wishlist</h1>
                            <ul class="breadcrumb-link">
                                <li><a href="index.html" style="color: #de3576;">Home</a></li>
                                <li><i class="far fa-long-arrow-right"></i></li>
                                <li class="active">Wishlist</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Wishlist Section -->
    <section class="wishlist-page-section pt-120">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="cart-wrapper" data-aos="fade-up" data-aos-delay="20" data-aos-duration="1000">
                        <div class="cart-list table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-tshirt"></i>Product Details</th>
                                        <th><i class="fas fa-sack-dollar"></i>Unit Price</th>
                                        <th><i class="fas fa-eye"></i>Stock Status</th>
                                        <th><i class="fas fa-rocket-launch"></i>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($_SESSION['wishlist'])): ?>
                                        <tr>
                                            <td colspan="4">
                                                <div class="empty-wishlist">
                                                    <h3>Your wishlist is empty!</h3>
                                                    <a href="shops.php" class="theme-btn style-one">Continue Shopping</a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($_SESSION['wishlist'] as $item): ?>
                                            <?php $stock = get_product_stock($conn, $item['id']); ?>
                                            <tr>
                                                <td>
                                                    <div class="product-thumb-item">
                                                        <div class="product-img">
                                                            <a href="<?= htmlspecialchars($item['image']) ?>" class="img-popup">
                                                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="Product Thumbnail">
                                                            </a>
                                                        </div>
                                                        <div class="product-info">
                                                            <h4 class="title"><a href="product-details.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['title']) ?></a></h4>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="price"><span class="currency">$</span><?= number_format($item['price'], 2) ?></div>
                                                </td>
                                                <td>
                                                    <div class="product-stock <?= $stock > 0 ? '' : 'out-of-stock' ?>">
                                                        <?= $stock > 0 ? "$stock in stock" : "Out of stock" ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="action-cart">
                                                        <a href="product-details.php?id=<?= $item['id'] ?>" class="theme-btn style-one">View Product</a>
                                                        <form method="POST" action="wishlist.php">
                                                            <input type="hidden" name="add_to_cart" value="1">
                                                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                                            <input type="hidden" name="title" value="<?= htmlspecialchars($item['title']) ?>">
                                                            <input type="hidden" name="image" value="<?= htmlspecialchars($item['image']) ?>">
                                                            <input type="hidden" name="price" value="<?= $item['price'] ?>">
                                                            <button type="submit" class="theme-btn style-one" <?= $stock == 0 ? 'disabled' : '' ?>>Add to Cart</button>
                                                        </form>
                                                        <a href="wishlist.php?remove_wishlist=<?= $item['id'] ?>" class="remove-cart"><i class="far fa-times"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
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
    <script src="assets/vendor/jquery/jquery.min.js"></script>
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/vendor/slick/slick.min.js"></script>
    <script src="assets/vendor/nice-select/js/jquery.nice-select.min.js"></script>
    <script src="assets/vendor/magnific-popup/dist/jquery.magnific-popup.min.js"></script>
    <script src="assets/vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="assets/vendor/aos/aos.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Magnific Popup for product images
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

            // Update wishlist count after removal
            $('.remove-wishlist, .remove-cart').click(function(e) {
                $('.sidemenu-wrapper-wishlist').addClass('open');
                $('.offcanvas__overlay').addClass('open');
            });
        });
    </script>
</body>
</html>