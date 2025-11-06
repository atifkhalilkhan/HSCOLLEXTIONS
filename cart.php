<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Remove Item
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $remove_id) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
    header("Location: cart.php");
    exit;
}

// Handle Update Quantity
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $key => $quantity) {
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] = (int)$quantity > 0 ? (int)$quantity : 1;
        }
    }
    header("Location: cart.php");
    exit;
}

// Handle Clear Cart
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="eCommerce,shop,fashion">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Cart - Pesco</title>
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
        .cart-button.active::after {
            content: '<?= count($_SESSION['cart']) ?>';
            position: absolute;
            top: -10px;
            right: -10px;
            background: #ff4d4d;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
        }
        .nav-search { position: relative; }
        .nav-search input { width: 100%; padding: 10px; border-radius: 4px; border: 1px solid #ddd; }
        .nav-search button { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; }
    </style>
</head>
<body>
    <div class="preloader">
        <div class="loader">
            <img src="assets/images/loader.gif" alt="Loader">
        </div>
    </div>
    <div class="offcanvas__overlay"></div>
    <div class="sidemenu-wrapper-cart">
        <div class="sidemenu-content">
            <div class="widget widget-shopping-cart">
                <h4>My Cart</h4>
                <div class="sidemenu-cart-close"><i class="far fa-times"></i></div>
                <div class="widget-shopping-cart-content">
                    <ul class="pesco-mini-cart-list">
                        <?php if (empty($_SESSION['cart'])): ?>
                            <li>Your cart is empty</li>
                        <?php else: ?>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                                <li class="sidebar-cart-item">
                                    <a href="cart.php?remove=<?php echo $item['id']; ?>" class="remove-cart"><i class="far fa-trash-alt"></i></a>
                                    <a href="product-details.php?id=<?php echo $item['id']; ?>">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="cart image">
                                        <?php echo htmlspecialchars($item['title']); ?>
                                    </a>
                                    <span class="quantity"><?php echo $item['quantity']; ?> × <span><span class="currency">₨</span><?php echo number_format($item['price'], 2); ?></span></span>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                    <div class="cart-mini-total">
                        <div class="cart-total">
                            <span><strong>Subtotal:</strong></span>
                            <span class="amount"><span class="currency">₨</span><?php echo number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $_SESSION['cart'])), 2); ?></span>
                        </div>
                    </div>
                    <div class="cart-button-box">
                        <a href="cart.php" class="theme-btn style-one">View Cart</a>
                        <a href="checkout.php" class="theme-btn style-one">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <header class="header-navigation style-one">
        <div class="container">
            <div class="primary-menu">
                <div class="site-branding d-lg-none d-block">
                    <a href="index.html" class="brand-logo"><img src="assets/images/logo/logo.jpg" alt="Logo"></a>
                </div>
                <div class="nav-inner-menu">
                    <div class="main-categories-wrap d-none d-lg-block">
                        <a class="categories-btn-active" href="#">
                            <span class="fas fa-list"></span>
                            <span class="text">Product Categories <i class="fas fa-angle-down"></i></span>
                        </a>
                        <div class="categories-dropdown-wrap categories-dropdown-active">
                            <div class="categori-dropdown-item">
                                <ul>
                                    <li><a href="shops.php?category=1"><img src="assets/images/icon/unsuited.png" alt="Unstitched">Unstitched Suits</a></li>
                                    <li><a href="shops.php?category=2"><img src="assets/images/icon/suited.png" alt="Stitched">Stitched Suite</a></li>
                                    <li><a href="shops.php?category=3"><img src="assets/images/icon/chapal.png" alt="Footwear">Casual Slippers</a></li>
                                    <li><a href="shops.php?category=4"><img src="assets/images/icon/foot.png" alt="Fancy Footwear">Fancy Footwear</a></li>
                                    <li><a href="shops.php?category=5"><img src="assets/images/icon/parras.png" alt="Luxury">Handbags</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="pesco-nav-main">
                        <div class="pesco-nav-menu">
                            <div class="nav-search mb-40">
                                <div class="form-group">
                                    <form action="shops.php" method="GET">
                                        <input type="search" class="form_control" placeholder="Search Here" name="search">
                                        <button class="search-btn"><i class="far fa-search"></i></button>
                                    </form>
                                </div>
                            </div>
                            <!-- Rest of the navigation menu remains the same -->
                            <div class="pesco-tabs style-three d-block d-lg-none">
                                <!-- Keep existing tab content -->
                            </div>
                            <div class="hotline-support d-flex d-lg-none mt-30">
                                <div class="icon">
                                    <i class="flaticon-support"></i>
                                </div>
                                <div class="info">
                                    <span>24/7 Support</span>
                                    <h5><a href="tel:+923462744165">+923462744165</a></h5>
                                </div>
                            </div>
                            <nav class="main-menu d-none d-lg-block">
                                <ul>
                                    <li><a href="index.html">Home</a></li>
                                    <li><a href="about-us.html">About Us</a></li>
                                    <li class="menu-item has-children"><a href="#">Products</a>
                                        <ul class="sub-menu">
                                            <li><a href="shops.php">All Products</a></li>
                                            <li><a href="shops.php?category=1">Unstitched Collection</a></li>
                                            <li><a href="shops.php?category=2">Stitched Collection</a></li>
                                            <li><a href="shops.php?category=3">Footwear</a></li>
                                            <li><a href="shops.php?category=5">Handbags</a></li>
                                            <li><a href="shops.php?category=3">Casual Slippers</a></li>
                                            <li><a href="cart.php">Cart</a></li>
                                            <li><a href="checkout.php">Checkout</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="faq.html">FAQs</a></li>
                                    <li><a href="contact.html">Contact</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="nav-right-item style-one">
                    <ul>
                        <li>
                            <div class="deals d-lg-block d-none"><i class="far fa-fire-alt"></i>Deal</div>
                        </li>
                        <li>
                            <div class="wishlist-btn d-lg-block d-none"><i class="far fa-heart"></i><span class="pro-count"><?php echo count($_SESSION['wishlist']); ?></span></div>
                        </li>
                        <li>
                            <div class="cart-button d-flex align-items-center">
                                <div class="icon">
                                    <i class="fas fa-shopping-bag"></i><span class="pro-count"><?php echo count($_SESSION['cart']); ?></span>
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
    </header>
    <main class="main-bg">
        <section class="page-banner">
            <div class="page-banner-wrapper p-r z-1">
                <svg class="lineanm" viewBox="0 0 1920 347" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path class="line" d="M-39 345.187C70 308.353 397.628 293.477 436 145.186C490 -63.5 572 -57.8156 688 255.186C757.071 441.559 989.5 -121.315 1389 98.6856C1708.6 274.686 1940.33 156.519 1964.5 98.6856" stroke="white" stroke-width="3" stroke-dasharray="2 2"/>
                </svg>
                <div class="page-image"><img src="assets/images/bg/page-img-1.png" alt="image"></div>
                <svg class="page-svg" xmlns="http://www.w3.org/2000/svg">
                    <!-- Keep existing SVG -->
                </svg>
                <div class="shape shape-one"><span></span></div>
                <div class="shape shape-two"><span></span></div>
                <div class="shape shape-three"><span><img src="assets/images/shape/curved-arrow.png" alt=""></span></div>
                <div class="shape shape-four"><span><img src="assets/images/shape/stars.png" alt=""></span></div>
                <div class="container">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="page-banner-content">
                                <h1>Cart Page</h1>
                                <ul class="breadcrumb-link">
                                    <li><a href="index.html">Home</a></li>
                                    <li><i class="far fa-long-arrow-right"></i></li>
                                    <li class="active">Cart</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="cart-page-section pt-120 pb-80">
            <div class="container">
                <div class="row">
                    <div class="col-xl-8">
                        <div class="cart-wrapper mb-40" data-aos="fade-up" data-aos-duration="1200">
                            <h3 class="mb-20">Total Cart Items: <?php echo count($_SESSION['cart']); ?></h3>
                            <div class="cart-list table-responsive">
                                <form method="POST">
                                    <input type="hidden" name="update_cart" value="1">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><i class="fas fa-tshirt"></i>Products Details</th>
                                                <th><i class="fas fa-sack-dollar"></i>Price</th>
                                                <th style="text-align: center;"><i class="fas fa-eye"></i>Quantity</th>
                                                <th style="text-align: right;"><i class="fas fa-money-bill"></i>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (empty($_SESSION['cart'])): ?>
                                                <tr><td colspan="4">Your cart is empty</td></tr>
                                            <?php else: ?>
                                                <?php foreach ($_SESSION['cart'] as $key => $item): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="product-thumb-item">
                                                                <div class="product-img">
                                                                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="Product Thumbnail">
                                                                </div>
                                                                <div class="product-info">
                                                                    <h4 class="title"><a href="product-details.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['title']); ?></a></h4>
                                                                    <div class="product-meta">
                                                                        <span><?php echo htmlspecialchars($item['color']); ?></span>
                                                                        <span><?php echo htmlspecialchars($item['size']); ?></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="price"><span class="currency">₨</span><?php echo number_format($item['price'], 2); ?></div>
                                                        </td>
                                                        <td>
                                                            <div class="action-cart">
                                                                <div class="quantity-input">
                                                                    <button type="button" class="quantity-down"><i class="far fa-minus"></i></button>
                                                                    <input class="quantity" type="text" value="<?php echo $item['quantity']; ?>" name="quantity[<?php echo $key; ?>]">
                                                                    <button type="button" class="quantity-up"><i class="far fa-plus"></i></button>
                                                                </div>
                                                                <a href="cart.php?remove=<?php echo $item['id']; ?>" class="cart-remove"><i class="far fa-times"></i></a>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="total-price"><span class="currency">₨</span><?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                    <div class="cart-bottom d-flex align-items-center justify-content-between mt-40">
                                        <div class="ct-shopping">
                                            <a href="shops.php" class="theme-btn style-one">Continue Shopping</a>
                                        </div>
                                        <div class="cl-cart">
                                            <button type="submit" name="clear_cart" value="1" class="theme-btn style-one">Clear Cart</button>
                                            <button type="submit" class="theme-btn style-one">Update Cart</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
                        <div class="cart-sidebar-area">
                            <div class="cart-widget coupon-box-widget mb-40" data-aos="fade-up" data-aos-duration="1200">
                                <h4>Use Coupon Code</h4>
                                <p>Enter your coupon code if you have one.</p>
                                <form>
                                    <input type="text" class="form_control" required>
                                    <button class="theme-btn style-one">Apply</button>
                                </form>
                            </div>
                            <div class="cart-widget cart-total-widget mb-40" data-aos="fade-up" data-aos-duration="1400">
                                <h4>Cart Totals</h4>
                                <div class="sub-total">
                                    <h5>Subtotal <span class="price">₨<?php echo number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $_SESSION['cart'])), 2); ?></span></h5>
                                </div>
                                <div class="shipping-cart">
                                    <h4>Shipping</h4>
                                    <div class="single-radio">
                                        <input class="form-check-input" type="radio" name="radio" checked value="Free" id="radio1">
                                        <label class="form-check-label" for="radio1">
                                            Free Delivery <span class="price">₨0.00</span>
                                        </label>
                                    </div>
                                    <div class="single-radio">
                                        <input class="form-check-input" type="radio" name="radio" value="Flat" id="radio2">
                                        <label class="form-check-label" for="radio2">
                                            Flat Rate <span class="price">₨500.00</span>
                                        </label>
                                    </div>
                                    <div class="single-radio">
                                        <input class="form-check-input" type="radio" name="radio" value="Local" id="radio3">
                                        <label class="form-check-label" for="radio3">
                                            Local Area <span class="price">₨300.00</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="price-total">
                                    <h5>Total <span class="price">₨<?php echo number_format(array_sum(array_map(function($item) { return $item['price'] * $item['quantity']; }, $_SESSION['cart'])), 2); ?></span></h5>
                                </div>
                                <div class="proceced-checkout">
                                    <a href="checkout.php" class="theme-btn style-one">Proceed to Checkout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="newsletter-section pb-95">
            <!-- Keep existing newsletter section -->
        </section>
    </main>
    <footer class="footer-main">
        <!-- Keep existing footer -->
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
            $('.sidemenu-cart-close').click(function() {
                $('.sidemenu-wrapper-cart').removeClass('open');
                $('.offcanvas__overlay').removeClass('open');
            });
            $('.cart-button').click(function() {
                $('.sidemenu-wrapper-cart').toggleClass('open');
                $('.offcanvas__overlay').toggleClass('open');
            });
        });
    </script>
</body>
</html>