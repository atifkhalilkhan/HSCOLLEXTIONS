<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';
include 'include/cart-functions.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ------------------------
// 1. Add to Cart Handler
// ------------------------
if (isset($_POST['product_id'])) {
    $item_id = (int)$_POST['product_id'];
    
    // Check if already in cart (same product + color + size)
    $found = false;
    foreach ($_SESSION['cart'] as &$cart_item) {
        if (
            $cart_item['id'] === $item_id &&
            $cart_item['color'] === ($_POST['color'] ?? '') &&
            $cart_item['size'] === ($_POST['size'] ?? '')
        ) {
            // Already in cart, increment quantity
            $cart_item['quantity'] += (int)($_POST['quantity'] ?? 1);
            $found = true;
            break;
        }
    }
    unset($cart_item);

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $item_id,
            'title' => $_POST['title'],
            'image' => $_POST['image'],
            'price' => $_POST['price'],
            'color' => $_POST['color'] ?? '',
            'size' => $_POST['size'] ?? '',
            'quantity' => (int)($_POST['quantity'] ?? 1)
        ];
    }

    header("Location: cart.php");
    exit;
}

// ------------------------
// 2. Remove Item
// ------------------------
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

// ------------------------
// 3. Update Quantity
// ------------------------
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $key => $quantity) {
        if (isset($_SESSION['cart'][$key])) {
            $_SESSION['cart'][$key]['quantity'] = (int)$quantity > 0 ? (int)$quantity : 1;
        }
    }
    header("Location: cart.php");
    exit;
}

// ------------------------
// 4. Clear Cart
// ------------------------
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
    <link rel="stylesheet" href="include/custom.css">
</head>
<body>
        <!--====== Preloader ======-->
    <div class="preloader">
        <div class="loader">
            <img src="assets/images/loader.gif" alt="Loader">
        </div>
    </div>
    <!--======  Start Overlay  ======-->
     <?php include 'include/sidecart.php'?>
     <?php include 'include/wishlistcart.php'?>
   

     <!--====== Start Header Section ======-->
    <header class="header-area">
        <?php include 'include/header.php'; ?>
        <?php include 'include/nav.php'; ?>
    </header><!--====== End Header Section ======-->
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
                            <h4 style="color: #de3576;" class="title">
                                <a href="product-details.php?id=<?php echo $item['id']; ?>">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </a>
                            </h4>
                            <div class="product-meta">
                                <span><?php echo !empty($item['color']) ? htmlspecialchars($item['color']) : ''; ?></span>
                                <span><?php echo !empty($item['size']) ? htmlspecialchars($item['size']) : ''; ?></span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="price"><span class="currency">₨</span><?php echo number_format($item['price'], 0); ?></div>
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
                    <div class="total-price"><span class="currency">₨</span><?php echo number_format($item['price'] * $item['quantity'], 0); ?></div>
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
                                            <button type="submit" class="theme-btn style-one">Update Cart</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4">
    <div class="cart-sidebar-area">
        <div class="cart-widget cart-total-widget mb-40" data-aos="fade-up" data-aos-duration="1400">
            
            <div class="shipping-cart">
                <h4>Shipping</h4>
                <p>Cash on Delivery available all over Pakistan
                    </p>
                    <p>₨:250 for Karachi | ₨:300 for other cities</p>
            </div>
            
            <h4>Cart Totals</h4>
            <div class="price-total">
                <h5>
                    Price 
                    <span class="price">
                        ₨<?php echo number_format(array_sum(array_map(function($item) { 
                            return $item['price'] * $item['quantity']; 
                        }, $_SESSION['cart'])), 0); ?>
                    </span>
                </h5>
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
        <?php include 'include/footer.php' ?>
    </footer>
    <script src="assets/js/wishlist.js"></script>
   <script>
$(document).ready(function() {

    // 🩵 Fix: Prevent duplicate click events
    $('.quantity-down').off('click').on('click', function() {
        let input = $(this).siblings('.quantity');
        let value = parseInt(input.val());
        if (value > 1) input.val(value - 1);
    });

    $('.quantity-up').off('click').on('click', function() {
        let input = $(this).siblings('.quantity');
        let value = parseInt(input.val());
        input.val(value + 1);
    });

    // 🩵 Cart open/close functionality (no change)
    $('.sidemenu-cart-close').off('click').on('click', function() {
        $('.sidemenu-wrapper-cart').removeClass('open');
        $('.offcanvas__overlay').removeClass('open');
    });

    $('.cart-button').off('click').on('click', function() {
        $('.sidemenu-wrapper-cart').toggleClass('open');
        $('.offcanvas__overlay').toggleClass('open');
    });

});
</script>

</body>
</html>