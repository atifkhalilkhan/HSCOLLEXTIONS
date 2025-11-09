<?php
session_start();
include 'include/cart-functions.php';
include 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Collect form data
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $country = trim($_POST['country']);
    $city = trim($_POST['city']);
    $zip_code = trim($_POST['zip_code']);
    $address = trim($_POST['address']);
    
    // If payment_method not sent, default to Cash on Delivery
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : "Cash on Delivery";

    // Calculate subtotal
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    // Delivery charge based on city
    if (strtolower($city) === 'karachi') {
        $delivery_charge = 250;
    } elseif (strtolower($city) === 'other') {
        $delivery_charge = 350;
    } else {
        $delivery_charge = 300;
    }

    $total_amount = $subtotal + $delivery_charge;

    // --- Save order in database ---
    $stmt = $conn->prepare("INSERT INTO orders (full_name, email, phone, country, city, zip_code, address, payment_method, total_amount, delivery_charge) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }
    $stmt->bind_param("ssssssssdd", $full_name, $email, $phone, $country, $city, $zip_code, $address, $payment_method, $total_amount, $delivery_charge);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // --- Save items with color, size, total ---
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total, color, size) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt_item) {
        die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
    }

    foreach ($_SESSION['cart'] as $item) {
        $line_total = $item['price'] * $item['quantity'];
        $color = isset($item['color']) ? $item['color'] : '';
        $size = isset($item['size']) ? $item['size'] : '';
        $stmt_item->bind_param(
            "iisiddss",
            $order_id,
            $item['id'],
            $item['title'],
            $item['quantity'],
            $item['price'],
            $line_total,
            $color,
            $size
        );
        $stmt_item->execute();
    }

    $stmt_item->close();
    $stmt->close();

    // --- Email Section ---
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hscollextions@gmail.com'; 
        $mail->Password = 'apap pzdx ufqu edqr'; 
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('hscollextions@gmail.com', 'HS Collextions');
        $mail->addAddress($email, $full_name);
        $mail->addAddress('hscollextions@gmail.com', 'HS Collextions Admin');
        $mail->isHTML(true);
        $mail->Subject = "New Order #$order_id from $full_name";

        $body = "<h2>Order Confirmation</h2>
        <p><b>Name:</b> $full_name<br>
        <b>Email:</b> $email<br>
        <b>Phone:</b> $phone<br>
        <b>Address:</b> $address, $city ($zip_code), $country<br>
        <b>Payment Method:</b> $payment_method<br>
        <b>Delivery Charge:</b> Rs $delivery_charge<br>
        <b>Total:</b> Rs $total_amount</p>
        <h3>Order Details:</h3>
        <ul>";

        foreach ($_SESSION['cart'] as $item) {
            $line_total = number_format($item['price'] * $item['quantity'], 2);
            $color = isset($item['color']) ? $item['color'] : 'N/A';
            $size = isset($item['size']) ? $item['size'] : 'N/A';
            $body .= "<li>{$item['title']} (x{$item['quantity']}), Color: $color, Size: $size - Rs $line_total</li>";
        }

        $body .= "</ul><p><b>Thank you for shopping with HS Collections!</b></p>";

        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
    }

    // Clear cart and redirect
    $_SESSION['cart'] = [];
    header("Location: thankyou.php?order_id=$order_id");
    exit;
}
?>
 


<!DOCTYPE html>
<html lang="en">
    <head>
        <!--====== Required meta tags ======-->
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <meta name="description" content="eCommerce,shop,fashion">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <!--====== Title ======-->
        <title>Pesco - eCommerce HTML Template</title>
        <!--====== Favicon Icon ======-->
        <link rel="shortcut icon" href="assets/images/favicon.png" type="image/png">
        <!--====== Google Fonts ======-->
        <link href="https://fonts.googleapis.com/css2?family=Aoboshi+One&family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
        <!--====== Flaticon css ======-->
        <link rel="stylesheet" href="assets/fonts/flaticon/flaticon_pesco.css">
        <!--====== FontAwesome css ======-->
        <link rel="stylesheet" href="assets/fonts/fontawesome/css/all.min.css">
        <!--====== Bootstrap css ======-->
        <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
        <!--====== Slick-popup css ======-->
        <link rel="stylesheet" href="assets/vendor/slick/slick.css">
        <!--====== Nice Select css ======-->
        <link rel="stylesheet" href="assets/vendor/nice-select/css/nice-select.css">
        <!--====== Magnific-popup css ======-->
        <link rel="stylesheet" href="assets/vendor/magnific-popup/dist/magnific-popup.css">
        <!--====== Jquery UI css ======-->
        <link rel="stylesheet" href="assets/vendor/jquery-ui/jquery-ui.min.css">
        <!--====== Animate css ======-->
        <link rel="stylesheet" href="assets/vendor/aos/aos.css">
        <!--====== Default css ======-->
        <link rel="stylesheet" href="assets/css/default.css">
        <!--====== Style css ======-->
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
    <!--====== Start Overlay ======-->
    <div class="offcanvas__overlay"></div>
    <!--====== Start Sidemenu-wrapper-cart Area ======-->
     <!-- Overlay and Side Carts -->
     <div class="offcanvas__overlay"></div>
    <?php include 'include/sidecart.php'; ?>
    <?php include 'include/wishlistcart.php'; ?>
    
    <!-- Header Section -->
    <header class="header-area">
        <?php include 'include/header.php'; ?>
        <?php include 'include/nav.php'; ?>
    </header>
    <!--====== End Header Section ======-->


        <!--====== Main Bg  ======-->
        <main class="main-bg">
            <!--====== Start Page Banner  ======-->
            <section class="page-banner">
                <!--===  Page Banner Wrapper  ===-->
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
                                <!--===  Page Banner Content  ===-->
                                <div class="page-banner-content">
                                    <h1>Checkout</h1>
                                    <ul class="breadcrumb-link">
                                        <li><a href="index.html">Home</a></li>
                                        <li><i class="far fa-long-arrow-right"></i></li>
                                        <li class="active">Checkout</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section><!--====== End Page Banner  ======-->
<!-- === Checkout Page === -->
<!-- === Checkout Page === -->
<section class="checkout-section pt-120 pb-80">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="checkout-wrapper" data-aos="fade-up" data-aos-duration="1200">
                    <form class="checkout-form" method="POST" id="checkoutForm">
                        <div class="row">
                            <!-- Billing Details -->
                            <div class="col-xl-7">
                                <div class="billing-wrapper">
                                    <h3 class="title">Billing Details</h3>
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Full Name <span>*</span></label>
                                                <input type="text" class="form_control" name="full_name" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Phone Number <span>*</span></label>
                                                <input type="text" class="form_control" name="phone" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Email Address <span>*</span></label>
                                                <input type="email" class="form_control" name="email" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Country / Region<span>*</span></label>
                                                <select class="wide" name="country" required>
                                                    <option>Pakistan</option>
                                                    <option>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>City <span>*</span></label>
                                                <select class="wide" name="city" id="citySelect" required>
                                                    <option value="">Select City</option>
                                                    <option value="Karachi" selected>Karachi</option>
                                                    <option value="Lahore">Lahore</option>
                                                    <option value="Islamabad">Islamabad</option>
                                                    <option value="Faisalabad">Faisalabad</option>
                                                    <option value="Multan">Multan</option>
                                                    <option value="Hyderabad">Hyderabad</option>
                                                    <option value="Peshawar">Peshawar</option>
                                                    <option value="Quetta">Quetta</option>
                                                    <option value="Gujranwala">Gujranwala</option>
                                                    <option value="Sialkot">Sialkot</option>
                                                    <option value="Sukkur">Sukkur</option>
                                                    <option value="Bahawalpur">Bahawalpur</option>
                                                    <option value="Sargodha">Sargodha</option>
                                                    <option value="Rawalpindi">Rawalpindi</option>
                                                    <option value="Other">Other (Enter Manually)</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Zip Code <span>*</span></label>
                                                <input type="text" class="form_control" name="zip_code" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Shipping Address<span>*</span></label>
                                                <input type="text" class="form_control" name="address" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="col-xl-5">
                                <div class="order-summary-wrapper mb-30">
                                    <h3 class="title">Your Order</h3>
                                    <div class="order-list">
    <div class="list-item">
        <div class="item-title">Product</div>
        <div class="subtotal">Subtotal</div>
    </div>

    <?php if (!empty($_SESSION['cart'])): ?>
        <?php 
        $subtotal = 0; 
        foreach ($_SESSION['cart'] as $item): 
            $line_total = $item['price'] * $item['quantity'];
            $subtotal += $line_total;
            $color = isset($item['color']) ? $item['color'] : 'N/A';
            $size = isset($item['size']) ? $item['size'] : 'N/A';
        ?>
            <div class="product-item">
                <div class="product-name">
                    <?= htmlspecialchars($item['title']) ?> <br>
                    <small>Color: <?= htmlspecialchars($color) ?> | Size: <?= htmlspecialchars($size) ?> x<?= $item['quantity'] ?></small>
                </div>
                <div class="product-total">
                    Rs <?= number_format($line_total) ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <div class="list-item">
            <div class="subtotal">Subtotal</div>
            <div class="product-total" id="subtotal">Rs <?= number_format($subtotal) ?></div>
        </div>
        <div class="list-item">
            <div class="shipping">Shipping</div>
            <div class="shipping-total" id="shippingCost">Rs 250</div>
        </div>
        <div class="list-item">
            <div class="total">Total</div>
            <div class="product-total" id="totalAmount">Rs <?= number_format($subtotal + 250) ?></div>
        </div>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

                                </div>

                                <!-- Payment Info (COD only) -->
                                <div class="payment-method-wrapper">
                                    <h4 class="title mb-20">Cash on Delivery</h4>
                                    <button type="submit" class="theme-btn style-one">
    Place Order <i class="fas fa-arrow-right"></i>
</button>

                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- === Shipping and Total Update Script === -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const citySelect = document.getElementById('citySelect');
    const subtotalText = document.getElementById('subtotal').innerText.replace('Rs', '').trim();
    const subtotal = parseInt(subtotalText.replace(/,/g, '')) || 0;
    const shippingCost = document.getElementById('shippingCost');
    const totalAmount = document.getElementById('totalAmount');

    function updateShipping() {
        const city = citySelect.value.toLowerCase();
        let shipping = 0;

        if (city === 'karachi') {
            shipping = 250;
        } else if (city === 'other') {
            shipping = 350;
        } else if (city !== '') {
            shipping = 300;
        } else {
            shipping = 0;
        }

        if (shipping > 0) {
            shippingCost.innerText = 'Rs ' + shipping;
            totalAmount.innerText = 'Rs ' + (subtotal + shipping);
        } else {
            shippingCost.innerText = 'Select City';
            totalAmount.innerText = 'Rs ' + subtotal;
        }
    }

    // Update shipping instantly when city changes
    citySelect.addEventListener('change', updateShipping);

    // Set default Karachi shipping on load
    updateShipping();
});
</script>


        </main>
            <!--====== Start Footer Main  ======-->
      <?php include 'include/footer.php' ?>
    <!--====== End Footer Main ======-->
    
    <!--====== Main js ======-->
    <script src="assets/js/wishlist.js"></script>
    </body>
</html>